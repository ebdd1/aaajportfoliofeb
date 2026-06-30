# MGRCAO — Backend Implementation Framework
## Laravel 11 — Migrations · Models · Controllers · Seeders · Mail
### Portfolio Fullstack Febryanus Tambing

---

# M — Mindset

Setiap komponen backend yang dibangun di sini harus mematuhi:

```
1. Setiap migration = satu tabel, satu concern
2. Setiap model punya: fillable, casts, relationships, accessor yang relevan
3. Setiap controller punya: proper request validation, tidak ada logic di controller
   yang seharusnya ada di service/model
4. Seeder = data nyata dari HTML asal, bukan lorem ipsum
5. Tidak ada dd(), var_dump(), atau debug code yang tertinggal
6. Response Inertia selalu pass data yang MINIMAL dibutuhkan view
   (bukan pass seluruh model dengan eager loading berlebihan)
```

---

## Phase 1 — Migrations

### 1.1 Profiles Table

```bash
php artisan make:migration create_profiles_table
```

```php
// database/migrations/xxxx_create_profiles_table.php
Schema::create('profiles', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('tagline');
    $table->text('bio');
    $table->string('photo_path')->nullable();
    $table->string('email');
    $table->string('university')->default('Universitas Cokroaminoto Palopo');
    $table->string('major')->default('Informatika');
    $table->string('semester')->default('4');
    $table->string('meta_title')->nullable();
    $table->text('meta_description')->nullable();
    $table->timestamps();
});
```

### 1.2 Stats Table

```bash
php artisan make:migration create_stats_table
```

```php
Schema::create('stats', function (Blueprint $table) {
    $table->id();
    $table->unsignedInteger('projects_count')->default(3);
    $table->unsignedInteger('semesters_count')->default(4);
    $table->unsignedInteger('experiences_count')->default(3);
    $table->timestamps();
});
```

### 1.3 Skills Table

```bash
php artisan make:migration create_skills_table
```

```php
Schema::create('skills', function (Blueprint $table) {
    $table->id();
    $table->string('category_number', 10);   // "01", "02", "03"
    $table->string('category_label');        // "Pengumpulan Informasi"
    $table->string('category_title');        // "Reconnaissance"
    $table->json('tags');                    // ["OSINT", "Nmap", ...]
    $table->unsignedTinyInteger('display_order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### 1.4 Experiences Table

```bash
php artisan make:migration create_experiences_table
```

```php
Schema::create('experiences', function (Blueprint $table) {
    $table->id();
    $table->string('period');       // "Jan 2024 – Mar 2024"
    $table->string('role');
    $table->string('organization');
    $table->text('description')->nullable();
    $table->unsignedTinyInteger('display_order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### 1.5 Projects Table

```bash
php artisan make:migration create_projects_table
```

```php
Schema::create('projects', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description');
    $table->json('tags');                    // ["Python", "Nmap", "DNS Enum"]
    $table->string('repo_url')->nullable();
    $table->string('demo_url')->nullable();
    $table->enum('repo_status', ['available', 'coming_soon', 'private'])
          ->default('coming_soon');
    $table->string('image_path')->nullable();
    $table->boolean('is_featured')->default(false);
    $table->unsignedTinyInteger('display_order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### 1.6 Certificates Table

```bash
php artisan make:migration create_certificates_table
```

```php
Schema::create('certificates', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('issuer');
    $table->date('issued_date')->nullable();
    $table->string('credential_url')->nullable();
    $table->string('file_path')->nullable();     // PDF sertifikat
    $table->string('image_path')->nullable();    // Thumbnail preview
    $table->unsignedTinyInteger('display_order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### 1.7 CVs Table

```bash
php artisan make:migration create_cvs_table
```

```php
Schema::create('cvs', function (Blueprint $table) {
    $table->id();
    $table->string('file_path');
    $table->string('original_filename');
    $table->unsignedBigInteger('file_size');   // bytes
    $table->boolean('is_active')->default(false);
    $table->timestamps();
});
```

### 1.8 Social Links Table

```bash
php artisan make:migration create_social_links_table
```

```php
Schema::create('social_links', function (Blueprint $table) {
    $table->id();
    $table->string('platform');   // email, github, linkedin, whatsapp
    $table->string('label');
    $table->string('url');
    $table->unsignedTinyInteger('display_order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### 1.9 Messages Table

```bash
php artisan make:migration create_messages_table
```

```php
Schema::create('messages', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email');
    $table->text('message');
    $table->boolean('is_read')->default(false);
    $table->string('ip_address', 45)->nullable();
    $table->text('user_agent')->nullable();
    $table->timestamps();
});
```

### Jalankan Semua Migration

```bash
php artisan migrate
```

---

## Phase 2 — Models

### 2.1 Profile Model

```bash
php artisan make:model Profile
```

```php
// app/Models/Profile.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'name', 'tagline', 'bio', 'photo_path', 'email',
        'university', 'major', 'semester',
        'meta_title', 'meta_description',
    ];

    // Ambil satu-satunya record profil
    public static function getSingleton(): self
    {
        return self::firstOrCreate(['id' => 1], [
            'name'     => 'Febryanus Tambing',
            'tagline'  => 'Cybersecurity Enthusiast & API Integration Specialist',
            'bio'      => '',
            'email'    => 'febryanustambing54@gmail.com',
        ]);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path
            ? asset('storage/' . $this->photo_path)
            : null;
    }
}
```

### 2.2 Stat Model

```bash
php artisan make:model Stat
```

```php
// app/Models/Stat.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    protected $fillable = [
        'projects_count', 'semesters_count', 'experiences_count',
    ];

    protected $casts = [
        'projects_count'    => 'integer',
        'semesters_count'   => 'integer',
        'experiences_count' => 'integer',
    ];

    public static function getSingleton(): self
    {
        return self::firstOrCreate(['id' => 1], [
            'projects_count'    => 3,
            'semesters_count'   => 4,
            'experiences_count' => 3,
        ]);
    }
}
```

### 2.3 Skill Model

```bash
php artisan make:model Skill
```

```php
// app/Models/Skill.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = [
        'category_number', 'category_label', 'category_title',
        'tags', 'display_order', 'is_active',
    ];

    protected $casts = [
        'tags'          => 'array',
        'is_active'     => 'boolean',
        'display_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('display_order');
    }
}
```

### 2.4 Experience Model

```bash
php artisan make:model Experience
```

```php
// app/Models/Experience.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    protected $fillable = [
        'period', 'role', 'organization', 'description',
        'display_order', 'is_active',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'display_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('display_order');
    }
}
```

### 2.5 Project Model

```bash
php artisan make:model Project
```

```php
// app/Models/Project.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title', 'description', 'tags', 'repo_url', 'demo_url',
        'repo_status', 'image_path', 'is_featured',
        'display_order', 'is_active',
    ];

    protected $casts = [
        'tags'          => 'array',
        'is_featured'   => 'boolean',
        'is_active'     => 'boolean',
        'display_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->orderByDesc('is_featured')
                     ->orderBy('display_order');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path
            ? asset('storage/' . $this->image_path)
            : null;
    }

    public function getRepoStatusLabelAttribute(): string
    {
        return match($this->repo_status) {
            'available'    => 'Lihat Repo',
            'coming_soon'  => 'Repo Segera Tersedia',
            'private'      => 'Repo Private',
            default        => 'Repo Segera Tersedia',
        };
    }
}
```

### 2.6 Certificate Model

```bash
php artisan make:model Certificate
```

```php
// app/Models/Certificate.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'title', 'issuer', 'issued_date', 'credential_url',
        'file_path', 'image_path', 'display_order', 'is_active',
    ];

    protected $casts = [
        'issued_date'   => 'date',
        'is_active'     => 'boolean',
        'display_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('display_order');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path
            ? asset('storage/' . $this->image_path)
            : null;
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path
            ? asset('storage/' . $this->file_path)
            : null;
    }
}
```

### 2.7 Cv Model

```bash
php artisan make:model Cv
```

```php
// app/Models/Cv.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    protected $table = 'cvs';

    protected $fillable = [
        'file_path', 'original_filename', 'file_size', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
    ];

    public static function getActive(): ?self
    {
        return self::where('is_active', true)->latest()->first();
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
```

### 2.8 Social Link & Message Model

```bash
php artisan make:model SocialLink
php artisan make:model Message
```

```php
// app/Models/SocialLink.php
class SocialLink extends Model
{
    protected $fillable = ['platform', 'label', 'url', 'display_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'display_order' => 'integer'];
    public function scopeActive($query) {
        return $query->where('is_active', true)->orderBy('display_order');
    }
}

// app/Models/Message.php
class Message extends Model
{
    protected $fillable = ['name', 'email', 'message', 'is_read', 'ip_address', 'user_agent'];
    protected $casts = ['is_read' => 'boolean'];
    public function scopeUnread($query) { return $query->where('is_read', false); }
}
```

---

## Phase 3 — Seeders (Data dari HTML Asal)

### 3.1 DatabaseSeeder

```php
// database/seeders/DatabaseSeeder.php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            ProfileSeeder::class,
            StatsSeeder::class,
            SkillSeeder::class,
            ExperienceSeeder::class,
            ProjectSeeder::class,
            SocialLinkSeeder::class,
        ]);
    }
}
```

### 3.2 Admin User Seeder

```bash
php artisan make:seeder AdminUserSeeder
```

```php
// database/seeders/AdminUserSeeder.php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'febryanustambing54@gmail.com'],
            [
                'name'     => 'Febryanus Tambing',
                'password' => Hash::make('password'),  // GANTI sebelum production!
            ]
        );
    }
}
```

### 3.3 Profile Seeder

```bash
php artisan make:seeder ProfileSeeder
```

```php
// database/seeders/ProfileSeeder.php
<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        Profile::updateOrCreate(['id' => 1], [
            'name'    => 'Febryanus Tambing',
            'tagline' => 'Cybersecurity Enthusiast & API Integration Specialist',
            'bio'     => 'Mahasiswa Informatika semester 4 yang mendalami reconnaissance, '
                       . 'network security, dan post-exploitation — sambil membangun '
                       . 'pengalaman nyata lewat integrasi API dan proyek keamanan siber dari nol.',
            'email'      => 'febryanustambing54@gmail.com',
            'university' => 'Universitas Cokroaminoto Palopo',
            'major'      => 'Informatika',
            'semester'   => '4',
            'meta_title' => 'Febryanus Tambing — Cybersecurity Enthusiast & API Integration Specialist',
            'meta_description' => 'Portfolio Febryanus Tambing, mahasiswa Informatika UNCP, '
                                . 'fokus reconnaissance, network security, dan post-exploitation.',
        ]);
    }
}
```

### 3.4 Stats Seeder

```bash
php artisan make:seeder StatsSeeder
```

```php
// database/seeders/StatsSeeder.php
use App\Models\Stat;

Stat::updateOrCreate(['id' => 1], [
    'projects_count'    => 3,
    'semesters_count'   => 4,
    'experiences_count' => 3,
]);
```

### 3.5 Skill Seeder (Data dari HTML asal)

```bash
php artisan make:seeder SkillSeeder
```

```php
// database/seeders/SkillSeeder.php
<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            [
                'category_number' => '01',
                'category_label'  => 'Pengumpulan Informasi',
                'category_title'  => 'Reconnaissance',
                'tags'            => ['OSINT', 'DNS Enumeration', 'Nmap', 'Python'],
                'display_order'   => 1,
            ],
            [
                'category_number' => '02',
                'category_label'  => 'Pengamanan Sistem',
                'category_title'  => 'Network Security',
                'tags'            => ['Firewall', 'IDS/IPS', 'VPN', 'Wireshark'],
                'display_order'   => 2,
            ],
            [
                'category_number' => '03',
                'category_label'  => 'Simulasi Serangan',
                'category_title'  => 'Post-Exploitation',
                'tags'            => ['Havoc C2', 'XOR Cipher', 'C#/.NET', 'Payload Dev'],
                'display_order'   => 3,
            ],
        ];

        foreach ($skills as $skill) {
            Skill::updateOrCreate(
                ['category_number' => $skill['category_number']],
                array_merge($skill, ['is_active' => true])
            );
        }
    }
}
```

### 3.6 Project Seeder

```bash
php artisan make:seeder ProjectSeeder
```

```php
// database/seeders/ProjectSeeder.php
<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'title'         => 'Password Manager CLI',
                'description'   => 'Password manager berbasis command-line yang mengenkripsi '
                                 . 'dan mengelola kredensial secara lokal menggunakan Python '
                                 . 'dengan enkripsi AES.',
                'tags'          => ['Python', 'AES', 'CLI'],
                'repo_status'   => 'coming_soon',
                'is_featured'   => true,
                'display_order' => 1,
            ],
            [
                'title'         => 'Domain Scanner Tool',
                'description'   => 'Tool reconnaissance untuk memetakan informasi domain target '
                                 . 'melalui DNS enumeration dan pemindaian port dengan Nmap.',
                'tags'          => ['Python', 'Nmap', 'DNS Enum'],
                'repo_status'   => 'coming_soon',
                'is_featured'   => false,
                'display_order' => 2,
            ],
            [
                'title'         => 'ZeroFive Encryption Tool',
                'description'   => 'Aplikasi desktop berbasis Windows Forms untuk enkripsi dan '
                                 . 'dekripsi data menggunakan XOR cipher, dibangun di atas .NET.',
                'tags'          => ['C#', '.NET', 'XOR Cipher'],
                'repo_status'   => 'coming_soon',
                'is_featured'   => false,
                'display_order' => 3,
            ],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(
                ['title' => $project['title']],
                array_merge($project, ['is_active' => true])
            );
        }
    }
}
```

### 3.7 Social Link Seeder

```bash
php artisan make:seeder SocialLinkSeeder
```

```php
// database/seeders/SocialLinkSeeder.php
<?php

namespace Database\Seeders;

use App\Models\SocialLink;
use Illuminate\Database\Seeder;

class SocialLinkSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            ['platform' => 'email',    'label' => 'Email',    'url' => 'mailto:febryanustambing54@gmail.com', 'display_order' => 1],
            ['platform' => 'github',   'label' => 'GitHub',   'url' => 'https://github.com/febryanustambing', 'display_order' => 2],
            ['platform' => 'linkedin', 'label' => 'LinkedIn', 'url' => 'https://linkedin.com/in/febryanustambing', 'display_order' => 3],
            ['platform' => 'whatsapp', 'label' => 'WhatsApp', 'url' => 'https://wa.me/62', 'display_order' => 4],
        ];

        foreach ($links as $link) {
            SocialLink::updateOrCreate(
                ['platform' => $link['platform']],
                array_merge($link, ['is_active' => true])
            );
        }
    }
}
```

---

## Phase 4 — Controllers

### 4.1 Public Portfolio Controller

```bash
php artisan make:controller Public/PortfolioController
```

```php
// app/Http/Controllers/Public/PortfolioController.php
<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactMessageRequest;
use App\Mail\NewContactMessage;
use App\Models\Certificate;
use App\Models\Cv;
use App\Models\Experience;
use App\Models\Message;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use App\Models\SocialLink;
use App\Models\Stat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PortfolioController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Public/Portfolio', [
            'profile'      => Profile::getSingleton(),
            'stats'        => Stat::getSingleton(),
            'skills'       => Skill::active()->get(),
            'experiences'  => Experience::active()->get(),
            'projects'     => Project::active()->get(),
            'certificates' => Certificate::active()->get(),
            'socialLinks'  => SocialLink::active()->get(),
            'hasActiveCv'  => Cv::getActive() !== null,
        ]);
    }

    public function sendMessage(ContactMessageRequest $request)
    {
        $message = Message::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'message'    => $request->message,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Kirim notifikasi email ke admin (via queue)
        Mail::to(config('mail.admin_notification_email', env('ADMIN_NOTIFICATION_EMAIL')))
            ->queue(new NewContactMessage($message));

        return back()->with('success', 'Pesan berhasil terkirim. Terima kasih!');
    }

    public function downloadCv(): BinaryFileResponse
    {
        $cv = Cv::getActive();

        abort_unless($cv, 404, 'CV tidak tersedia saat ini.');

        return response()->download(
            storage_path('app/public/' . $cv->file_path),
            $cv->original_filename
        );
    }

    public function showCertificate(Certificate $certificate): Response
    {
        abort_unless($certificate->is_active, 404);

        return Inertia::render('Public/Certificate', [
            'certificate' => $certificate,
        ]);
    }
}
```

### 4.2 Admin Dashboard Controller

```bash
php artisan make:controller Admin/DashboardController
```

```php
// app/Http/Controllers/Admin/DashboardController.php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Message;
use App\Models\Project;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_messages'   => Message::count(),
                'unread_messages'  => Message::unread()->count(),
                'total_projects'   => Project::where('is_active', true)->count(),
                'total_certs'      => Certificate::where('is_active', true)->count(),
            ],
            'recentMessages' => Message::latest()
                ->take(5)
                ->get(['id', 'name', 'email', 'message', 'is_read', 'created_at']),
        ]);
    }
}
```

### 4.3 Contact Message Request

```bash
php artisan make:request ContactMessageRequest
```

```php
// app/Http/Requests/ContactMessageRequest.php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactMessageRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'min:2', 'max:100'],
            'email'   => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Nama wajib diisi.',
            'name.min'         => 'Nama minimal 2 karakter.',
            'email.required'   => 'Email wajib diisi.',
            'email.email'      => 'Format email tidak valid.',
            'message.required' => 'Pesan wajib diisi.',
            'message.min'      => 'Pesan minimal 10 karakter.',
        ];
    }
}
```

---

## Phase 5 — Mail

```bash
php artisan make:mail NewContactMessage --markdown=emails.contact.new
```

```php
// app/Mail/NewContactMessage.php
<?php

namespace App\Mail;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewContactMessage extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Message $contactMessage) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pesan baru dari {$this->contactMessage->name} via Portfolio",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact.new',
        );
    }
}
```

```markdown
{{-- resources/views/emails/contact/new.blade.php --}}
@component('mail::message')
# Pesan Baru dari Portfolio

**Dari:** {{ $contactMessage->name }}
**Email:** {{ $contactMessage->email }}
**Waktu:** {{ $contactMessage->created_at->format('d M Y H:i') }}

---

**Pesan:**

{{ $contactMessage->message }}

---

@component('mail::button', ['url' => url('/admin/messages/' . $contactMessage->id), 'color' => 'red'])
Lihat di Dashboard
@endcomponent

© {{ date('Y') }} Portfolio Febryanus Tambing
@endcomponent
```

---

## Phase 6 — Rate Limiting untuk Form Kontak

```php
// app/Providers/AppServiceProvider.php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

public function boot(): void
{
    RateLimiter::for('contact', function (Request $request) {
        return Limit::perHour(5)->by($request->ip())
            ->response(function () {
                return back()->withErrors([
                    'message' => 'Terlalu banyak pesan dikirim. Coba lagi dalam satu jam.'
                ]);
            });
    });
}
```

```php
// routes/web.php — update route kontak
Route::post('/contact', [PortfolioController::class, 'sendMessage'])
    ->name('contact.send')
    ->middleware('throttle:contact');
```

---

## Phase 7 — Queue Setup

```bash
# Buat tabel queue di database
php artisan queue:table
php artisan migrate

# Jalankan queue worker di development
php artisan queue:work

# Di production (Railway/VPS): gunakan Supervisor atau
# Railway cron job untuk menjalankan queue:work secara persisten
```

---

## Phase 8 — Final Check Backend

```bash
# Jalankan fresh migration + seeder
php artisan migrate:fresh --seed

# Verifikasi routes terdaftar
php artisan route:list --path=admin

# Clear cache jika ada perubahan config
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Jalankan server
php artisan serve
```

### Checklist Backend Selesai

```
[ ] php artisan migrate:fresh --seed berjalan tanpa error
[ ] Login ke /login berhasil dengan email & password dari seeder
[ ] GET / menampilkan data dari database (bukan hardcoded)
[ ] POST /contact menyimpan pesan ke tabel messages
[ ] Email notifikasi masuk ke Mailtrap (dev) setelah submit form
[ ] GET /admin/dashboard menampilkan stats yang benar
[ ] GET /admin/messages menampilkan daftar pesan
[ ] GET /cv/download mendownload CV aktif (setelah upload manual pertama)
[ ] Semua admin route redirect ke /login jika belum auth
[ ] Route throttle:contact aktif (coba submit 6x dari IP yang sama)
```
