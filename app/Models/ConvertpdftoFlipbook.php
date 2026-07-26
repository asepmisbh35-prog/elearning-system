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
use RuntimeException;

class ConvertPdfToFlipbook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    // Kalau gswin64c tidak ke-detect lewat queue worker, ganti ke path absolut:
    // 'C:\Program Files\gs\gs10.01.2\bin\gswin64c.exe'
    private string $ghostscriptBin = 'gswin64c';

    public function __construct(
        public ContentBlock $block
    ) {}

    public function handle(): void
    {
        if (!$this->block->file_path || !Storage::disk('public')->exists($this->block->file_path)) {
            Log::error('ConvertPdfToFlipbook: source PDF tidak ditemukan', [
                'block_id' => $this->block->id,
                'file_path' => $this->block->file_path,
            ]);
            $this->block->update(['flipbook_status' => 'failed']);
            return;
        }

        $this->block->update(['flipbook_status' => 'processing']);

        $absoluteSourcePath = Storage::disk('public')->path($this->block->file_path);
        $outputDir = 'flipbooks/' . $this->block->id;
        $absoluteOutputDir = Storage::disk('public')->path($outputDir);

        if (!is_dir($absoluteOutputDir)) {
            Storage::disk('public')->makeDirectory($outputDir);
        }

        try {
            $pageCount = ContentBlock::countPdfPagesViaGhostscript($absoluteSourcePath);

            if ($pageCount < 1) {
                throw new RuntimeException('Jumlah halaman PDF terdeteksi 0, kemungkinan file rusak.');
            }

            $outputPattern = $absoluteOutputDir . DIRECTORY_SEPARATOR . 'page-%d.jpg';

            $command = sprintf(
                '%s -dNOPAUSE -dBATCH -sDEVICE=jpeg -r150 -dJPEGQ=85 -sOutputFile=%s %s 2>&1',
                escapeshellcmd($this->ghostscriptBin),
                escapeshellarg($outputPattern),
                escapeshellarg($absoluteSourcePath)
            );

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new RuntimeException('Ghostscript gagal (exit ' . $returnCode . '): ' . implode("\n", $output));
            }

            for ($page = 1; $page <= $pageCount; $page++) {
                if (!file_exists($absoluteOutputDir . DIRECTORY_SEPARATOR . "page-{$page}.jpg")) {
                    throw new RuntimeException("Halaman {$page} gagal di-generate, file tidak ditemukan.");
                }
            }

            $this->block->update([
                'flipbook_status' => 'ready',
                'flipbook_path'   => $outputDir,
                'page_count'      => $pageCount,
            ]);
        } catch (\Throwable $e) {
            Log::error('ConvertPdfToFlipbook gagal: ' . $e->getMessage(), [
                'block_id' => $this->block->id,
            ]);

            $this->block->update([
                'type'            => 'pdf_viewer',
                'flipbook_status' => 'failed',
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->block->update([
            'type'            => 'pdf_viewer',
            'flipbook_status' => 'failed',
        ]);
    }
}
