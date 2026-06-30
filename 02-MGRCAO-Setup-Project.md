# MGRCAO — Setup Project Framework
## Laravel 11 + Inertia.js v2 + Vue 3 + Tailwind CSS
### Portfolio Fullstack Febryanus Tambing

---

# M — Mindset

Anda adalah **Senior Laravel Full-Stack Engineer** yang membangun
portfolio fullstack dari referensi HTML statis yang sudah ada.

Prinsip kerja di project ini:

```
1. Design system HTML asal = KONSTANTA — tidak boleh berubah
   (warna, font, spacing, animasi, component style semua dipertahankan)

2. Inertia.js = jembatan — Laravel handle routing & data,
   Vue 3 handle rendering — tidak perlu REST API terpisah

3. Zero AI Slop — tidak ada:
   - Data hardcoded di Vue component
   - Komponen tanpa props yang terhubung ke controller
   - Form tanpa validation dan error handling
   - File upload tanpa feedback progress

4. Setiap component Vue = satu tanggung jawab yang jelas

5. Seeder wajib: data awal dari HTML asal harus tersedia
   setelah fresh install tanpa input manual
```

---

# Phase 0 — Prerequisites

```bash
# Pastikan versi yang benar terpasang
php --version    # >= 8.2
composer --version
node --version   # >= 18
npm --version
mysql --version  # atau postgres

# Install Laravel Installer global jika belum ada
composer global require laravel/installer
```

---

# Phase 1 — Create Laravel Project

```bash
# Buat project Laravel 11 baru
laravel new portfolio-febryanus

# Pilihan saat wizard:
# Starter kit    : Breeze
# Breeze stack   : Inertia + Vue
# Testing        : Pest
# Database       : mysql (atau pgsql)

# Atau dengan flag lengkap:
composer create-project laravel/laravel portfolio-febryanus
cd portfolio-febryanus
```

---

# Phase 2 — Install Laravel Breeze dengan Inertia Vue Stack

```bash
# Masuk ke folder project
cd portfolio-febryanus

# Install Breeze
composer require laravel/breeze --dev

# Install stack Inertia + Vue 3
php artisan breeze:install vue

# Install frontend dependencies
npm install

# Build aset pertama kali
npm run dev
```

---

# Phase 3 — Konfigurasi Environment

```bash
# Copy .env.example
cp .env.example .env

# Generate application key
php artisan key:generate
```

**Edit `.env`:**

```env
APP_NAME="Portfolio Febryanus Tambing"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portfolio_febryanus
DB_USERNAME=root
DB_PASSWORD=

# Storage untuk file upload
FILESYSTEM_DISK=public

# Mail untuk notifikasi kontak (development: Mailtrap)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_user
MAIL_PASSWORD=your_mailtrap_pass
MAIL_FROM_ADDRESS="portfolio@febryanus.dev"
MAIL_FROM_NAME="Febryanus Tambing"
ADMIN_NOTIFICATION_EMAIL="febryanustambing54@gmail.com"

# Queue untuk email async
QUEUE_CONNECTION=database
```

```bash
# Buat database
mysql -u root -p -e "CREATE DATABASE portfolio_febryanus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Link storage publik
php artisan storage:link
```

---

# Phase 4 — Install & Konfigurasi Tailwind CSS dengan Design System

```bash
# Tailwind sudah terinstall via Breeze, tinggal konfigurasi
# Edit tailwind.config.js di root project
```

**`tailwind.config.js`:**

```javascript
import defaultTheme from 'tailwindcss/defaultTheme'

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
    './resources/js/**/*.js',
  ],

  theme: {
    extend: {
      colors: {
        cream: '#FAF8F1',
        oat: '#F5F1E8',
        'oat-dark': '#E8E2D3',
        paper: '#FFFFFF',
        terracotta: {
          DEFAULT: '#C96442',
          dark:    '#B5532F',
          light:   '#E0916F',
        },
        ink:          '#3D3929',
        taupe:        '#6B6456',
        'taupe-light': '#9C9484',
      },
      fontFamily: {
        serif: ['Fraunces', ...defaultTheme.fontFamily.serif],
        sans:  ['Inter', ...defaultTheme.fontFamily.sans],
        mono:  ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
      },
    },
  },
  plugins: [],
}
```

---

# Phase 5 — Install Dependencies Tambahan

```bash
# Backend PHP
composer require intervention/image-laravel   # optimasi gambar upload
composer require spatie/laravel-sluggable      # opsional: untuk URL sertifikat
composer require laravel/sanctum               # jika mau API token nanti

# Frontend Vue
npm install @headlessui/vue                   # modal, dropdown accessible
npm install @heroicons/vue                     # icons (konsisten dengan HTML asal SVG style)
npm install vue-filepond filepond             # drag & drop file upload
npm install @vueuse/core                      # Vue utilities
npm install vee-validate @vee-validate/zod zod  # form validation
npm install @tanstack/vue-table               # tabel admin
```

---

# Phase 6 — Struktur Folder yang Direkomendasikan

```
portfolio-febryanus/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                    # dari Breeze
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ProfileController.php
│   │   │   │   ├── SkillController.php
│   │   │   │   ├── ExperienceController.php
│   │   │   │   ├── ProjectController.php
│   │   │   │   ├── CertificateController.php
│   │   │   │   ├── CvController.php
│   │   │   │   ├── SocialLinkController.php
│   │   │   │   ├── StatsController.php
│   │   │   │   └── MessageController.php
│   │   │   └── Public/
│   │   │       └── PortfolioController.php
│   │   ├── Middleware/
│   │   │   └── EnsureAdminAuthenticated.php
│   │   └── Requests/
│   │       ├── Admin/
│   │       │   ├── StoreProjectRequest.php
│   │       │   ├── StoreCertificateRequest.php
│   │       │   └── ...
│   │       └── ContactMessageRequest.php
│   ├── Models/
│   │   ├── Profile.php
│   │   ├── Stat.php
│   │   ├── Skill.php
│   │   ├── Experience.php
│   │   ├── Project.php
│   │   ├── Certificate.php
│   │   ├── Cv.php
│   │   ├── SocialLink.php
│   │   └── Message.php
│   ├── Services/
│   │   ├── FileUploadService.php
│   │   └── ImageOptimizeService.php
│   └── Mail/
│       └── NewContactMessage.php
│
├── database/
│   ├── migrations/
│   │   ├── 2025_xx_xx_create_profiles_table.php
│   │   ├── 2025_xx_xx_create_stats_table.php
│   │   ├── 2025_xx_xx_create_skills_table.php
│   │   ├── 2025_xx_xx_create_experiences_table.php
│   │   ├── 2025_xx_xx_create_projects_table.php
│   │   ├── 2025_xx_xx_create_certificates_table.php
│   │   ├── 2025_xx_xx_create_cvs_table.php
│   │   ├── 2025_xx_xx_create_social_links_table.php
│   │   └── 2025_xx_xx_create_messages_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── AdminUserSeeder.php
│       ├── ProfileSeeder.php
│       ├── StatsSeeder.php
│       ├── SkillSeeder.php
│       ├── ExperienceSeeder.php
│       ├── ProjectSeeder.php
│       └── SocialLinkSeeder.php
│
└── resources/
    └── js/
        ├── Pages/
        │   ├── Public/
        │   │   └── Portfolio.vue          # halaman utama (semua section)
        │   └── Admin/
        │       ├── Dashboard.vue
        │       ├── Profile/
        │       │   └── Edit.vue
        │       ├── Skills/
        │       │   ├── Index.vue
        │       │   ├── Create.vue
        │       │   └── Edit.vue
        │       ├── Experiences/
        │       │   ├── Index.vue
        │       │   ├── Create.vue
        │       │   └── Edit.vue
        │       ├── Projects/
        │       │   ├── Index.vue
        │       │   ├── Create.vue
        │       │   └── Edit.vue
        │       ├── Certificates/
        │       │   ├── Index.vue
        │       │   ├── Create.vue
        │       │   └── Edit.vue
        │       ├── Cv/
        │       │   └── Index.vue
        │       ├── SocialLinks/
        │       │   └── Index.vue
        │       ├── Stats/
        │       │   └── Edit.vue
        │       └── Messages/
        │           ├── Index.vue
        │           └── Show.vue
        ├── Layouts/
        │   ├── PublicLayout.vue           # layout portfolio publik
        │   └── AdminLayout.vue            # layout dashboard admin
        └── Components/
            ├── Public/
            │   ├── Navbar.vue
            │   ├── HeroSection.vue
            │   ├── AboutSection.vue
            │   ├── SkillsSection.vue
            │   ├── ExperienceSection.vue
            │   ├── ProjectsSection.vue
            │   ├── ContactSection.vue
            │   └── Footer.vue
            └── Admin/
                ├── Sidebar.vue
                ├── TopBar.vue
                ├── StatsCard.vue
                ├── DataTable.vue
                ├── ConfirmModal.vue
                ├── FileUploader.vue
                └── Toast.vue
```

---

# Phase 7 — Routes Setup

**`routes/web.php`:**

```php
<?php

use App\Http\Controllers\Public\PortfolioController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\CvController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\Admin\StatsController;
use App\Http\Controllers\Admin\MessageController;
use Illuminate\Support\Facades\Route;

// ─── PUBLIC ROUTES ────────────────────────────────────────────
Route::get('/', [PortfolioController::class, 'index'])->name('home');
Route::post('/contact', [PortfolioController::class, 'sendMessage'])->name('contact.send');
Route::get('/cv/download', [PortfolioController::class, 'downloadCv'])->name('cv.download');
Route::get('/certificates/{certificate}', [PortfolioController::class, 'showCertificate'])
    ->name('certificates.show');

// ─── AUTH ROUTES (dari Breeze) ────────────────────────────────
require __DIR__.'/auth.php';

// ─── ADMIN ROUTES ─────────────────────────────────────────────
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified'])
    ->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile & Stats
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');

    Route::get('/stats', [StatsController::class, 'edit'])->name('stats.edit');
    Route::patch('/stats', [StatsController::class, 'update'])->name('stats.update');

    // Skills — full resource
    Route::resource('skills', SkillController::class);
    Route::patch('/skills/{skill}/toggle', [SkillController::class, 'toggle'])->name('skills.toggle');
    Route::patch('/skills/reorder', [SkillController::class, 'reorder'])->name('skills.reorder');

    // Experiences
    Route::resource('experiences', ExperienceController::class);
    Route::patch('/experiences/{experience}/toggle', [ExperienceController::class, 'toggle'])
        ->name('experiences.toggle');

    // Projects
    Route::resource('projects', ProjectController::class);
    Route::patch('/projects/{project}/toggle', [ProjectController::class, 'toggle'])
        ->name('projects.toggle');

    // Certificates
    Route::resource('certificates', CertificateController::class);
    Route::patch('/certificates/{certificate}/toggle', [CertificateController::class, 'toggle'])
        ->name('certificates.toggle');

    // CV
    Route::get('/cv', [CvController::class, 'index'])->name('cv.index');
    Route::post('/cv', [CvController::class, 'store'])->name('cv.store');
    Route::patch('/cv/{cv}/activate', [CvController::class, 'activate'])->name('cv.activate');
    Route::delete('/cv/{cv}', [CvController::class, 'destroy'])->name('cv.destroy');

    // Social Links
    Route::get('/social-links', [SocialLinkController::class, 'index'])->name('social-links.index');
    Route::patch('/social-links', [SocialLinkController::class, 'update'])->name('social-links.update');

    // Messages
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::patch('/messages/{message}/read', [MessageController::class, 'markAsRead'])
        ->name('messages.read');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])
        ->name('messages.destroy');
});
```

---

# Phase 8 — Global CSS & Font Setup

**`resources/js/app.js`:**

```javascript
import '../css/app.css'
import './bootstrap'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'

const appName = import.meta.env.VITE_APP_NAME || 'Portfolio'

createInertiaApp({
    title: (title) => `${title} — ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el)
    },
    progress: {
        color: '#C96442',  // warna terracotta dari design system
    },
})
```

**`resources/css/app.css`:**

```css
@import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap');

@tailwind base;
@tailwind components;
@tailwind utilities;

@layer base {
  html { scroll-behavior: smooth; }
  body { background-color: #FAF8F1; }
  ::selection { background-color: #C96442; color: #FAF8F1; }
  :focus-visible {
    outline: 2px solid #C96442;
    outline-offset: 3px;
    border-radius: 4px;
  }
  @media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
      animation-duration: 0.01ms !important;
      transition-duration: 0.01ms !important;
    }
  }
}

@layer components {
  .reveal {
    @apply opacity-0 translate-y-[18px] transition-all duration-700 ease-in-out;
  }
  .reveal.is-visible {
    @apply opacity-100 translate-y-0;
  }
  .nav-link {
    @apply relative;
  }
  .nav-link::after {
    content: '';
    @apply absolute left-0 -bottom-1 w-0 h-[1.5px] bg-terracotta transition-all duration-[250ms] ease-in-out;
  }
  .nav-link:hover::after {
    @apply w-full;
  }
  .card-hover {
    @apply transition-all duration-200 ease-in-out;
  }
  .card-hover:hover {
    @apply -translate-y-1 shadow-[0_12px_28px_-8px_rgba(61,57,41,0.12)] border-terracotta;
  }
  .btn-primary {
    @apply transition-all duration-200 ease-in-out;
  }
  .btn-primary:hover {
    @apply bg-terracotta-dark -translate-y-[1px];
  }
  .btn-primary:active {
    @apply translate-y-0;
  }
  .skill-pill {
    @apply transition-all duration-200 ease-in-out;
  }
  .skill-pill:hover {
    @apply bg-terracotta text-cream border-terracotta;
  }
}
```

---

# Phase 9 — Jalankan Migrations & Seeders

```bash
# Jalankan semua migration
php artisan migrate

# Jalankan semua seeder (data dari HTML asal)
php artisan db:seed

# Atau fresh + seed sekaligus (development)
php artisan migrate:fresh --seed
```

---

# Phase 10 — Development Server

```bash
# Terminal 1: Laravel backend
php artisan serve

# Terminal 2: Vite (hot reload Vue)
npm run dev

# Terminal 3: Queue worker (untuk email notifikasi kontak)
php artisan queue:work

# Buka browser: http://localhost:8000
# Admin login: http://localhost:8000/login
```

---

# Checklist Setup Selesai

```
[ ] php artisan serve berjalan tanpa error
[ ] npm run dev berjalan, hot reload aktif
[ ] http://localhost:8000/ menampilkan portfolio public
[ ] http://localhost:8000/login bisa login dengan kredensial seeder
[ ] http://localhost:8000/admin menampilkan dashboard admin
[ ] Storage symlink aktif (php artisan storage:link)
[ ] Font Fraunces, Inter, JetBrains Mono ter-load di browser
[ ] Warna terracotta, cream, ink sudah benar di halaman
[ ] Tidak ada error di browser console
[ ] Tidak ada error di php artisan serve output
```
