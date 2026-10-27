<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\LabRoom;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::count();
        $rooms = LabRoom::count();
        $assets = Asset::count();

        return view('admin.index', compact('users', 'rooms', 'assets'));
    }
}
