<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\LabRoom;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@siplab.test'],
            ['name' => 'Admin SIP-Lab', 'password' => Hash::make('password'), 'role' => 'admin', 'status' => 'active']
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@siplab.test'],
            ['name' => 'Staf Sarpras', 'password' => Hash::make('password'), 'role' => 'staff', 'status' => 'active']
        );

        $head = User::firstOrCreate(
            ['email' => 'kepala@siplab.test'],
            ['name' => 'Kepala Laboratorium', 'password' => Hash::make('password'), 'role' => 'kepala_lab', 'status' => 'active']
        );

        $student = User::firstOrCreate(
            ['email' => 'mahasiswa@siplab.test'],
            ['name' => 'Mahasiswa Demo', 'password' => Hash::make('password'), 'role' => 'mahasiswa', 'student_number' => '240101082', 'program_study' => 'Teknik Informatika', 'status' => 'active']
        );

        $category1 = Category::firstOrCreate(['name' => 'Elektronik']);
        $category2 = Category::firstOrCreate(['name' => 'Alat Ukur']);

        $room1 = LabRoom::firstOrCreate([
            'code' => 'LAB-101',
        ], [
            'name' => 'Laboratorium Riset AI',
            'location' => 'Gedung A Lt. 2',
            'capacity' => 20,
            'description' => 'Laboratorium untuk riset AI dan pengembangan perangkat lunak.',
            'status' => 'available',
            'open_time' => '08:00:00',
            'close_time' => '17:00:00',
        ]);

        $room2 = LabRoom::firstOrCreate([
            'code' => 'LAB-202',
        ], [
            'name' => 'Laboratorium Elektronika',
            'location' => 'Gedung B Lt. 1',
            'capacity' => 15,
            'description' => 'Laboratorium untuk praktikum elektronika dan sensor.',
            'status' => 'available',
            'open_time' => '08:00:00',
            'close_time' => '17:00:00',
        ]);

        Asset::firstOrCreate(['code' => 'AST-001'], [
            'category_id' => $category1->id,
            'lab_room_id' => $room1->id,
            'name' => 'Laptop Riset',
            'condition' => 'baik',
            'status' => 'available',
            'quantity' => 5,
            'unit' => 'unit',
            'notes' => 'Laptop untuk kebutuhan riset dan analisis data.',
        ]);

        Asset::firstOrCreate(['code' => 'AST-002'], [
            'category_id' => $category2->id,
            'lab_room_id' => $room2->id,
            'name' => 'Multimeter Digital',
            'condition' => 'baik',
            'status' => 'available',
            'quantity' => 3,
            'unit' => 'unit',
            'notes' => 'Digunakan untuk pengukuran rangkaian dan kelistrikan.',
        ]);
    }
}
