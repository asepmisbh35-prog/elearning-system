<?php

namespace App\Jobs;

use App\Models\ContentBlock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ConvertPdfToFlipbook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 600;

    public function __construct(public ContentBlock $block) {}

    public function handle(): void
    {
        $block = $this->block->fresh();

        if (! $block || $block->type !== 'pdf_flipbook') {
            return;
        }

        $block->update(['flipbook_status' => 'processing']);

        $disk = Storage::disk('public');
        $absolutePdfPath = $disk->path($block->file_path);

        if (! file_exists($absolutePdfPath)) {
            $this->markFailed($block, 'File PDF sumber tidak ditemukan di storage.');
            return;
        }

        $pageCount = ContentBlock::countPdfPagesViaGhostscript($absolutePdfPath);

        if ($pageCount < 1) {
            $this->markFailed($block, 'Ghostscript gagal membaca jumlah halaman PDF.');
            return;
        }

        $flipbookRelativeDir = 'materials/flipbook/' . $block->id;
        $flipbookAbsoluteDir = $disk->path($flipbookRelativeDir);

        if (! is_dir($flipbookAbsoluteDir)) {
            mkdir($flipbookAbsoluteDir, 0755, true);
        }

        try {
            $this->renderPagesToPng($absolutePdfPath, $flipbookAbsoluteDir, $pageCount);
            $this->convertPngsToWebp($flipbookAbsoluteDir, $pageCount);

            $block->update([
                'flipbook_status' => 'ready',
                'flipbook_path'   => $flipbookRelativeDir,
                'page_count'      => $pageCount,
            ]);
        } catch (Throwable $e) {
            Log::error('ConvertPdfToFlipbook gagal: ' . $e->getMessage(), [
                'content_block_id' => $block->id,
            ]);
            $this->markFailed($block, $e->getMessage());
        }
    }

    public function failed(?Throwable $exception): void
    {
        $block = $this->block->fresh();
        if ($block) {
            $this->markFailed($block, $exception?->getMessage() ?? 'Job gagal setelah beberapa percobaan.');
        }
    }

    private function renderPagesToPng(string $absolutePdfPath, string $outputDir, int $pageCount): void
    {
        $ghostscriptBin = 'gswin64c';
        $outputPattern = $outputDir . DIRECTORY_SEPARATOR . 'page-%d.png';

        // Pakai proc_open dengan array argumen, BUKAN exec() dengan string command.
        // Alasan: escapeshellarg() di Windows mengganti karakter '%' jadi spasi (proteksi
        // bawaan PHP terhadap ekspansi variable environment cmd.exe), yang merusak
        // placeholder '%d' milik Ghostscript. proc_open dengan array argumen melewati
        // shell parsing sama sekali, jadi '%d' terkirim utuh apa adanya.
        $process = proc_open(
            [$ghostscriptBin, '-q', '-dBATCH', '-dNOPAUSE', '-dNOSAFER', '-sDEVICE=png16m', '-r220', '-o', $outputPattern, $absolutePdfPath],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes
        );

        if (! is_resource($process)) {
            throw new \RuntimeException('Gagal menjalankan proses Ghostscript.');
        }

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $returnCode = proc_close($process);

        if ($returnCode !== 0) {
            throw new \RuntimeException('Ghostscript gagal render PNG: ' . trim($stdout . ' ' . $stderr));
        }

        for ($i = 1; $i <= $pageCount; $i++) {
            if (! file_exists($outputDir . DIRECTORY_SEPARATOR . "page-{$i}.png")) {
                throw new \RuntimeException("Halaman {$i} gagal di-render (file PNG tidak ditemukan).");
            }
        }
    }

    private function convertPngsToWebp(string $dir, int $pageCount): void
    {
        if (! function_exists('imagewebp')) {
            throw new \RuntimeException('Ekstensi GD di server tidak mendukung WebP (imagewebp tidak tersedia).');
        }

        for ($i = 1; $i <= $pageCount; $i++) {
            $pngPath = $dir . DIRECTORY_SEPARATOR . "page-{$i}.png";
            $webpPath = $dir . DIRECTORY_SEPARATOR . "page-{$i}.webp";

            $image = @imagecreatefrompng($pngPath);
            if (! $image) {
                throw new \RuntimeException("Gagal membuka PNG halaman {$i} untuk dikonversi.");
            }

            imagewebp($image, $webpPath, 92);
            imagedestroy($image);

            @unlink($pngPath);
        }
    }

    private function markFailed(ContentBlock $block, string $reason): void
    {
        $block->update(['flipbook_status' => '  failed']);
        Log::warning("Flipbook ContentBlock #{$block->id} gagal: {$reason}");
    }
}
