<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Faculty;
use Illuminate\Support\Str;

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Faculty::insert([
            [
                "id" =>         Str::uuid(),
                "name" =>       "Fakultas Ilmu Pendidikan",
                "created_at" => "2000-01-01 00:00:00"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "Fakultas Bahasa dan Seni",
                "created_at" => "2000-01-01 00:00:01"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "Fakultas Matematika dan Ilmu Pengetahuan Alam",
                "created_at" => "2000-01-01 00:00:02"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "Fakultas Ilmu Sosial dan Hukum",
                "created_at" => "2000-01-01 00:00:03"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "Fakultas Teknik",
                "created_at" => "2000-01-01 00:00:04"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "Fakultas Ilmu Keolahragaan dan Kesehatan",
                "created_at" => "2000-01-01 00:00:05"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "Fakultas Ekonomi dan Bisnis",
                "created_at" => "2000-01-01 00:00:06"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "Fakultas Psikologi",
                "created_at" => "2000-01-01 00:00:07"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "Sekolah Pascasarjana",
                "created_at" => "2000-01-01 00:00:08"
            ]
        ]);
    }
}