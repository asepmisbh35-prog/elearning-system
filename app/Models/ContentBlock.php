<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Student;

class ContentBlock extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'material_id',
        'type',
        'order',
        'content',
        'video_url',
        'video_embed_id',
        'video_platform',
        'file_path',
        'original_filename',
        'file_size',
        'page_count',
        'flipbook_status',
        'flipbook_path',
        'link_url',
        'link_title',
        'link_domain',
    ];

    // ── Relasi ────────────────────────────────────────

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    // ── Helper ────────────────────────────────────────

    public function isLockedFor(Student $student): bool
    {
        $previous = $this->material->contentBlocks()
            ->where('order', '<', $this->order)
            ->orderByDesc('order')
            ->first();

        if (! $previous) {
            return false;
        }

        $progress = $previous->progressFor($student);
        return ! ($progress && $progress->isCompleted());
    }

    /**
     * Ekstrak video embed ID dari URL YouTube/Vimeo.
     */
    public static function extractVideoInfo(string $url): array
    {
        // YouTube: youtube.com/watch?v=ID atau youtu.be/ID
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return ['platform' => 'youtube', 'embed_id' => $m[1]];
        }

        // Vimeo: vimeo.com/ID
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
            return ['platform' => 'vimeo', 'embed_id' => $m[1]];
        }

        return ['platform' => 'other', 'embed_id' => null];
    }

    /**
     * Ekstrak domain dari URL link.
     */
    public static function extractDomain(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST) ?? $url;
        return str_starts_with($host, 'www.') ? substr($host, 4) : $host;
    }

    /**
     * URL embed untuk ditampilkan di iframe.
     */
    public function getEmbedUrlAttribute(): ?string
    {
        return match ($this->video_platform) {
            'youtube' => "https://www.youtube.com/embed/{$this->video_embed_id}",
            'vimeo'   => "https://player.vimeo.com/video/{$this->video_embed_id}",
            default   => null,
        };
    }

    /**
     * URL file PDF / flipbook untuk ditampilkan.
     */
    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    /**
     * Daftar URL gambar webp hasil konversi flipbook.
     */
    public function getFlipbookPagesAttribute(): array
    {
        if (! $this->flipbook_path || $this->flipbook_status !== 'ready') {
            return [];
        }

        $pages = [];
        for ($i = 1; $i <= $this->page_count; $i++) {
            $pages[] = asset('storage/' . $this->flipbook_path . "/page-{$i}.webp");
        }

        return $pages;
    }

    public static function countPdfPagesViaGhostscript(string $absolutePdfPath): int
    {
        $ghostscriptBin = 'gswin64c';

        $psScript = sprintf(
            '(%s) (r) file runpdfbegin pdfpagecount = quit',
            str_replace('\\', '/', $absolutePdfPath)
        );

        $tmpScript = tempnam(sys_get_temp_dir(), 'gs_count_') . '.ps';
        file_put_contents($tmpScript, $psScript);

        $command = sprintf(
            '%s -q -dNODISPLAY -dNOSAFER -dBATCH %s 2>&1',
            escapeshellcmd($ghostscriptBin),
            escapeshellarg($tmpScript)
        );

        exec($command, $output, $returnCode);
        @unlink($tmpScript);

        $result = trim(implode('', $output));

        return ($returnCode === 0 && is_numeric($result)) ? (int) $result : 0;
    }

    public function progresses()
    {
        return $this->hasMany(MaterialProgress::class);
    }

    public function progressFor(Student $student): ?MaterialProgress
    {
        return $this->progresses()->where('student_id', $student->id)->first();
    }
}
