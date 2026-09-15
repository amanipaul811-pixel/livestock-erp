<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function open(Request $request, string $notification)
    {
        // Scoped to the current user's own notifications, not a global
        // lookup -- so a stale link (already deleted/pruned, or from
        // before a database reset regenerated every notification's id)
        // and someone else's notification both land here as "not found"
        // instead of a raw 404/403 error page, and either way we send the
        // user somewhere useful instead of leaving them on a dead end.
        $record = $request->user()->notifications()->find($notification);

        if (! $record) {
            return redirect()->route('dashboard')->with('status', 'That notification is no longer available.');
        }

        $record->markAsRead();

        return redirect($record->data['url'] ?? route('dashboard'));
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
