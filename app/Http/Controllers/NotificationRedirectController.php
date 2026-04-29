<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationRedirectController extends Controller
{
    /**
     * Tandai notifikasi sebagai dibaca, lalu redirect ke URL tujuan dengan highlight parameter.
     */
    public function __invoke(Request $request, string $notificationId): RedirectResponse
    {
        $user = $request->user();

        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
        }

        $targetUrl = $request->query('url', '/');
        $highlightId = $request->query('highlight');

        // Tambahkan highlight param ke target URL
        if ($highlightId) {
            $separator = str_contains($targetUrl, '?') ? '&' : '?';
            $targetUrl .= $separator.'highlight='.$highlightId;
        }

        return redirect($targetUrl);
    }
}
