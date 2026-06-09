# Sistem Reservasi Ruang Asisten

Platform web untuk manajemen reservasi ruang asisten khusus bagi mahasiswa penyandang disabilitas di universitas. Sistem ini memudahkan mahasiswa untuk melakukan booking ruang, dan membantu asisten dalam manajemen jadwal dan reservasi.

## 📋 Fitur Utama

### Untuk Mahasiswa
- **Google OAuth Login**: Login mudah menggunakan akun Google
- **Verifikasi Akun**: Pengisian data profil (nama, NIM, jurusan, angkatan, jenis kelamin, kontak WhatsApp)
- **Reservasi Ruang**: Booking ruang asisten dengan sistem kalender interaktif
- **Lihat Ketersediaan**: Visualisasi jadwal ruang asisten dan libur nasional
- **Manajemen Reservasi**: Lihat dan kelola reservasi yang sudah dibuat

### Untuk Asisten
- **Dashboard Jadwal**: Melihat semua jadwal ruang dan reservasi
- **Manajemen Jadwal**: Mengatur jadwal ketersediaan ruang asisten
- **Tracking Reservasi**: Pantau semua reservasi mahasiswa

### Untuk Admin
- **Manajemen User**: Verifikasi dan kelola data mahasiswa
- **Manajemen Staff**: Kelola data asisten dan staff
- **Statistik & Laporan**: Lihat total user, staff, dan user yang belum terverifikasi
- **Konfigurasi Kontak**: Atur nomor WhatsApp Contact Person untuk notifikasi
- **Manajemen Jadwal**: Setup jadwal ketersediaan ruang

## 🛠️ Tech Stack

| Bagian | Teknologi |
|--------|-----------|
| **Backend** | PHP 8.2+, Laravel 12 |
| **Frontend** | Vue.js, Vite, Tailwind CSS, Bootstrap 5 |
| **Database** | SQLite (default), MySQL/PostgreSQL (configurable) |
| **Authentication** | Google OAuth (Laravel Socialite) |
| **Package Manager** | Composer (PHP), NPM (Frontend) |
| **Testing** | PHPUnit |

## 📂 Struktur Project

```
redis/
├── app/
│   ├── Http/Controllers/
│   │   ├── UserController.php          # Login & verifikasi user
│   │   ├── ReservationController.php   # Reservasi ruang asisten
│   │   ├── AdminController.php         # Manajemen admin
│   │   └── AssistantController.php     # Dashboard asisten
│   ├── Models/                         # Database models
│   │   ├── User.php
│   │   ├── Identity.php               # Data profil mahasiswa
│   │   ├── Reservation.php
│   │   ├── Schedule.php
│   │   ├── Faculty.php & Major.php
│   │   ├── Holiday.php & Month.php
│   │   └── ContactPerson.php
│   └── Mail/                          # Email notifications
├── resources/                         # Frontend views & assets
├── database/                          # Migrations & seeders
├── routes/                            # API & web routes
├── config/                            # Configuration files
└── tests/                             # Test files
```

## ⚙️ Instalasi & Setup

### Prerequisites
- PHP 8.2 atau lebih tinggi
- Composer
- Node.js & NPM
- Database (SQLite, MySQL, atau PostgreSQL)

### Langkah-Langkah

1. **Clone repository**
   ```bash
   git clone https://github.com/frahmnn/redis.git
   cd redis
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Setup Google OAuth**
   - Buat project di [Google Cloud Console](https://console.cloud.google.com)
   - Buat OAuth 2.0 credentials (Client ID & Client Secret)
   - Tambahkan ke `.env`:
     ```
     GOOGLE_CLIENT_ID=your_client_id
     GOOGLE_CLIENT_SECRET=your_client_secret
     ```

6. **Setup Calendar API** (untuk libur nasional)
   - Dapatkan API key dari [Calendarific](https://calendarific.com)
   - Tambahkan ke `.env`:
     ```
     CALENDARIFIC_API_KEY=your_api_key
     ```

7. **Jalankan database migrations**
   ```bash
   php artisan migrate
   ```

8. **Mulai development server**
   ```bash
   # Terminal 1 - Backend
   php artisan serve
   
   # Terminal 2 - Frontend (dev)
   npm run dev
   
   # Terminal 3 - Queue listener (untuk background jobs)
   php artisan queue:listen
   ```

Atau gunakan command yang sudah disediakan:
```bash
npm run dev:all
```

## 📝 Konfigurasi Penting

- **Database**: Edit `DB_CONNECTION` di `.env` (sqlite, mysql, atau pgsql)
- **Session**: Disimpan di database (SESSION_DRIVER=database)
- **Cache**: Menggunakan database (CACHE_STORE=database)
- **Queue**: Menggunakan database (QUEUE_CONNECTION=database)
- **Mail**: Default menggunakan log driver (MAIL_MAILER=log)

## 🧪 Testing

```bash
# Jalankan semua test
php artisan test

# Atau dengan PHPUnit langsung
./vendor/bin/phpunit

# Run specific test
php artisan test tests/Unit/UserTest.php
```

## 🔄 Command Penting

| Command | Deskripsi |
|---------|-----------|
| `php artisan serve` | Jalankan development server |
| `php artisan migrate` | Jalankan database migrations |
| `php artisan tinker` | Interactive shell |
| `npm run dev` | Mulai Vite dev server (frontend) |
| `npm run build` | Build frontend untuk production |
| `php artisan test` | Jalankan test suite |
| `php artisan queue:listen` | Jalankan queue listener |

## 📚 Dokumentasi

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Socialite](https://laravel.com/docs/socialite)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Vite](https://vitejs.dev/guide/)
- [Google OAuth Setup](https://developers.google.com/identity/protocols/oauth2)

## 📄 Environment Variables

Key variables di `.env`:
```env
APP_NAME=Laravel
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite          # Database connection
SESSION_DRIVER=database       # Session storage
QUEUE_CONNECTION=database     # Queue driver

REDIS_HOST=127.0.0.1         # Redis (optional)
REDIS_PORT=6379

GOOGLE_CLIENT_ID=your_id
GOOGLE_CLIENT_SECRET=your_secret

CALENDARIFIC_API_KEY=your_api_key
```

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan:
1. Fork repository ini
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 Lisensi

Project ini dilisensikan di bawah MIT License. Lihat file `LICENSE` untuk detail.

## 👤 Author

[frahmnn](https://github.com/frahmnn)

---

**Last Updated**: Juni 2026
