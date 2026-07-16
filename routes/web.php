<?php

use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\CertificateParserController;
use App\Http\Controllers\Admin\CvController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\StatsController;
use App\Http\Controllers\Admin\Finance\FinanceDashboardController;
use App\Http\Controllers\Admin\Finance\WalletController;
use App\Http\Controllers\Admin\Finance\TransactionController;
use App\Http\Controllers\Admin\Finance\TransferController;
use App\Http\Controllers\Admin\Finance\InvoiceController;
use App\Http\Controllers\Admin\Finance\TransactionCategoryController;
use App\Http\Controllers\Admin\Finance\BudgetController;
use App\Http\Controllers\Admin\Finance\SavingsGoalController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\Product\ProductDashboardController;
use App\Http\Controllers\Admin\Product\ProductController;
use App\Http\Controllers\Admin\Product\ProductOrderController;
use App\Http\Controllers\Admin\Product\RoadmapController;
use App\Http\Controllers\Admin\Product\ProductMetricController;
use App\Http\Controllers\Admin\Blog\BlogPostController;
use App\Http\Controllers\Admin\Blog\BlogCategoryController;
use App\Http\Controllers\Admin\Blog\BlogTagController;
use App\Http\Controllers\Admin\Blog\ImageUploadController;
use App\Http\Controllers\Public\PortfolioController;
use App\Http\Controllers\Public\ProductController as PublicProductController;
use App\Http\Controllers\Public\CheckoutController;
use App\Http\Controllers\Public\DownloadController;
use App\Http\Controllers\Public\BlogController;
use App\Http\Controllers\Public\BlogRssController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Api\PakasirWebhookController;
use Illuminate\Support\Facades\Route;

// ─── PUBLIC ROUTES ────────────────────────────────────────────────────────────
Route::get('/', [PortfolioController::class, 'index'])->name('home');
Route::get('/products', [PublicProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [PublicProductController::class, 'show'])->name('products.show');
Route::post('/contact', [PortfolioController::class, 'sendMessage'])
    ->name('contact.send')
    ->middleware('throttle:contact');
Route::get('/cv/download', [PortfolioController::class, 'downloadCv'])->name('cv.download');
Route::get('/certificates/{certificate}', [PortfolioController::class, 'showCertificate'])
    ->name('certificates.show');

// ─── BLOG ────────────────────────────────────────────────────────────────
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/feed', BlogRssController::class)->name('blog.feed');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// ─── SITEMAP ────────────────────────────────────────────────────────────────
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

// ─── ADMIN AUTH ROUTES (Hidden URL) ─────────────────────────────────────────────
Route::prefix('hyadms/malemologin/ds')
    ->name('admin.login.')
    ->group(function () {
        Route::get('/', [AdminLoginController::class, 'create'])->name('create');
        Route::post('/', [AdminLoginController::class, 'store'])->name('store');
        Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('logout');
    });

// ─── AUTH ROUTES (dari Breeze) ───────────────────────────────────────────────
require __DIR__ . '/auth.php';

// ─── ADMIN ROUTES ─────────────────────────────────────────────────────────────
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Profile & Stats
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');

        Route::get('/stats', [StatsController::class, 'edit'])->name('stats.edit');
        Route::patch('/stats', [StatsController::class, 'update'])->name('stats.update');

        // Skills
        Route::resource('skills', SkillController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::patch('/skills/{skill}/toggle', [SkillController::class, 'toggle'])->name('skills.toggle');

        // Experiences
        Route::resource('experiences', ExperienceController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::patch('/experiences/{experience}/toggle', [ExperienceController::class, 'toggle'])
            ->name('experiences.toggle');

        // Projects
        Route::resource('projects', ProjectController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::patch('/projects/{project}/toggle', [ProjectController::class, 'toggle'])
            ->name('projects.toggle');

        // Certificates
        Route::resource('certificates', CertificateController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::patch('/certificates/{certificate}/toggle', [CertificateController::class, 'toggle'])
            ->name('certificates.toggle');
        Route::post('/certificates/parse', [CertificateParserController::class, 'parse'])
            ->name('certificates.parse');

        // CV
        Route::get('/cv', [CvController::class, 'index'])->name('cv.index');
        Route::post('/cv', [CvController::class, 'store'])->name('cv.store');
        Route::patch('/cv/{cv}/activate', [CvController::class, 'activate'])->name('cv.activate');
        Route::delete('/cv/{cv}', [CvController::class, 'destroy'])->name('cv.destroy');

        // Social Links
        Route::get('/social-links', [SocialLinkController::class, 'index'])->name('social-links.index');
        Route::post('/social-links', [SocialLinkController::class, 'store'])->name('social-links.store');
        Route::put('/social-links/{socialLink}', [SocialLinkController::class, 'update'])->name('social-links.update');
        Route::delete('/social-links/{socialLink}', [SocialLinkController::class, 'destroy'])->name('social-links.destroy');
        Route::patch('/social-links/{socialLink}/toggle', [SocialLinkController::class, 'toggle'])->name('social-links.toggle');

        // ─── BLOG MODULE ───────────────────────────────────────
        Route::prefix('blog')->name('blog.')->group(function () {
            // Image Upload
            Route::post('/upload-image', [ImageUploadController::class, 'store'])->name('upload-image');
            Route::delete('/upload-image', [ImageUploadController::class, 'destroy'])->name('upload-image.destroy');

            // Posts
            Route::get('/posts', [BlogPostController::class, 'index'])->name('posts.index');
            Route::get('/posts/create', [BlogPostController::class, 'create'])->name('posts.create');
            Route::post('/posts', [BlogPostController::class, 'store'])->name('posts.store');
            Route::get('/posts/{post}/edit', [BlogPostController::class, 'edit'])->name('posts.edit');
            Route::post('/posts/{post}', [BlogPostController::class, 'update'])->name('posts.update');
            Route::delete('/posts/{post}', [BlogPostController::class, 'destroy'])->name('posts.destroy');
            Route::patch('/posts/{post}/toggle-featured', [BlogPostController::class, 'toggleFeatured'])->name('toggle-featured');
            Route::patch('/posts/{post}/toggle-status', [BlogPostController::class, 'toggleStatus'])->name('toggle-status');

            // Categories
            Route::get('/categories', [BlogCategoryController::class, 'index'])->name('categories.index');
            Route::post('/categories', [BlogCategoryController::class, 'store'])->name('categories.store');
            Route::post('/categories/{category}', [BlogCategoryController::class, 'update'])->name('categories.update');
            Route::delete('/categories/{category}', [BlogCategoryController::class, 'destroy'])->name('categories.destroy');

            // Tags
            Route::get('/tags', [BlogTagController::class, 'index'])->name('tags.index');
            Route::post('/tags', [BlogTagController::class, 'store'])->name('tags.store');
            Route::post('/tags/{tag}', [BlogTagController::class, 'update'])->name('tags.update');
            Route::delete('/tags/{tag}', [BlogTagController::class, 'destroy'])->name('tags.destroy');
        });

        // Site Settings
        Route::get('/settings/web', [SiteSettingController::class, 'edit'])->name('settings.web');
        Route::patch('/settings/web', [SiteSettingController::class, 'update'])->name('settings.web.update');
        Route::post('/settings/web/payment', [SiteSettingController::class, 'updatePayment'])->name('settings.web.payment');
        Route::post('/settings/web/favicon', [SiteSettingController::class, 'updateFavicon'])->name('settings.web.favicon');
        Route::post('/settings/web/og-image', [SiteSettingController::class, 'updateOgImage'])->name('settings.web.og-image');
        Route::post('/settings/web/hero', [SiteSettingController::class, 'updateHero'])->name('settings.web.hero');
        Route::delete('/settings/web/hero', [SiteSettingController::class, 'deleteHeroBackground'])->name('settings.web.hero.destroy');
        Route::patch('/settings/web/seo', [SiteSettingController::class, 'updateSeo'])->name('settings.web.seo');

        // Tasks (Time Management)
        Route::resource('tasks', TaskController::class)->except(['create', 'show', 'edit']);
        Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');

        // Messages
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
        Route::patch('/messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
        Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
        Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
        Route::patch('/messages/{message}/read', [MessageController::class, 'markAsRead'])
            ->name('messages.read');
        Route::delete('/messages/{message}', [MessageController::class, 'destroy'])
            ->name('messages.destroy');

        // Orders
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/simulate', [OrderController::class, 'simulate'])->name('orders.simulate');
        Route::post('/orders/{order}/mark-paid', [OrderController::class, 'markAsPaid'])->name('orders.mark-paid');
        Route::post('/orders/{order}/cancel', [OrderController::class, 'markAsCancelled'])->name('orders.cancel');

        // Products (legacy)
        Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::patch('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
        Route::patch('/products/{product}/toggle-featured', [AdminProductController::class, 'toggleFeatured'])->name('products.toggleFeatured');
        Route::patch('/products/{product}/toggle-active', [AdminProductController::class, 'toggleActive'])->name('products.toggleActive');

        // ─── FINANCE MODULE ───────────────────────────────────────
        Route::prefix('finance')->name('finance.')->group(function () {

            Route::get('/', [FinanceDashboardController::class, 'index'])->name('dashboard');

            Route::resource('wallets', WalletController::class);

            Route::resource('transactions', TransactionController::class);
            Route::get('transactions/export', [TransactionController::class, 'export'])->name('transactions.export');

            Route::resource('transfers', TransferController::class);

            Route::resource('invoices', InvoiceController::class);
            Route::patch('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
            Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');

            Route::resource('transaction-categories', TransactionCategoryController::class)->only(['index', 'store', 'update', 'destroy']);

            Route::resource('budgets', BudgetController::class)->only(['index', 'store', 'destroy']);

            Route::resource('savings-goals', SavingsGoalController::class);
            Route::patch('savings-goals/{goal}/add-funds', [SavingsGoalController::class, 'addFunds'])->name('savings-goals.add-funds');
        });

        // ─── PRODUCT MODULE ───────────────────────────────────────
        Route::prefix('products-v2')->name('products.')->group(function () {

            Route::get('/', [ProductDashboardController::class, 'index'])->name('dashboard');

            Route::resource('catalog', ProductController::class);
            Route::patch('catalog/{product}/toggle-public', [ProductController::class, 'togglePublic'])->name('catalog.toggle-public');

            Route::resource('orders', ProductOrderController::class);
            Route::patch('orders/{order}/status', [ProductOrderController::class, 'updateStatus'])->name('orders.update-status');

            Route::get('{product}/roadmap', [RoadmapController::class, 'index'])->name('roadmap.index');
            Route::resource('{product}/roadmap', RoadmapController::class)
                ->except(['index'])
                ->names([
                    'store' => 'roadmap.store',
                    'update' => 'roadmap.update',
                    'destroy' => 'roadmap.destroy',
                ]);

            Route::resource('{product}/metrics', ProductMetricController::class)->only(['store', 'destroy']);
        });
    });

// ─── CHECKOUT & PURCHASES ───────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/checkout/summary', [\App\Http\Controllers\Public\CheckoutSummaryController::class, '__invoke'])->name('checkout.summary');
    Route::get('/checkout/{slug}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/{slug}', [CheckoutController::class, 'initiate'])
        ->middleware('throttle:3,1')  // 3 attempts per minute to prevent order spam
        ->name('checkout.initiate');
    Route::get('/payment/{order}', [CheckoutController::class, 'payment'])->name('payment.show');
    Route::post('/payment/{order}/retry', [CheckoutController::class, 'retryPayment'])
        ->middleware('throttle:5,1')  // 5 retries per minute
        ->name('payment.retry');

    // User Dashboard - Simple URLs without UUID
    Route::get('/dashboard/user/home', [\App\Http\Controllers\Public\UserDashboardController::class, 'show'])->name('user.dashboard.home');
    Route::get('/dashboard/user/purchases', [\App\Http\Controllers\Public\DownloadController::class, 'showPurchases'])->name('user.purchases');

    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/download/{order}/{product}', [DownloadController::class, 'download'])->name('download.product');
});

// ─── API ROUTES (Inertia-style) ─────────────────────────────────────────────────
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\CartController;

// Upload routes (authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::post('/api/upload', [UploadController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('api.upload');

    Route::get('/api/uploads/{upload}', [UploadController::class, 'show'])->name('api.uploads.show');
    Route::delete('/api/uploads/{upload}', [UploadController::class, 'destroy'])->name('api.uploads.destroy');
    Route::get('/api/uploads/{upload}/signed-url', [UploadController::class, 'signedUrl'])->name('api.uploads.signed-url');
});

// Cart routes (all users) - rate limited
Route::middleware(['web', 'throttle:60,1'])->group(function () {
    Route::get('/api/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/api/cart', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/api/cart/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/api/cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('/api/cart', [CartController::class, 'clear'])->name('cart.clear');
});

// API Routes - rate limited
Route::middleware(['auth', 'verified', 'throttle:100,1'])->prefix('api')->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index'])->name('api.notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead'])->name('api.notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead'])->name('api.notifications.read-all');
});
Route::middleware(['auth', 'verified', 'throttle:30,1'])->group(function () {
    Route::get('/orders/{orderId}', [\App\Http\Controllers\Api\OrderController::class, 'show'])->name('api.orders.show');
    Route::get('/orders/{orderId}', [\App\Http\Controllers\User\OrderController::class, 'show'])->name('orders.show');
});
