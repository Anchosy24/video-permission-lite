<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Video;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================
        // ADMIN
        // ============================
        $admin = User::create([
            'name' => 'Admin Mediatama',
            'email' => 'admin@mediatama.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // ============================
        // CUSTOMERS
        // ============================
        $customer1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@mediatama.com',
            'password' => Hash::make('customer123'),
            'role' => 'customer',
            'is_active' => true,
        ]);

        $customer2 = User::create([
            'name' => 'Siti Rahma',
            'email' => 'siti@mediatama.com',
            'password' => Hash::make('customer123'),
            'role' => 'customer',
            'is_active' => true,
        ]);

        // ============================
        // VIDEOS
        // ============================
        Video::insert([
            [
                'title' => 'Tutorial Laravel Dasar',
                'description' => 'Pengenalan dasar framework Laravel untuk pemula.',
                'video_url' => 'https://www.youtube.com/embed/upOxC-rVJsU?si=oneOmFuUqcLasHQx',
                'duration' => 15, // 15 menit
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Bootstrap 5 Crash Course',
                'description' => 'Belajar Bootstrap 5 untuk tampilan web modern.',
                'video_url' => 'https://www.youtube.com/embed/Jyvffr3aCp0',
                'duration' => 25,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'SweetAlert2 Tutorial',
                'description' => 'Cara menggunakan SweetAlert2 untuk notifikasi interaktif.',
                'video_url' => 'https://www.youtube.com/embed/0W6i5LYKCSI',
                'duration' => 10,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
