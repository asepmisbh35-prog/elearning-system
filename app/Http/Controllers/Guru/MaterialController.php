<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContentBlockRequest;
use App\Http\Requests\StoreMaterialRequest;
use App\Jobs\ConvertPdfToFlipbook;
use App\Models\ContentBlock;
use App\Models\Material;
use App\Models\Meeting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Notifications\NewMaterialPublished;
use App\Models\ClassEnrollment;


class MaterialController extends Controller
{
    // ── Materi CRUD ───────────────────────────────────

    /**
     * Daftar materi dalam satu meeting.
     */
    public function index(Meeting $meeting): View
    {
        $this->authorizeTeacher($meeting);

        $materials = $meeting->materials()
            ->withCount('progresses')
            ->orderBy('order')
            ->get();

        return view('guru.materials.index', compact('meeting', 'materials'));
    }

    /**
     * Form buat materi baru.
     */
    public function create(Meeting $meeting): View
    {
        $this->authorizeTeacher($meeting);

        $nextOrder = $meeting->materials()->max('order') + 1;

        return view('guru.materials.create', compact('meeting', 'nextOrder'));
    }

    /**
     * Simpan materi baru.
     */
    public function store(StoreMaterialRequest $request, Meeting $meeting): RedirectResponse
    {
        $this->authorizeTeacher($meeting);

        $meeting->materials()->create($request->validated());

        return redirect()
            ->route('guru.meetings.materials.index', $meeting)
            ->with('success', 'Materi berhasil dibuat.');
    }

    /**
     * Preview materi — tampilan yang mirip dengan yang dilihat siswa.
     */
    public function show(Meeting $meeting, Material $material): View
    {
        $this->authorizeTeacher($meeting);

        $material->load('contentBlocks', 'discussions.user', 'discussions.replies.user', 'meeting.schoolClass.teacher.user');

        return view('guru.materials.show', compact('meeting', 'material'));
    }

    /**
     * Halaman edit materi + daftar blok konten.
     */
    public function edit(Meeting $meeting, Material $material): View
    {
        $this->authorizeTeacher($meeting);

        $material->load('contentBlocks');

        return view('guru.materials.edit', compact('meeting', 'material'));
    }

    /**
     * Update data materi.
     */
    public function update(StoreMaterialRequest $request, Meeting $meeting, Material $material): RedirectResponse
    {
        $this->authorizeTeacher($meeting);

        $material->update($request->validated());

        if ($material->status === 'published' && $material->wasChanged('status')) {
            $studentUserIds = ClassEnrollment::where('school_class_id', $material->meeting->school_class_id)
                ->with('student.user')
                ->get()
                ->pluck('student.user')
                ->filter();

            \Illuminate\Support\Facades\Notification::send($studentUserIds, new NewMaterialPublished($material));
        }

        return redirect()
            ->route('guru.meetings.materials.edit', [$meeting, $material])
            ->with('success', 'Materi berhasil diperbarui.');
    }

    /**
     * Hapus materi (soft delete).
     */
    public function destroy(Meeting $meeting, Material $material): RedirectResponse
    {
        $this->authorizeTeacher($meeting);

        $material->delete();

        return redirect()
            ->route('guru.meetings.materials.index', $meeting)
            ->with('success', 'Materi berhasil dihapus.');
    }

    // ── Blok Konten CRUD ──────────────────────────────

    /**
     * Tambah blok konten ke materi.
     */
    public function storeBlock(StoreContentBlockRequest $request, Meeting $meeting, Material $material): RedirectResponse
    {
        $this->authorizeTeacher($meeting);

        $data = [
            'material_id' => $material->id,
            'type'        => $request->type,
            'order'       => $request->order,
        ];

        switch ($request->type) {
            case 'text':
                $data['content'] = $request->content;
                break;

            case 'video':
                $info = ContentBlock::extractVideoInfo($request->video_url);
                $data['video_url']      = $request->video_url;
                $data['video_platform'] = $info['platform'];
                $data['video_embed_id'] = $info['embed_id'];
                break;

            case 'pdf_viewer':
                $path = $request->file('file')->store('materials/pdf', 'public');
                $data['file_path']         = $path;
                $data['original_filename'] = $request->file('file')->getClientOriginalName();
                $data['file_size']         = $request->file('file')->getSize();
                break;

            case 'pdf_flipbook':
                // Validasi jumlah halaman PDF (maks 100) — pakai Ghostscript, bukan Imagick
                $tmpPath   = $request->file('file')->getRealPath();
                $pageCount = ContentBlock::countPdfPagesViaGhostscript($tmpPath);

                if ($pageCount < 1) {
                    return back()->withErrors(['file' => 'File PDF tidak valid atau rusak.']);
                }
                if ($pageCount > 100) {
                    return back()->withErrors(['file' => 'PDF flipbook maksimal 100 halaman.']);
                }

                $path = $request->file('file')->store('materials/flipbook-source', 'public');
                $data['file_path']         = $path;
                $data['original_filename'] = $request->file('file')->getClientOriginalName();
                $data['file_size']         = $request->file('file')->getSize();
                $data['flipbook_status']   = 'pending';
                break;

            case 'link':
                $data['link_url']    = $request->link_url;
                $data['link_title']  = $request->link_title;
                $data['link_domain'] = ContentBlock::extractDomain($request->link_url);
                break;
        }

        $block = ContentBlock::create($data);

        // Dispatch konversi flipbook ke queue
        if ($request->type === 'pdf_flipbook') {
            ConvertPdfToFlipbook::dispatch($block);
        }

        return redirect()
            ->route('guru.meetings.materials.edit', [$meeting, $material])
            ->with('success', 'Blok konten berhasil ditambahkan.');
    }

    /**
     * Hapus blok konten.
     */
    public function destroyBlock(Meeting $meeting, Material $material, ContentBlock $block): RedirectResponse
    {
        $this->authorizeTeacher($meeting);

        // Hapus file fisik jika ada
        if ($block->file_path) {
            Storage::disk('public')->delete($block->file_path);
        }
        if ($block->flipbook_path) {
            Storage::disk('public')->deleteDirectory($block->flipbook_path);
        }

        $block->delete();

        return redirect()
            ->route('guru.meetings.materials.edit', [$meeting, $material])
            ->with('success', 'Blok konten berhasil dihapus.');
    }

    /**
     * Reorder blok konten via AJAX (drag & drop).
     * Request: { blocks: [{ id: 1, order: 1 }, ...] }
     */
    public function reorderBlocks(Request $request, Meeting $meeting, Material $material): \Illuminate\Http\JsonResponse
    {
        $this->authorizeTeacher($meeting);

        foreach ($request->blocks as $item) {
            ContentBlock::where('id', $item['id'])
                ->where('material_id', $material->id)
                ->update(['order' => $item['order']]);
        }

        return response()->json(['status' => 'ok']);
    }

    // ── Private helper ────────────────────────────────

    private function authorizeTeacher(Meeting $meeting): void
    {
        $teacherUserId = $meeting->schoolClass->teacher->user_id;
        abort_unless(Auth::id() === $teacherUserId || Auth::user()->isAdmin(), 403);
    }

    
}
