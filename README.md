# Redis - Laravel Application

A modern Laravel web application project built with PHP, featuring comprehensive tools for robust application development.

## 🚀 Features

- **Laravel Framework**: Full-featured PHP web application framework
- **Database Support**: Multiple back-ends for session and cache storage
- **ORM**: Powerful Eloquent database ORM
- **Migrations**: Database agnostic schema migrations
- **Job Processing**: Robust background job processing with queue support
- **Real-time Broadcasting**: Event broadcasting capabilities
- **Testing**: PHPUnit testing framework configured and ready to use

## 📋 Tech Stack

- **Backend**: PHP with Laravel Framework
- **Frontend**: Vue.js via Vite
- **Database**: Configured for multiple database backends
- **Package Management**: 
  - PHP: Composer
  - JavaScript/CSS: NPM

## ⚙️ Project Structure

```
redis/
├── app/                          # Application logic
├── bootstrap/                    # Application bootstrapping
├── config/                       # Configuration files
├── database/                     # Database migrations and seeders
├── public/                       # Public assets
├── resources/                    # Views and raw assets
├── routes/                       # Application routes
├── storage/                      # Application storage (logs, cache)
├── tests/                        # Test files
├── composer.json                 # PHP dependencies
├── package.json                  # JavaScript dependencies
├── phpunit.xml                   # PHPUnit configuration
├── vite.config.js                # Vite configuration for frontend bundling
└── artisan                       # Laravel command-line interface
```

## 🛠️ Installation & Setup

### Prerequisites
- PHP 8.0 or higher
- Composer
- Node.js & NPM

### Steps

1. **Clone the repository**
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

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Run database migrations**
   ```bash
   php artisan migrate
   ```

6. **Start development server**
   ```bash
   php artisan serve
   ```

7. **Build frontend assets** (in another terminal)
   ```bash
   npm run dev
   ```

## 🧪 Testing

Run PHPUnit tests:
```bash
php artisan test
```

Or directly with PHPUnit:
```bash
./vendor/bin/phpunit
```

## 📝 Configuration

- Environment variables: `.env` (copy from `.env.example`)
- Database configuration: `config/database.php`
- Cache configuration: `config/cache.php`
- Session configuration: `config/session.php`

## 🔄 Key Commands

| Command | Description |
|---------|-------------|
| `php artisan serve` | Start the development server |
| `php artisan migrate` | Run database migrations |
| `php artisan tinker` | Interactive shell for testing |
| `npm run dev` | Start Vite development server |
| `npm run build` | Build production assets |
| `php artisan test` | Run test suite |

## 📚 Documentation

- [Laravel Documentation](https://laravel.com/docs)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [Database Migrations](https://laravel.com/docs/migrations)
- [Queue Jobs](https://laravel.com/docs/queues)
- [Broadcasting](https://laravel.com/docs/broadcasting)

## 📄 License

This project is open source software. Check the LICENSE file for details.

## 👤 Author

[frahmnn](https://github.com/frahmnn)

---

**Last Updated**: June 2026
