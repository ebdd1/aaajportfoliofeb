# Project Audit: Portfolio-Febryanus

Generated: 2026-07-16T14:51:00+08:00
Path: /root/mycvebry/portfolio-febryanus
Auditor: gorisetini v2

---

## Executive Summary

Portfolio-Febryanus adalah aplikasi full-stack Laravel + Vue 3 + Inertia.js yang berfungsi dengan baik secara keseluruhan. Tech stack modern (PHP 8.3, Laravel 13, Vue 3.4) dengan deployment ke Railway. Namun ada beberapa area yang perlu perhatian:

**Top Concerns:**
1. **17 failing tests** - Test suite tidak stabil, 10 assertions fail + 7 errors (route missing, table missing)
2. **Massive JS bundle** - 2.8MB uncompressed dengan chunk terbesar 666KB (Index-D1axRjOu.js) - seharusnya <200KB per chunk
3. **No CI/CD pipeline** - Tidak ada GitHub Actions workflow
4. **Large PHP files** - DocumentParserService (431 lines) dan FileValidationService (339 lines) melanggar single responsibility

**Overall Assessment:** Project functional tapi perlu maintenance loop untuk stabilize tests dan optimize performance.

---

## Tech Stack

| Category   | Technology | Version | Notes |
|------------|-----------|---------|-------|
| Framework  | Laravel   | 13.8+   | PHP full-stack framework |
| Language   | PHP       | 8.3+    | Latest stable |
| Frontend   | Vue 3     | 3.4.0   | Composition API |
| State      | Inertia.js| 2.0     | SSR-ready |
| Build      | Vite      | 5.4     | Fast HMR |
| Styling    | Tailwind  | 3.2     | Custom design tokens |
| Database   | SQLite    | local   | PostgreSQL on Railway |
| Testing    | PHPUnit   | 12.5    | 19 test files |
| Linting    | Laravel Pint | 1.27 | PSR-12 compatible |
| Payment    | Pakasir   | -       | Local payment gateway |

---

## Struktur

Directory tree (3 level max):
```
app/
├── Http/
│   ├── Controllers/Admin/ (14 subdirs), Api/, Auth/, Public/
│   └── Middleware/
├── Models/Blog/, Finance/, Product/, User, Profile, Skill, dll.
└── Services/Upload/, PakasirService.php, CartService.php
resources/js/
├── Components/ (33 Vue components)
├── Pages/Admin/ (19 subdirs), Auth/, Public/, User/
└── stores/ (Pinia stores)
```

Entry points: `artisan`, `bootstrap/app.php`, `resources/js/app.js`, `vite.config.js`
Pattern: **MVC dengan Service Layer** (Controllers thin, business logic di Services)
Naming: **camelCase** (JS) / **snake_case** (PHP) / **PascalCase** (Models)

File counts:
- Controllers: ~45
- Models: ~25
- Services: ~12
- Vue Components: 72 pages + 33 reusable components
- Tests: 19 files

---

## Database

ORM: **Eloquent**
Migration files: 27 files

Tabel utama:
| Table | Related to | Notes |
|-------|-----------|-------|
| users | - | Auth + profile |
| profiles | users | Bio, photo, social links |
| skills | - | Skill categories |
| experiences | - | Work history |
| products | categories | Digital products |
| orders | users, products | Purchase records |
| wallets | users | Finance wallets |
| transactions | wallets, categories | Income/expense |
| blog_posts | categories, tags | Content |
| site_settings | - | Configuration |

---

## API

Route count: ~60+ endpoints
Auth method: **Laravel Sanctum** (session-based)
Response format: **Inertia.js** (SSR dengan props)

Sample endpoints:
- `GET /` → Portfolio homepage
- `GET /products` → Product catalog
- `GET /blog` → Blog listing
- `POST /contact` → Contact form (rate limited)
- `/admin/*` → Full CMS (hidden URL)
- `/admin/finance/*` → Finance module

---

## Testing

Framework: **PHPUnit 12.5**
Test files: 19 files
Coverage: **Tidak terukur**

**Test Status: FAILING**
- Total tests: 99
- Passed: 82
- Failed: 10 assertions + 7 errors = 17 total failures

**Failures breakdown:**
1. 6 tests fail - **route not defined** (`ushome.user`, `user.dashboard`)
2. 4 tests fail - **table empty** (profiles, tidak ada seed data)
3. 1 test fail - **redirect mismatch**

**Errors breakdown:**
1. 1 test error - **no such table: uploads** (Unit test)
2. 6 tests error - **Route not defined**

---

## Infrastructure

CI/CD: **TIDAK ADA** - No GitHub Actions
Docker: **TIDAK ADA** - No Dockerfile
Deployment target: **Railway** (via railway.json)
Environment: **.env.example ada** (65 lines)

Railway config:
```json
{
  "build": { "builder": "NIXPACKS" },
  "deploy": {
    "preDeployCommand": "php artisan migrate --force"
  }
}
```

Issues:
- Tidak ada CI untuk run tests sebelum deploy
- Seeders perlu manual run di Railway

---

## Security Posture

**No critical security vulnerabilities detected.**

- Hardcoded secrets: TIDAK ADA
- CSRF protection: AKTIF
- Rate limiting: ADA (contact form)
- Dependency vulnerabilities: TIDAK ADA (`composer audit` clean)

---

## Code Quality

Linting: **Laravel Pint** available
Type safety: PHPDoc used but inconsistent
TODO/FIXME count: **1** (acceptable)
Files > 500 lines: **2**

**Findings:**

### [MEDIUM] DocumentParserService violates Single Responsibility
- File: `app/Services/Upload/DocumentParserService.php` (431 lines)
- Issue: Mix of PDF parsing, text extraction, CV data mapping
- Recommended: Extract to separate services

### [MEDIUM] FileValidationService too large
- File: `app/Services/Upload/FileValidationService.php` (339 lines)
- Issue: Multiple validation rules in one class
- Recommended: Extract to smaller validators

---

## Accessibility

Frontend: **Vue 3 + Tailwind CSS**

**Findings:**

### [MEDIUM] Images missing alt attributes
- Evidence: Only 1 alt tag in 105 Vue files
- Impact: Screen reader users cannot understand images
- Recommended: Add alt attributes

### [MEDIUM] Heading hierarchy not fully semantic
- Location: `resources/js/Pages/Public/Portfolio.vue`
- Some sections skip heading levels
- Recommended: Audit heading nesting

### [LOW] Interactive elements have aria-labels
- Evidence: CartDrawer, Navigation have proper aria-label
- Status: Good practice

---

## Bundle Size + Performance

**WARNING: Bundle size exceeds best practices**

| Chunk | Size | Gzipped | Target |
|-------|------|---------|--------|
| Index-D1axRjOu.js | 666 KB | 189 KB | <100 KB |
| apexcharts.ssr.esm | 518 KB | 140 KB | lazy load |
| Editor-BBC2BKWM.js | 418 KB | 131 KB | lazy load |
| app-BJv87dGi.js | 305 KB | 107 KB | baseline OK |

**Total JS: 2.8 MB uncompressed, ~700 KB gzipped**

**Findings:**

### [HIGH] Chunk too large (666 KB)
- Impact: Slow initial load, especially mobile
- Recommended: Code-splitting dengan dynamic import

### [HIGH] TipTap Editor loaded on all pages
- File: `Editor-BBC2BKWM.js` (418 KB)
- Impact: Wasteful jika user tidak edit content
- Recommended: Lazy load hanya di edit pages

### [HIGH] ApexCharts loaded globally
- File: `apexcharts.ssr.esm` (518 KB)
- Impact: Charts hanya dipakai di dashboard
- Recommended: Dynamic import hanya di dashboard

---

## Prioritized Action Plan

### Kritis (harus segera)

1. **[HIGH] Fix failing tests - 16 broken tests (pre-existing issues)**
   - 6 tests: Missing routes (`ushome.user`, `user.dashboard`)
   - 5 tests: Missing seed data for `profiles` table
   - 1 test: Redirect mismatch in RegistrationTest
   - Recommended: `/gowork --fix test`
   - **STATUS: PARTIALLY FIXED** - FileValidationService tests fixed (4 tests now passing)

2. **[HIGH] Setup CI/CD pipeline**
   - Missing GitHub Actions
   - Recommended: `/devops-engineer`

### Penting (minggu ini)

3. **[HIGH] Optimize JS bundle size**
   - Lazy load TipTap, ApexCharts
   - Code-split oversized chunks
   - Recommended: `/gopolish`

4. **[MEDIUM] Refactor large service files - ✅ COMPLETED**
   - DocumentParserService: 431 → 67 lines (-84%)
   - FileValidationService: 339 → 107 lines (-68%)
   - Extracted 9 new single-responsibility services:
     - Parsers: PdfParserService, DocxParserService, CvDataMapper
     - Validators: FileTypeValidator, MagicNumberValidator, FileSizeValidator, DuplicateValidator, ImageValidator, DocumentValidator
   - **Recommended: DONE**

### Perlu perhatian (bulan ini)

5. **[MEDIUM] Add missing alt attributes**
   - Audit all images
   - Recommended: `/gopolish`

6. **[MEDIUM] Setup type checking**
   - Setup PHPStan
   - Recommended: `/gowork`

### Nice to have (nanti)

7. **[LOW] Add comprehensive test coverage**
   - Current: 83 passing tests
   - Target: 80%+ coverage

---

## Rekomendasi Skill Sequence

1. `/gowork --fix test` - Fix 17 failing tests
2. `/devops-engineer` - Setup CI/CD dengan test gate
3. `/gopolish` - Optimize bundle + accessibility
4. `/gowork --refactor` - Extract large services
5. `/goarch` - Future architecture improvements

---

## ASSUMPTION

- **Test coverage**: Tidak terukur karena coverage tool belum dijalankan
- **Database on Railway**: CLAUDE.md mention PostgreSQL tapi stack.json SQLite
- **Seeders**: CategorySeeder registered tapi perlu manual execution di Railway
