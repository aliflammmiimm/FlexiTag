<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Mark a message notification as read.
     */
    public function markAsRead(Request $request, int $id): JsonResponse|RedirectResponse
    {
        DB::table('messages')->where('id', $id)->update(['read_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Pesan telah ditandai sebagai dibaca.');
    }

    /**
     * Mark all unread messages as read.
     */
    public function markAllAsRead(Request $request): JsonResponse|RedirectResponse
    {
        DB::table('messages')
            ->whereNull('read_at')
            ->whereIn('type', ['abuse_report', 'contact'])
            ->update(['read_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }

    /**
     * Emergency action: deactivate tag associated with an abuse report.
     */
    public function deactivateTag(Request $request, int $messageId): RedirectResponse
    {
        $message = DB::table('messages')->where('id', $messageId)->first();

        if ($message && $message->user_id) {
            DB::table('tags')
                ->where('user_id', $message->user_id)
                ->update([
                    'status' => 'nonaktif',
                    'disabled_by' => 'admin',
                    'updated_at' => now(),
                ]);

            DB::table('messages')->where('id', $messageId)->update(['read_at' => now()]);

            AuditLog::record('block_tag_abuse', "Tag milik User #{$message->user_id} dinonaktifkan darurat via laporan abuse #{$messageId}");

            return back()->with('success', 'Tag terkait pengguna berhasil dinonaktifkan demi keamanan.');
        }

        return back()->with('error', 'Tag tidak ditemukan atau tidak terikat pada pengguna spesifik.');
    }
}
