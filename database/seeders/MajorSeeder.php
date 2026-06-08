<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Major;
use App\Models\Faculty;
use Illuminate\Support\Str;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faculties = Faculty::pluck("id","name");
        Major::insert([
            [
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Manajemen Pendidikan",
                "faculty_id" => $faculties["Fakultas Ilmu Pendidikan"],
                "created_at" => "2000-01-01 00:00:09"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Guru PAUD",
                "faculty_id" => $faculties["Fakultas Ilmu Pendidikan"],
                "created_at" => "2000-01-01 00:00:10"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Guru Sekolah Dasar",
                "faculty_id" => $faculties["Fakultas Ilmu Pendidikan"],
                "created_at" => "2000-01-01 00:00:11"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Khusus",
                "faculty_id" => $faculties["Fakultas Ilmu Pendidikan"],
                "created_at" => "2000-01-01 00:00:12"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Masyarakat",
                "faculty_id" => $faculties["Fakultas Ilmu Pendidikan"],
                "created_at" => "2000-01-01 00:00:13"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Bimbingan Konseling",
                "faculty_id" => $faculties["Fakultas Ilmu Pendidikan"],
                "created_at" => "2000-01-01 00:00:14"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Teknologi Pendidikan",
                "faculty_id" => $faculties["Fakultas Ilmu Pendidikan"],
                "created_at" => "2000-01-01 00:00:15"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Perpustakaan dan Sains Informasi",
                "faculty_id" => $faculties["Fakultas Ilmu Pendidikan"],
                "created_at" => "2000-01-01 00:00:16"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Bimbingan Konseling",
                "faculty_id" => $faculties["Fakultas Ilmu Pendidikan"],
                "created_at" => "2000-01-01 00:00:17"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Pendidikan Masyarakat",
                "faculty_id" => $faculties["Fakultas Ilmu Pendidikan"],
                "created_at" => "2000-01-01 00:00:18"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Teknologi Pendidikan",
                "faculty_id" => $faculties["Fakultas Ilmu Pendidikan"],
                "created_at" => "2000-01-01 00:00:19"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Pendidikan Anak Usia Dini",
                "faculty_id" => $faculties["Fakultas Ilmu Pendidikan"],
                "created_at" => "2000-01-01 00:00:20"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Pendidikan Dasar",
                "faculty_id" => $faculties["Fakultas Ilmu Pendidikan"],
                "created_at" => "2000-01-01 00:00:21"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Manajemen Pendidikan",
                "faculty_id" => $faculties["Fakultas Ilmu Pendidikan"],
                "created_at" => "2000-01-01 00:00:22"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Pendidikan Khusus",
                "faculty_id" => $faculties["Fakultas Ilmu Pendidikan"],
                "created_at" => "2000-01-01 00:00:23"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S3 - Teknologi Pendidikan",
                "faculty_id" => $faculties["Fakultas Ilmu Pendidikan"],
                "created_at" => "2000-01-01 00:00:24"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S3 - Pendidikan Anak Usia Dini",
                "faculty_id" => $faculties["Fakultas Ilmu Pendidikan"],
                "created_at" => "2000-01-01 00:00:25"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S3 - Pendidikan Dasar",
                "faculty_id" => $faculties["Fakultas Ilmu Pendidikan"],
                "created_at" => "2000-01-01 00:00:26"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S3 - Manajemen Pendidikan",
                "faculty_id" => $faculties["Fakultas Ilmu Pendidikan"],
                "created_at" => "2000-01-01 00:00:27"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Bahasa Arab",
                "faculty_id" => $faculties["Fakultas Bahasa dan Seni"],
                "created_at" => "2000-01-01 00:00:09"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Bahasa Indonesia",
                "faculty_id" => $faculties["Fakultas Bahasa dan Seni"],
                "created_at" => "2000-01-01 00:00:10"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Bahasa Inggris",
                "faculty_id" => $faculties["Fakultas Bahasa dan Seni"],
                "created_at" => "2000-01-01 00:00:11"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Bahasa Jepang",
                "faculty_id" => $faculties["Fakultas Bahasa dan Seni"],
                "created_at" => "2000-01-01 00:00:12"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Bahasa Jerman",
                "faculty_id" => $faculties["Fakultas Bahasa dan Seni"],
                "created_at" => "2000-01-01 00:00:13"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Bahasa Mandarin",
                "faculty_id" => $faculties["Fakultas Bahasa dan Seni"],
                "created_at" => "2000-01-01 00:00:14"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Bahasa Prancis",
                "faculty_id" => $faculties["Fakultas Bahasa dan Seni"],
                "created_at" => "2000-01-01 00:00:15"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Musik",
                "faculty_id" => $faculties["Fakultas Bahasa dan Seni"],
                "created_at" => "2000-01-01 00:00:16"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Seni Rupa",
                "faculty_id" => $faculties["Fakultas Bahasa dan Seni"],
                "created_at" => "2000-01-01 00:00:17"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Tari",
                "faculty_id" => $faculties["Fakultas Bahasa dan Seni"],
                "created_at" => "2000-01-01 00:00:18"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Sastra Indonesia",
                "faculty_id" => $faculties["Fakultas Bahasa dan Seni"],
                "created_at" => "2000-01-01 00:00:19"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Sastra Inggris",
                "faculty_id" => $faculties["Fakultas Bahasa dan Seni"],
                "created_at" => "2000-01-01 00:00:20"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Pendidikan Bahasa Indonesia",
                "faculty_id" => $faculties["Fakultas Bahasa dan Seni"],
                "created_at" => "2000-01-01 00:00:21"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Pendidikan Bahasa Inggris",
                "faculty_id" => $faculties["Fakultas Bahasa dan Seni"],
                "created_at" => "2000-01-01 00:00:22"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Pendidikan Bahasa Arab",
                "faculty_id" => $faculties["Fakultas Bahasa dan Seni"],
                "created_at" => "2000-01-01 00:00:23"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Pendidikan Seni",
                "faculty_id" => $faculties["Fakultas Bahasa dan Seni"],
                "created_at" => "2000-01-01 00:00:24"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Matematika",
                "faculty_id" => $faculties["Fakultas Matematika dan Ilmu Pengetahuan Alam"],
                "created_at" => "2000-01-01 00:00:09"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Fisika",
                "faculty_id" => $faculties["Fakultas Matematika dan Ilmu Pengetahuan Alam"],
                "created_at" => "2000-01-01 00:00:10"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Kimia",
                "faculty_id" => $faculties["Fakultas Matematika dan Ilmu Pengetahuan Alam"],
                "created_at" => "2000-01-01 00:00:11"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Biologi",
                "faculty_id" => $faculties["Fakultas Matematika dan Ilmu Pengetahuan Alam"],
                "created_at" => "2000-01-01 00:00:12"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Matematia",
                "faculty_id" => $faculties["Fakultas Matematika dan Ilmu Pengetahuan Alam"],
                "created_at" => "2000-01-01 00:00:13"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Fisika",
                "faculty_id" => $faculties["Fakultas Matematika dan Ilmu Pengetahuan Alam"],
                "created_at" => "2000-01-01 00:00:14"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Kimia",
                "faculty_id" => $faculties["Fakultas Matematika dan Ilmu Pengetahuan Alam"],
                "created_at" => "2000-01-01 00:00:15"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Biologi",
                "faculty_id" => $faculties["Fakultas Matematika dan Ilmu Pengetahuan Alam"],
                "created_at" => "2000-01-01 00:00:16"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Ilmu Komputer",
                "faculty_id" => $faculties["Fakultas Matematika dan Ilmu Pengetahuan Alam"],
                "created_at" => "2000-01-01 00:00:17"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Statistika",
                "faculty_id" => $faculties["Fakultas Matematika dan Ilmu Pengetahuan Alam"],
                "created_at" => "2000-01-01 00:00:18"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Pendidikan Matematika",
                "faculty_id" => $faculties["Fakultas Matematika dan Ilmu Pengetahuan Alam"],
                "created_at" => "2000-01-01 00:00:19"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Pendidikan Fisika",
                "faculty_id" => $faculties["Fakultas Matematika dan Ilmu Pengetahuan Alam"],
                "created_at" => "2000-01-01 00:00:20"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Pendidikan Kimia",
                "faculty_id" => $faculties["Fakultas Matematika dan Ilmu Pengetahuan Alam"],
                "created_at" => "2000-01-01 00:00:21"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Pendidikan Biologi",
                "faculty_id" => $faculties["Fakultas Matematika dan Ilmu Pengetahuan Alam"],
                "created_at" => "2000-01-01 00:00:22"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Geografi",
                "faculty_id" => $faculties["Fakultas Ilmu Sosial dan Hukum"],
                "created_at" => "2000-01-01 00:00:09"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Sejarah",
                "faculty_id" => $faculties["Fakultas Ilmu Sosial dan Hukum"],
                "created_at" => "2000-01-01 00:00:10"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Geografi",
                "faculty_id" => $faculties["Fakultas Ilmu Sosial dan Hukum"],
                "created_at" => "2000-01-01 00:00:11"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "D4 - Hubungan Masyarakat dan Komunikasi Digital",
                "faculty_id" => $faculties["Fakultas Ilmu Sosial dan Hukum"],
                "created_at" => "2000-01-01 00:00:12"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Ilmu Komunikasi",
                "faculty_id" => $faculties["Fakultas Ilmu Sosial dan Hukum"],
                "created_at" => "2000-01-01 00:00:13"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Agama Islam",
                "faculty_id" => $faculties["Fakultas Ilmu Sosial dan Hukum"],
                "created_at" => "2000-01-01 00:00:14"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Ilmu Pengetahuan Sosial",
                "faculty_id" => $faculties["Fakultas Ilmu Sosial dan Hukum"],
                "created_at" => "2000-01-01 00:00:15"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Pancasila & Kewarganegaraan",
                "faculty_id" => $faculties["Fakultas Ilmu Sosial dan Hukum"],
                "created_at" => "2000-01-01 00:00:16"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Sosiologi",
                "faculty_id" => $faculties["Fakultas Ilmu Sosial dan Hukum"],
                "created_at" => "2000-01-01 00:00:17"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Sosiologi",
                "faculty_id" => $faculties["Fakultas Ilmu Sosial dan Hukum"],
                "created_at" => "2000-01-01 00:00:18"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "D4 - Usaha Perjalanan Wisata",
                "faculty_id" => $faculties["Fakultas Ilmu Sosial dan Hukum"],
                "created_at" => "2000-01-01 00:00:19"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Ilmu Hukum",
                "faculty_id" => $faculties["Fakultas Ilmu Sosial dan Hukum"],
                "created_at" => "2000-01-01 00:00:20"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Pendidikan Geografi",
                "faculty_id" => $faculties["Fakultas Ilmu Sosial dan Hukum"],
                "created_at" => "2000-01-01 00:00:21"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Pendidikan Sejarah",
                "faculty_id" => $faculties["Fakultas Ilmu Sosial dan Hukum"],
                "created_at" => "2000-01-01 00:00:22"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Pendidikan Pancasaila dan Kewarganegaraan",
                "faculty_id" => $faculties["Fakultas Ilmu Sosial dan Hukum"],
                "created_at" => "2000-01-01 00:00:23"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Vokasional Konstruksi Bangunan",
                "faculty_id" => $faculties["Fakultas Teknik"],
                "created_at" => "2000-01-01 00:00:09"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Vokasional Teknik Elektro",
                "faculty_id" => $faculties["Fakultas Teknik"],
                "created_at" => "2000-01-01 00:00:10"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Vokasional Teknik Elektronika",
                "faculty_id" => $faculties["Fakultas Teknik"],
                "created_at" => "2000-01-01 00:00:11"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Vokasional Teknik Mesin",
                "faculty_id" => $faculties["Fakultas Teknik"],
                "created_at" => "2000-01-01 00:00:12"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Teknik Mesin",
                "faculty_id" => $faculties["Fakultas Teknik"],
                "created_at" => "2000-01-01 00:00:13"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Sistem dan Teknologi Informasi",
                "faculty_id" => $faculties["Fakultas Teknik"],
                "created_at" => "2000-01-01 00:00:14"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Informatika",
                "faculty_id" => $faculties["Fakultas Teknik"],
                "created_at" => "2000-01-01 00:00:15"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Rekayasa Keselamatan Kebakaran",
                "faculty_id" => $faculties["Fakultas Teknik"],
                "created_at" => "2000-01-01 00:00:16"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Vokasional Seni Kuliner",
                "faculty_id" => $faculties["Fakultas Teknik"],
                "created_at" => "2000-01-01 00:00:17"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Vokasional Desain Fashion",
                "faculty_id" => $faculties["Fakultas Teknik"],
                "created_at" => "2000-01-01 00:00:18"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Vokasional Tata Rias",
                "faculty_id" => $faculties["Fakultas Teknik"],
                "created_at" => "2000-01-01 00:00:19"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Vokasional Kesejahteraan Keluarga",
                "faculty_id" => $faculties["Fakultas Teknik"],
                "created_at" => "2000-01-01 00:00:20"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "D4 - Teknologi Rekayasa Otomasi",
                "faculty_id" => $faculties["Fakultas Teknik"],
                "created_at" => "2000-01-01 00:00:21"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "D4 - Teknologi Rekayasa Manufaktur",
                "faculty_id" => $faculties["Fakultas Teknik"],
                "created_at" => "2000-01-01 00:00:22"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "D4 - Teknologi Rekayasa Konstruksi Bangunan Gedung",
                "faculty_id" => $faculties["Fakultas Teknik"],
                "created_at" => "2000-01-01 00:00:23"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "D4 - Manajemen Pelabuhan dan Logistik Maritim",
                "faculty_id" => $faculties["Fakultas Teknik"],
                "created_at" => "2000-01-01 00:00:24"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "D4 - Seni Kuliner dan Pengolahan Jasa Makanan",
                "faculty_id" => $faculties["Fakultas Teknik"],
                "created_at" => "2000-01-01 00:00:25"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "D4 - Desain Mode",
                "faculty_id" => $faculties["Fakultas Teknik"],
                "created_at" => "2000-01-01 00:00:26"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "D4 - Kosmetik dan Perawatan Kecantikan",
                "faculty_id" => $faculties["Fakultas Teknik"],
                "created_at" => "2000-01-01 00:00:27"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Pendidikan Teknologi Dan Kejuruan",
                "faculty_id" => $faculties["Fakultas Teknik"],
                "created_at" => "2000-01-01 00:00:28"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Ilmu Keolahragaaan",
                "faculty_id" => $faculties["Fakultas Ilmu Keolahragaan dan Kesehatan"],
                "created_at" => "2000-01-01 00:00:09"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Jasmani",
                "faculty_id" => $faculties["Fakultas Ilmu Keolahragaan dan Kesehatan"],
                "created_at" => "2000-01-01 00:00:10"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Kepelatihan Olahraga",
                "faculty_id" => $faculties["Fakultas Ilmu Keolahragaan dan Kesehatan"],
                "created_at" => "2000-01-01 00:00:11"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Olahraga Rekreasi",
                "faculty_id" => $faculties["Fakultas Ilmu Keolahragaan dan Kesehatan"],
                "created_at" => "2000-01-01 00:00:12"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Kepelatihan Kecabangan Olahraga",
                "faculty_id" => $faculties["Fakultas Ilmu Keolahragaan dan Kesehatan"],
                "created_at" => "2000-01-01 00:00:13"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Pendidikan Jasmani",
                "faculty_id" => $faculties["Fakultas Ilmu Keolahragaan dan Kesehatan"],
                "created_at" => "2000-01-01 00:00:14"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Ilmu Keolahragaan",
                "faculty_id" => $faculties["Fakultas Ilmu Keolahragaan dan Kesehatan"],
                "created_at" => "2000-01-01 00:00:15"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S3 - Pendidikan Jasmani",
                "faculty_id" => $faculties["Fakultas Ilmu Keolahragaan dan Kesehatan"],
                "created_at" => "2000-01-01 00:00:16"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Akuntansi",
                "faculty_id" => $faculties["Fakultas Ekonomi dan Bisnis"],
                "created_at" => "2000-01-01 00:00:09"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Manajemen",
                "faculty_id" => $faculties["Fakultas Ekonomi dan Bisnis"],
                "created_at" => "2000-01-01 00:00:10"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Bisnis Digital",
                "faculty_id" => $faculties["Fakultas Ekonomi dan Bisnis"],
                "created_at" => "2000-01-01 00:00:11"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Administrasi Perkantoran",
                "faculty_id" => $faculties["Fakultas Ekonomi dan Bisnis"],
                "created_at" => "2000-01-01 00:00:12"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Akuntansi",
                "faculty_id" => $faculties["Fakultas Ekonomi dan Bisnis"],
                "created_at" => "2000-01-01 00:00:13"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Bisnis",
                "faculty_id" => $faculties["Fakultas Ekonomi dan Bisnis"],
                "created_at" => "2000-01-01 00:00:14"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Pendidikan Ekonomi",
                "faculty_id" => $faculties["Fakultas Ekonomi dan Bisnis"],
                "created_at" => "2000-01-01 00:00:15"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "D4 - Administrasi Perkantoran Digital",
                "faculty_id" => $faculties["Fakultas Ekonomi dan Bisnis"],
                "created_at" => "2000-01-01 00:00:16"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "D4 - Akuntansi Sektor Publik",
                "faculty_id" => $faculties["Fakultas Ekonomi dan Bisnis"],
                "created_at" => "2000-01-01 00:00:17"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "D4 - Pemasaran Digital",
                "faculty_id" => $faculties["Fakultas Ekonomi dan Bisnis"],
                "created_at" => "2000-01-01 00:00:18"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Manajemen",
                "faculty_id" => $faculties["Fakultas Ekonomi dan Bisnis"],
                "created_at" => "2000-01-01 00:00:19"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Akuntansi",
                "faculty_id" => $faculties["Fakultas Ekonomi dan Bisnis"],
                "created_at" => "2000-01-01 00:00:20"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Pendidikan Ekonomi",
                "faculty_id" => $faculties["Fakultas Ekonomi dan Bisnis"],
                "created_at" => "2000-01-01 00:00:21"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S3 - Ilmu Manajemen",
                "faculty_id" => $faculties["Fakultas Ekonomi dan Bisnis"],
                "created_at" => "2000-01-01 00:00:22"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S3 - Ilmu Akuntansi",
                "faculty_id" => $faculties["Fakultas Ekonomi dan Bisnis"],
                "created_at" => "2000-01-01 00:00:23"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S1 - Psikologi",
                "faculty_id" => $faculties["Fakultas Psikologi"],
                "created_at" => "2000-01-01 00:00:09"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Sains Psikologi",
                "faculty_id" => $faculties["Fakultas Psikologi"],
                "created_at" => "2000-01-01 00:00:10"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Linguistik Terapan",
                "faculty_id" => $faculties["Sekolah Pascasarjana"],
                "created_at" => "2000-01-01 00:00:09"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Pendidikan Lingkungan",
                "faculty_id" => $faculties["Sekolah Pascasarjana"],
                "created_at" => "2000-01-01 00:00:10"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Manajemen Lingkungan",
                "faculty_id" => $faculties["Sekolah Pascasarjana"],
                "created_at" => "2000-01-01 00:00:11"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Penelitian dan Evaluasi Pendidikan",
                "faculty_id" => $faculties["Sekolah Pascasarjana"],
                "created_at" => "2000-01-01 00:00:12"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S2 - Manajemen Pendidikan Tinggi",
                "faculty_id" => $faculties["Sekolah Pascasarjana"],
                "created_at" => "2000-01-01 00:00:13"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S3 - Linguistik Terapan",
                "faculty_id" => $faculties["Sekolah Pascasarjana"],
                "created_at" => "2000-01-01 00:00:14"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S3 - Pendidikan Kependudukan dan Lingkungan Hidup",
                "faculty_id" => $faculties["Sekolah Pascasarjana"],
                "created_at" => "2000-01-01 00:00:15"
            ],[
                "id" =>         Str::uuid(),
                "name" =>       "S3 - Penelitian dan Evaluasi Pendidikan",
                "faculty_id" => $faculties["Sekolah Pascasarjana"],
                "created_at" => "2000-01-01 00:00:16"
            ]
        ]);
    }
}