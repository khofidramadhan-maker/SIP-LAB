<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $rooms = Ruangan::all();
        $assets = Alat::all();
        return view('inventory.index', compact('rooms', 'assets'));
    }

    public function show(Alat $asset)
    {
        return view('inventory.show', compact('asset'));
    }
}
