# CLAUDE.md — Portfolio-Febryanus
## Febryanus Tambing Portfolio
## MGRCAO Persistent Context — Dibaca Otomatis Setiap Sesi

---

> **INSTRUKSI UNTUK MODEL:**
> File ini adalah sumber kebenaran tunggal untuk project Portfolio-Febryanus.
> Baca seluruh file ini SEBELUM melakukan atau menyarankan apapun.
> Jangan override rules di sini dengan asumsi dari training data umum.
> Jika ada konflik antara file ini dan "cara biasa" — file ini menang.

---

# BAGIAN 1 — IDENTITAS PROJECT

```
Nama Project : Portfolio-Febryanus
Owner        : Febryanus Tambing
Purpose      : Personal Portfolio + Digital Products Store + Finance Tracker
Target       : Recruiter/Client + Customer + Self (finance management)
Bahasa       : Bahasa Indonesia (konten) + PHP/Vue (kode)
```

---

# BAGIAN 2 — TECH STACK

```
BACKEND
  Language    : PHP 8.3+
  Framework   : Laravel 13
  ORM         : Eloquent
  Database    : SQLite

FRONTEND
  Framework   : Vue 3 + Inertia.js 2.0
  Build Tool  : Vite 8
  Styling     : Tailwind CSS 3.2
  Icons       : Heroicons (Vue)

AUTH
  Package     : Laravel Breeze
  API Token   : Laravel Sanctum (ready)

PAYMENT
  Gateway     : Pakasir (via PakasirService.php)
  Webhook     : /api/pakasir/webhook
```

---

# BAGIAN 3 — FITUR PROJECT

```
1. PORTFOLIO WEBSITE
   - Hero section dengan stats
   - About section
   - Skills grid dengan categories
   - Experience timeline
   - Projects showcase
   - Certificates dengan verify link
   - Contact form
   - CV download

2. DIGITAL PRODUCTS STORE
   - Product catalog
   - Checkout via Pakasir
   - Purchase history
   - Product download
   - Order management

3. PERSONAL FINANCE MANAGER
   - Multiple wallets
   - Income/expense tracking
   - Transaction categories
   - Transfers antar wallet
   - Budget planning
   - Savings goals
   - Invoice generation (PDF)

4. ADMIN CMS
   - Dashboard overview
   - Portfolio management (skills, projects, experience, certificates)
   - CV management
   - Social links
   - Site settings
   - Messages inbox
   - Finance module (full CRUD)
   - Products management
```

---

# BAGIAN 4 — DIRECTORY STRUCTURE

```
portfolio-febryanus/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin controllers
│   │   │   │   ├── Finance/    # Finance module
│   │   │   │   └── Product/    # Product module
│   │   │   ├── Auth/           # Breeze auth
│   │   │   └── Public/         # Public pages
│   │   ├── Middleware/
│   │   │   ├── EnsureUserIsAdmin.php
│   │   │   └── HandleInertiaRequests.php
│   │   └── Requests/           # Form validation
│   ├── Models/
│   │   ├── Finance/            # Wallet, Transaction, dll
│   │   ├── Product/            # Product, Order, dll
│   │   └── User, Profile, Project, Skill, dll.
│   └── Services/
│       └── PakasirService.php  # Payment integration
├── resources/
│   └── js/
│       ├── Components/         # Vue reusable components
│       ├── Layouts/             # Guest, Auth, Admin, User
│       └── Pages/              # Page components
│           ├── Admin/
│           ├── Auth/
│           ├── Public/
│           └── Profile/
├── routes/
│   ├── web.php
│   └── auth.php
└── database/
    ├── migrations/
    ├── factories/
    └── seeders/
```

---

# BAGIAN 5 — ROUTES & ENDPOINTS

```
PUBLIC ROUTES
  GET  /                     → Portfolio homepage
  GET  /products            → Product listing
  GET  /products/{slug}      → Product detail
  POST /contact             → Send contact message
  GET  /cv/download         → Download active CV

AUTH ROUTES (Breeze)
  GET/POST /login, /register, /logout
  GET/POST /forgot-password, /reset-password
  GET     /verify-email

CHECKOUT ROUTES (Auth + Verified)
  GET   /checkout/{slug}    → Checkout page
  POST  /checkout/{slug}    → Initiate payment
  GET   /ushome/user       → User purchases
  GET   /download/{order}/{product} → Download product

WEBHOOK
  POST /api/pakasir/webhook → Payment gateway callback

ADMIN ROUTES (Auth + Verified + Admin)
  /admin/*                  → All admin pages
  /admin/finance/*          → Finance module
  /admin/products-v2/*      → Product module
```

---

# BAGIAN 6 — DESIGN SYSTEM

```
CUSTOM COLORS (tailwind.config.js)
  cream              : #FAF8F1
  oat                : #F5F1E8
  oat-dark           : #E8E2D3
  paper              : #FFFFFF
  terracotta         : #C96442 (primary)
  terracotta-dark    : #B5532F
  terracotta-light   : #E0916F
  ink                : #3D3929
  taupe              : #6B6456
  taupe-light        : #9C9484

CUSTOM FONTS
  Serif   : Fraunces
  Sans    : Inter
  Mono    : JetBrains Mono

TYPOGRAPHY RULES
  - Body text: 14px minimum
  - Form input: 16px minimum (cegah zoom iOS)
  - Touch target: 44×44px minimum
  - Responsive: Mobile first (375px → 768px → 1024px)

COMPONENTS
  - Buttons: Primary (terracotta), Secondary (outlined), Danger
  - Cards: Glass morphism, subtle shadows
  - Forms: Input, Checkbox, Toggle, Select
  - Layouts: Guest, Authenticated, Admin, User
```

---

# BAGIAN 7 — LARAVEL BEST PRACTICES

```
GENERATOR COMMANDS
  php artisan make:controller, make:model, make:migration
  php artisan make:request      → Form validation
  php artisan make:policy      → Authorization
  php artisan make:resource    → API resource
  php artisan make:job, make:mail, make:notification

CODE ORGANIZATION
  - Business logic → Service classes (app/Services/)
  - Validation    → Form Requests (app/Http/Requests/)
  - Authorization → Policies (app/Policies/)
  - Data logic    → Models (app/Models/)

QUERY BUILDER
  - Pakai Eloquent relationships (avoid raw SQL)
  - Use eager loading (with()) untuk N+1 prevention
  - Scope untuk query yang sering digunakan
```

---

# BAGIAN 8 — INERTIA.JS RULES

```
NAVIGATION
  - Pakai Inertia::link() / <Link> untuk internal navigation
  - Pakai Inertia::render() untuk error pages
  - Pakai redirect() bukan header() untuk redirects

DATA SHARING
  - Share data globally → HandleInertiaRequests.php
  - Flash messages → session()->flash()
  - View data → compact() atau with()

FORMS
  - Pakai Inertia form helper (useForm)
  - Handle errors dengan $errors object
  - Preserve state saat validation gagal

MODALS
  - Pakai Modal component
  - Pass data via component props
```

---

# BAGIAN 9 — VUE 3 COMPOSITION API

```
SCRIPT SETUP
  <script setup>
    // Composition API dengan script setup
  </script>

COMPONENT STRUCTURE
  - Reusable → resources/js/Components/
  - Pages    → resources/js/Pages/
  - Layouts  → resources/js/Layouts/

COMMON PATTERNS
  - Props: defineProps<{...}>()
  - Emit: defineEmits<{(...): void }>()
  - Ref: ref(), computed(), watch()
  - Lifecycle: onMounted(), onUnmounted()

ANIMATIONS
  - Intersection Observer untuk scroll reveal
  - Tailwind transition classes
  - Nprogress untuk loading state (terracotta color)
```

---

# BAGIAN 10 — PAKASIR PAYMENT GATEWAY

```
SERVICE FILE
  app/Services/PakasirService.php

METHODS
  - createTransaction() → Buat payment link
  - verifyPayment()     → Verifikasi status
  - handleWebhook()     → Process webhook

WEBHOOK ENDPOINT
  POST /api/pakasir/webhook
  - Verify signature
  - Update order status
  - Handle success/failure

ENV VARIABLES
  PAKASIR_API_KEY=
  PAKASIR_MERCHANT_ID=
  PAKASIR_WEBHOOK_SECRET=

CHECKOUT FLOW
  1. User click checkout → POST /checkout/{slug}
  2. Backend buat transaction via PakasirService
  3. Redirect ke payment page Pakasir
  4. User bayar → Pakasir call webhook
  5. Webhook update order status
  6. User redirect ke success page
  7. User bisa download product
```

---

# BAGIAN 11 — ADMIN RULES

```
ACCESS CONTROL
  Middleware: ['auth', 'verified', 'admin']
  Custom middleware: EnsureUserIsAdmin.php

CRUD PATTERN
  - Controller: index(), create(), store(), show(), edit(), update(), destroy()
  - Form Request untuk validation
  - Policy untuk authorization
  - Soft deletes untuk data penting

ADMIN PAGES LOCATION
  resources/js/Pages/Admin/

FINANCE MODULE
  - Wallets, Transactions, Transfers, Categories
  - Budgets, Savings Goals, Invoices
  - Dashboard dengan summary

PRODUCTS MODULE
  - Catalog, Orders, Purchases
  - Checkout flow, Download management
```

---

# BAGIAN 12 — SECURITY RULES

```
BUILT-IN LARAVEL
  - CSRF protection (CSRF middleware)
  - XSS prevention (Blade escaped output)
  - SQL injection prevention (Eloquent ORM)
  - Mass assignment protection ($fillable)

AUTH SECURITY
  - Password hashing (bcrypt)
  - Rate limiting (throttle middleware)
  - Session invalidation on logout
  - Email verification untuk checkout

FILE UPLOADS
  - Validate file type
  - Validate file size
  - Store outside web root
  - Generate unique filenames

ADMIN SECURITY
  - Admin middleware untuk proteksi routes
  - Whitelist fields untuk update (bukan fillable all)
  - Sanitize input sebelum save
```

---

# BAGIAN 13 — AI SLOP RULES (ZERO TOLERANCE)

```
DEFINISI AI SLOP
Kode yang terlihat lengkap tapi tidak fungsional untuk production.

LARANGAN KERAS
❌ FAKE DATA
   - Hardcoded array untuk testing
   - Fake statistics
   - Data tidak dari database

❌ KOSMETIK COMPONENTS
   - Button tanpa onClick
   - Form tidak submit
   - Card tidak interaktif

❌ FAKE SECURITY
   - Auth guard hanya di client
   - Role dari client state
   - No CSRF protection

❌ DEAD CODE
   - Commented out code
   - Unused imports
   - TODO/FIXME yang tidak resolved

YANG HARUS DILAKUKAN
✅ Data dari Eloquent/TanStack Query
✅ Button dengan handler fungsional
✅ Form dengan validation
✅ Error handling yang proper
✅ Loading, empty, error states
```

---

# BAGIAN 14 — GENERATE CODE CHECKLIST

```
SEBELUM GENERATE COMPONENT
[ ] Loading state? (skeleton/spinner)
[ ] Empty state? (saat data kosong)
[ ] Error state? (saat API gagal)
[ ] Accessible? (aria-label, keyboard nav)
[ ] Responsive? (mobile first)
[ ] Props typed? (TypeScript/prop types)

SEBELUM GENERATE CONTROLLER
[ ] Validate request? (Form Request)
[ ] Auth check? (middleware)
[ ] Authorization? (Policy)
[ ] Whitelist fields? (bukan fillable all)
[ ] Return proper response? (JSON/view)

SEBELUM GENERATE MODEL
[ ] $fillable defined?
[ ] $casts defined?
[ ] Relationships defined?
[ ] Scopes defined?
[ ] Soft deletes jika perlu?
```

---

# BAGIAN 15 — SKILL YANG TERSEDIA

```
INSTALLED
  .agents/skills/laravel-api
  → Laravel API development patterns

LOCAL DOCS
  Tidak ada — cek routes/, app/ untuk patterns
```

---

# BAGIAN 16 — CARA MEMINTA SESUATU

```
TEMPLATE YANG BENAR
Saya mau [deskripsi fitur/task].

Konteks:
- Halaman: [nama halaman]
- Data dari: [model/database/API]
- Auth required: [ya/tidak, role]
- Admin/Self/Customer: [siapa targetnya]

Pastikan:
- Pakai [component pattern] yang ada
- Tidak ada hardcoded data
- Ada loading, empty, error state
- Sesuai design system (Tailwind colors)

CONTOH YANG BENAR
"Buat form untuk add transaction di finance module.
Data: amount, category (dropdown), wallet (dropdown), type (income/expense).
Auth: admin only. Pakai existing Form component."

CONTOH YANG SALAH
"Buatin form transaction"
→ Model akan generate form generic tanpa context
```

---

> **REMINDER UNTUK MODEL:**
> Anda membaca file ini karena Claude Code me-load CLAUDE.md otomatis
> di setiap sesi. Ini berarti semua rules di atas berlaku sejak
> detik pertama sesi ini dibuka — tanpa perlu-ingatkan lagi oleh user.
> Portfolio-Febryanus adalah project LARAVEL + INERTIA.JS + VUE 3 + TAILWIND,
> BUKAN React/Node.js. Pakasir untuk payment, bukan Midtrans/Stripe.

---

## ECC INTEGRATION

ECC (Effective Claude Code) sudah terinstall via plugin.
Beroperasi sebagai lapisan TAMBAHAN di atas rules project ini.

### Hierarki Prioritas Rules

```
PRIORITAS 1 (tertinggi): CLAUDE.md project ini
  → Design system, anti-slop rules, stack spesifik portfolio

PRIORITAS 2: Project-level rules (.claude/rules/ecc/)
  → PHP coding standards, Vue patterns

PRIORITAS 3: Global ECC rules (dari plugin)
  → Coding standards umum

Jika ada konflik antara ECC dan CLAUDE.md → CLAUDE.md menang.
```

### ECC Agents yang Tersedia

```
/ecc:plan           → Planner agent (planning fitur baru)
/ecc:code-review    → Review kode sebelum commit
/ecc:security-scan  → Security audit (komplementer OWASP rules)
/ecc:vue-review     → Vue component review
/ecc:laravel-security → Laravel-specific security check
```

### ECC Skills yang Relevan

```
LARAVEL
  laravel-patterns      → Arsitektur umum Laravel
  laravel-tdd          → TDD workflow dengan Pest
  laravel-security     → Security patterns Laravel
  laravel-verification → Testing verification

VUE & FRONTEND
  vue-patterns         → Vue 3 Composition API patterns
  vite-patterns        → Vite build optimization

WORKFLOW
  tdd-workflow         → TDD methodology
  verification-loop    → Build-test-lint cycle
  security-review      → OWASP checklist generik
```

### Cara Pakai

```
Untuk planning fitur baru:
  /ecc:plan "Tambah fitur export CSV untuk transaksi"

Untuk code review:
  /ecc:code-review

Untuk security check:
  /ecc:security-scan
  atau /ecc:laravel-security

Untuk Vue component review:
  /ecc:vue-review
```

### Yang TIDAK Berubah

```
✅ Design system terracotta/cream/ink tetap berlaku
✅ Anti-AI-slop rules tetap berlaku
✅ Pakasir untuk payment (bukan Midtrans/Stripe)
✅ Laravel + Inertia.js + Vue 3 stack
```
