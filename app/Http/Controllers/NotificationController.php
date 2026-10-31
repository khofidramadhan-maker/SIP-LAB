<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = Notifikasi::where('user_id', Auth::id())->latest()->get();

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notifikasi $notification): RedirectResponse
    {
        $ownedNotification = Auth::user()->notifikasi()->findOrFail($notification->id);
        $ownedNotification->update(['sudah_dibaca' => true]);

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }
}
