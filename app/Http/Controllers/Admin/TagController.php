<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TagController extends Controller
{
    /**
     * Display the NFC Tags Inventory list with real database records.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', 'all');
        $search = $request->query('search', '');

        // Summary Metric Baseline from real database tags
        $totalDiproduksi = Tag::count();
        $tagTerhubung = Tag::where('status', 'aktif')->count();
        $tagStok = Tag::where('status', 'belum_aktif')->count();
        $tagDiblokir = Tag::where('status', 'nonaktif')->count();
        $rasioAktif = $totalDiproduksi > 0 ? round(($tagTerhubung / $totalDiproduksi) * 100, 1).'%' : '0%';

        $metrics = [
            'total_diproduksi' => $totalDiproduksi,
            'tag_terhubung' => $tagTerhubung,
            'rasio_aktif' => $rasioAktif,
            'tag_stok' => $tagStok,
            'tag_diblokir' => $tagDiblokir,
        ];

        $tagsQuery = Tag::with(['user.profile'])->latest('id');

        if ($statusFilter !== 'all') {
            $tagsQuery->statusFilter($statusFilter);
        }

        if (filled($search)) {
            $tagsQuery->searchFilter($search);
        }

        $tags = $tagsQuery->paginate(10)->withQueryString();
        $users = User::where('role', 'user')->orderBy('name')->get();

        return view('admin.tags', compact('metrics', 'tags', 'statusFilter', 'search', 'users'));
    }

    /**
     * Block a tag by administrator (FR-ADM-04 & BR-04).
     */
    public function block(Tag $tag): RedirectResponse
    {
        $tag->update([
            'status' => 'nonaktif',
            'disabled_by' => 'admin',
        ]);

        AuditLog::record('block_tag', "Tag [{$tag->tag_code}] diblokir oleh admin");

        return back()->with('success', "Tag {$tag->tag_code} berhasil diblokir oleh pengelola.");
    }

    /**
     * Unblock a tag by administrator.
     */
    public function unblock(Tag $tag): RedirectResponse
    {
        $newStatus = $tag->user_id ? 'aktif' : 'belum_aktif';

        $tag->update([
            'status' => $newStatus,
            'disabled_by' => null,
        ]);

        AuditLog::record('unblock_tag', "Blokir Tag [{$tag->tag_code}] dibuka oleh admin");

        return back()->with('success', "Blokir pada Tag {$tag->tag_code} berhasil dibuka (Status: {$newStatus}).");
    }

    /**
     * Unlink tag ownership (BR-01 & BR-04).
     */
    public function unlink(Tag $tag): RedirectResponse
    {
        $prevOwner = $tag->user?->name ?? ('User #'.$tag->user_id);

        $tag->update([
            'user_id' => null,
            'status' => 'belum_aktif',
            'disabled_by' => null,
            'activated_at' => null,
        ]);

        AuditLog::record('unlink_tag', "Kepemilikan Tag [{$tag->tag_code}] dilepas dari {$prevOwner}");

        return back()->with('success', "Tautan kepemilikan Tag {$tag->tag_code} berhasil dilepas.");
    }

    /**
     * Pair/assign an unassigned tag to a registered user.
     */
    public function pair(Request $request, Tag $tag): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $targetUser = User::findOrFail($validated['user_id']);

        $tag->update([
            'user_id' => $targetUser->id,
            'status' => 'aktif',
            'activated_at' => now(),
            'disabled_by' => null,
        ]);

        AuditLog::record('pair_tag', "Tag [{$tag->tag_code}] ditautkan ke pengguna {$targetUser->name}");

        return back()->with('success', "Tag {$tag->tag_code} berhasil ditautkan ke {$targetUser->name}.");
    }

    /**
     * Export the current tags inventory to CSV (FR-ADM-04).
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $statusFilter = $request->query('status', 'all');
        $search = $request->query('search', '');

        $tagsQuery = Tag::with(['user.profile'])->latest('id');

        if ($statusFilter !== 'all') {
            $tagsQuery->statusFilter($statusFilter);
        }

        if (filled($search)) {
            $tagsQuery->searchFilter($search);
        }

        $tags = $tagsQuery->get();

        $filename = 'FlexiTag_Inventory_'.date('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($tags) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'Tag_Code', 'Batch_ID', 'Status', 'Disabled_By', 'Pemilik_Nama', 'Pemilik_Email', 'Mode_Aktif', 'URL_Publik', 'Tanggal_Diproduksi', 'Tanggal_Aktivasi']);

            foreach ($tags as $index => $t) {
                fputcsv($handle, [
                    $index + 1,
                    $t->tag_code,
                    $t->batch_id ?? '-',
                    $t->status,
                    $t->disabled_by ?? '-',
                    $t->user?->name ?? 'Belum Terhubung',
                    $t->user?->email ?? '-',
                    $t->user?->profile?->active_mode ?? '-',
                    $t->public_url,
                    $t->produced_at ? $t->produced_at->format('Y-m-d H:i') : '-',
                    $t->activated_at ? $t->activated_at->format('Y-m-d H:i') : '-',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
