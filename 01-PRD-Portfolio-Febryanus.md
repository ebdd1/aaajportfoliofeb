# PRD — Portfolio Fullstack Febryanus Tambing
## Product Requirements Document
### Versi 1.0 — Laravel 11 + Inertia.js + Vue 3 + Admin Dashboard

---

## 1. Ringkasan Produk

**Nama Produk:** Portfolio Fullstack Febryanus Tambing
**Versi HTML Asal:** `index.html` (sudah ada, menjadi referensi desain)
**Tujuan Konversi:** Mengubah portfolio HTML statis menjadi sistem fullstack
dengan CMS admin panel — sehingga seluruh konten dapat dikelola tanpa
menyentuh kode.

### 1.1 Masalah yang Diselesaikan

```
SEBELUM (HTML Statis):
  ❌ Setiap update konten = edit kode HTML langsung
  ❌ Tidak ada tempat upload CV / sertifikat
  ❌ Form kontak hanya buka email client (tidak masuk ke database)
  ❌ Tidak ada dashboard untuk kelola proyek, pengalaman, keahlian
  ❌ Tidak ada statistik visitor / pesan masuk

SESUDAH (Fullstack):
  ✅ Semua konten dikelola via Admin Dashboard
  ✅ CV dan sertifikat bisa diupload dan diganti kapan saja
  ✅ Form kontak masuk ke database, ada notifikasi email
  ✅ CRUD penuh: profil, proyek, keahlian, pengalaman, sertifikat
  ✅ Dashboard analytics: total pesan, total view, proyek aktif
```

---

## 2. Design System — Dipertahankan dari HTML Asal

Design system ini adalah **konstanta** — tidak boleh berubah di versi fullstack.

```
WARNA
  cream        : #FAF8F1  (background utama)
  oat          : #F5F1E8  (background section alternate)
  oat-dark     : #E8E2D3  (border, divider)
  paper        : #FFFFFF  (card background)
  terracotta   : #C96442  (primary, CTA, accent)
  terracotta-dark : #B5532F (hover state)
  terracotta-light: #E0916F (secondary accent)
  ink          : #3D3929  (text utama)
  taupe        : #6B6456  (text sekunder)
  taupe-light  : #9C9484  (text tersier, placeholder)

TIPOGRAFI
  Font Serif   : Fraunces (heading, nama, judul section)
  Font Sans    : Inter (body text, label, navigasi)
  Font Mono    : JetBrains Mono (badge kategori, kode, section label kecil)

KOMPONEN
  card-hover   : translateY(-4px), shadow, border terracotta
  btn-primary  : bg-terracotta, hover bg-terracotta-dark, translateY(-1px)
  skill-pill   : hover bg-terracotta, color cream
  nav-link     : underline slide animation via ::after
  reveal       : opacity 0 → 1, translateY(18px → 0), IntersectionObserver
  blob         : SVG blur background decoration
  rounded-2xl  : border radius standar card (16px)
  rounded-full : border radius standar button/pill

SPACING STANDAR
  Section padding  : py-20 lg:py-28
  Container max    : max-w-5xl atau max-w-6xl, px-6 lg:px-10
  Card padding     : p-6 atau p-7
  Gap grid         : gap-5 atau gap-6
```

---

## 3. Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────────┐
│                    VISITOR / PUBLIC                          │
├─────────────────────────────────────────────────────────────┤
│  Portfolio Public (SSR via Inertia.js + Vue 3)              │
│  ├── / (Home — semua section dalam satu halaman)            │
│  ├── /projects (opsional: halaman proyek terpisah)          │
│  └── /certificates/:id (halaman sertifikat publik)          │
├─────────────────────────────────────────────────────────────┤
│                    ADMIN / PRIVATE                           │
├─────────────────────────────────────────────────────────────┤
│  Admin Dashboard (Inertia.js + Vue 3, protected route)      │
│  ├── /admin (dashboard overview)                            │
│  ├── /admin/profile (kelola profil & bio)                   │
│  ├── /admin/projects (CRUD proyek)                          │
│  ├── /admin/skills (CRUD keahlian & kategori)               │
│  ├── /admin/experiences (CRUD pengalaman kerja)             │
│  ├── /admin/certificates (CRUD + upload sertifikat)         │
│  ├── /admin/cv (upload & ganti CV)                          │
│  ├── /admin/stats (hero stats: proyek, semester, pengalaman)│
│  ├── /admin/social-links (kelola email, GitHub, LinkedIn,   │
│  │                        WhatsApp)                         │
│  └── /admin/messages (lihat & kelola pesan kontak)          │
├─────────────────────────────────────────────────────────────┤
│                    BACKEND API                               │
├─────────────────────────────────────────────────────────────┤
│  Laravel 11 (API + Server-Side Rendering via Inertia)       │
│  ├── Authentication: Laravel Breeze (session-based)         │
│  ├── File Storage: Laravel Storage (local/S3)               │
│  ├── Database: MySQL / PostgreSQL                           │
│  └── Email: Laravel Mail (kontak form notification)         │
└─────────────────────────────────────────────────────────────┘
```

---

## 4. Entitas Data (Database Schema)

### 4.1 Profile (1 record — singleton)

```
id, name, tagline, bio, photo_path, email,
university, major, semester,
meta_title, meta_description, updated_at
```

### 4.2 Stats (1 record — singleton, untuk hero section)

```
id, projects_count, semesters_count, experiences_count, updated_at
```

### 4.3 Skills

```
id, category_number (01/02/03), category_label,
category_title, tags (JSON array), display_order, is_active, timestamps
```

Contoh tags: `["OSINT", "DNS Enumeration", "Nmap", "Python"]`

### 4.4 Experiences (Timeline)

```
id, period (contoh: "Jan 2024 – Mar 2024"), role,
organization, description, display_order, is_active, timestamps
```

### 4.5 Projects

```
id, title, description, tags (JSON array),
repo_url (nullable), demo_url (nullable),
repo_status (enum: available|coming_soon|private),
image_path (nullable), is_featured, display_order,
is_active, timestamps
```

### 4.6 Certificates

```
id, title, issuer, issued_date, credential_url (nullable),
file_path (nullable — PDF atau gambar sertifikat),
image_path (nullable — preview thumbnail),
is_active, display_order, timestamps
```

### 4.7 CV

```
id, file_path, original_filename, file_size,
is_active (hanya satu yang aktif), uploaded_at
```

### 4.8 Social Links

```
id, platform (email|github|linkedin|whatsapp),
label, url, display_order, is_active, timestamps
```

### 4.9 Messages (kontak form)

```
id, name, email, message, is_read,
ip_address, user_agent, created_at
```

### 4.10 Admin (single user)

```
id, name, email, password, remember_token, timestamps
```
(dari Laravel default users table)

---

## 5. Fitur per Modul Admin

### 5.1 Dashboard Overview

```
Tampilkan:
  - Total pesan masuk (badge: berapa yang belum dibaca)
  - Total proyek aktif
  - Total sertifikat
  - Total view portfolio (opsional: simpan di simple counter)
  - Pesan terbaru (5 terakhir) dengan link ke modul messages
  - Quick actions: Upload CV baru, Tambah proyek, Tambah sertifikat
```

### 5.2 Profil & Bio

```
Form untuk edit:
  - Nama lengkap
  - Tagline (Cybersecurity Enthusiast & API Integration Specialist)
  - Bio (paragraf panjang)
  - Foto profil (upload, preview, ganti)
  - Universitas, jurusan, semester
  - Meta title & description (untuk SEO)
```

### 5.3 Hero Stats

```
Form simpel untuk update tiga angka di hero section:
  - Proyek Dibangun
  - Semester Berjalan
  - Pengalaman Kerja
```

### 5.4 Keahlian

```
CRUD per kategori:
  - Nomor kategori (01, 02, 03, dst)
  - Label kategori (Pengumpulan Informasi, Pengamanan Sistem, dll)
  - Judul (Reconnaissance, Network Security, dll)
  - Tags: list pill yang bisa ditambah/hapus dinamis
  - Urutan tampil
  - Aktif/nonaktif toggle
```

### 5.5 Pengalaman (Timeline)

```
CRUD per entry:
  - Periode (teks bebas: "Jan 2024 – Mar 2024")
  - Role / jabatan
  - Organisasi / perusahaan
  - Deskripsi singkat
  - Urutan tampil (untuk kontrol urutan timeline)
  - Aktif/nonaktif toggle
```

### 5.6 Proyek

```
CRUD per proyek:
  - Judul proyek
  - Deskripsi
  - Tags (seperti: Python, Nmap, DNS Enum)
  - URL repository (opsional)
  - URL demo (opsional)
  - Status repo: available | coming_soon | private
  - Gambar/screenshot (upload opsional)
  - Featured toggle (tampil di urutan teratas)
  - Urutan tampil
  - Aktif/nonaktif toggle
```

### 5.7 Sertifikat

```
CRUD per sertifikat:
  - Judul sertifikat
  - Penerbit (issuer)
  - Tanggal diterbitkan
  - URL credential (opsional — link ke Credly/Coursera/dll)
  - Upload file sertifikat (PDF atau gambar)
  - Upload thumbnail/preview (gambar)
  - Urutan tampil
  - Aktif/nonaktif toggle
```

### 5.8 CV

```
Upload & kelola CV:
  - Upload file PDF
  - Preview nama file dan ukuran
  - Tandai CV mana yang aktif (hanya satu yang jadi download link)
  - History upload (bisa simpan versi lama)
  - Tombol download untuk test
```

### 5.9 Social Links

```
Manage link kontak:
  - Platform: Email, GitHub, LinkedIn, WhatsApp, dan tambahan lain
  - Label (teks yang tampil)
  - URL / nilai (untuk email = alamat email, WA = nomor)
  - Aktif/nonaktif per platform
  - Urutan tampil
```

### 5.10 Pesan Kontak

```
Inbox pesan:
  - Tabel list pesan (nama, email, preview pesan, tanggal, status baca)
  - Klik → modal atau halaman detail pesan penuh
  - Tandai sudah dibaca / belum dibaca
  - Hapus pesan
  - Badge jumlah belum dibaca di sidebar admin
  - (Opsional) Balas langsung via email dari dashboard
```

---

## 6. Public Portfolio — Mapping dari HTML Asal

Semua section dari HTML asal harus ter-render dengan data dari database:

| Section HTML | Sumber Data |
|---|---|
| `#hero` — nama, tagline, bio | `profiles` + `stats` |
| `#tentang` — bio, 4 info card | `profiles` |
| `#keahlian` — 3 kategori + pills | `skills` |
| `#pengalaman` — timeline | `experiences` |
| `#proyek` — project cards | `projects` |
| `#kontak` — form + social links | `social_links` + `messages` store |
| Footer — nama, navigasi, skill tags | `profiles` + `skills` |

---

## 7. Non-Functional Requirements

```
SECURITY
  [ ] Admin route diproteksi middleware auth Laravel
  [ ] CSRF protection aktif di semua form
  [ ] File upload: validasi MIME type, max size, virus scan (opsional)
  [ ] Rate limiting di form kontak (max 5 submit per IP per jam)
  [ ] Sanitasi input sebelum disimpan ke database

PERFORMANCE
  [ ] Gambar di-optimize saat upload (via Intervention Image)
  [ ] Cache halaman public portfolio (Laravel cache)
  [ ] Lazy loading gambar di frontend
  [ ] CSS dan JS di-minify di production build

SEO
  [ ] Meta title dan description dari database (profil)
  [ ] Open Graph tags untuk social sharing
  [ ] Sitemap.xml (opsional)
  [ ] Canonical URL

UX ADMIN
  [ ] Konfirmasi sebelum hapus (modal confirm)
  [ ] Toast notification setelah setiap aksi (success/error)
  [ ] Preview konten sebelum publish
  [ ] Responsive admin panel (bisa kelola dari mobile)
```

---

## 8. Tech Stack Final

```
Backend
  Framework   : Laravel 11
  PHP         : >= 8.2
  Database    : MySQL 8 atau PostgreSQL 15
  Auth        : Laravel Breeze (session-based)
  File Storage: Laravel Storage (local atau S3-compatible)
  Email       : Laravel Mail + SMTP / Mailtrap (dev)
  Queue       : Database queue (untuk kirim email notifikasi kontak)
  Cache       : File cache atau Redis

Frontend
  Adapter     : Inertia.js v2 (jembatan Laravel ↔ Vue)
  Framework   : Vue 3 (Composition API + <script setup>)
  Build tool  : Vite (sudah built-in di Laravel 11)
  Styling     : Tailwind CSS v3 (config identik dengan HTML asal)
  Icons       : Heroicons atau Lucide Vue (konsisten dengan HTML asal)
  Fonts       : Fraunces, Inter, JetBrains Mono (Google Fonts)

Admin UI Library (opsional tapi recommended)
  Komponen    : shadcn-vue ATAU headlessui/vue (untuk modal, dropdown)
  Table       : TanStack Table Vue (untuk tabel pesan & list admin)
  Form        : VeeValidate + Zod (validasi client-side)
  File upload : vue-filepond atau custom dropzone

Development Tools
  Seeder      : Laravel Seeder (untuk data awal dari HTML asal)
  Testing     : PHPUnit (basic) + Pest (recommended)
  Linting     : ESLint + Prettier (frontend)
```

---

## 9. Scope & Out of Scope

### Dalam Scope

```
✅ Portfolio public satu halaman (semua section dari HTML asal)
✅ Admin dashboard dengan auth
✅ CRUD semua entitas (profil, proyek, keahlian, pengalaman, sertifikat)
✅ Upload CV dan ganti kapan saja
✅ Upload sertifikat (file + thumbnail)
✅ Form kontak masuk ke database + email notifikasi
✅ Dashboard overview dengan statistik dasar
✅ Halaman sertifikat publik (/certificates/:id)
✅ Design system identik dengan HTML asal
```

### Diluar Scope (untuk fase berikutnya)

```
❌ Multi-user / role berbeda (cukup satu admin)
❌ Blog / artikel
❌ Komentar pengunjung
❌ Analitik lanjutan (Google Analytics bisa ditambahkan manual)
❌ Multi-bahasa
❌ Dark mode
❌ PWA / mobile app
```
