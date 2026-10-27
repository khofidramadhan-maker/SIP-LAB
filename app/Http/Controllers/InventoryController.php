<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\LabRoom;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $rooms = LabRoom::all();
        $assets = Asset::with('category')->get();
        return view('inventory.index', compact('rooms', 'assets'));
    }

    public function show(Asset $asset)
    {
        return view('inventory.show', compact('asset'));
    }
}
