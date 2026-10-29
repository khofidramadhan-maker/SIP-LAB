<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notifikasi::where('user_id', Auth::id())->latest()->get();
        return view('notifications.index', compact('notifications'));
    }
}
