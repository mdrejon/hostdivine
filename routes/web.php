<?php

use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DomainPricingController as AdminDomainPricingController;
use App\Http\Controllers\Admin\InquiryController as AdminInquiryController;
use App\Http\Controllers\Admin\LayoutController as AdminLayoutController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\SectionController as AdminSectionController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\SharedSectionController as AdminSharedSectionController;
use App\Http\Controllers\Admin\SmtpController as AdminSmtpController;
use App\Http\Controllers\Admin\SystemController as AdminSystemController;
use App\Http\Controllers\Admin\UploadController as AdminUploadController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\SeoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Website (CMS pages rendered with Blade)
|--------------------------------------------------------------------------
*/

Route::get('/', [PageController::class, 'show'])->name('home');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::post('/contact', [ContactController::class, 'submit'])->middleware('throttle:10,1')->name('contact.submit');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

/*
|--------------------------------------------------------------------------
| Admin Dashboard (Inertia + Vue)
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Inquiries (Leads)
        Route::get('/inquiries', [AdminInquiryController::class, 'index'])->name('inquiries.index');
        Route::get('/inquiries/{inquiry}', [AdminInquiryController::class, 'show'])->name('inquiries.show');
        Route::put('/inquiries/{inquiry}', [AdminInquiryController::class, 'update'])->name('inquiries.update');
        Route::patch('/inquiries/{inquiry}/status', [AdminInquiryController::class, 'updateStatus'])->name('inquiries.update-status');
        Route::delete('/inquiries/{inquiry}', [AdminInquiryController::class, 'destroy'])->name('inquiries.destroy');

        // Pages & page builder
        Route::get('/pages', [AdminPageController::class, 'index'])->name('pages.index');
        Route::post('/pages', [AdminPageController::class, 'store'])->name('pages.store');
        Route::get('/pages/{page}', [AdminPageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{page}', [AdminPageController::class, 'update'])->name('pages.update');
        Route::delete('/pages/{page}', [AdminPageController::class, 'destroy'])->name('pages.destroy');
        Route::post('/pages/{page}/sections', [AdminSectionController::class, 'store'])->name('sections.store');
        Route::post('/pages/{page}/sections/reorder', [AdminSectionController::class, 'reorder'])->name('sections.reorder');

        // Sections (page sections and shared library sections)
        Route::put('/sections/{section}', [AdminSectionController::class, 'update'])->name('sections.update');
        Route::patch('/sections/{section}/toggle', [AdminSectionController::class, 'toggle'])->name('sections.toggle');
        Route::post('/sections/{section}/duplicate', [AdminSectionController::class, 'duplicate'])->name('sections.duplicate');
        Route::post('/sections/{section}/share', [AdminSectionController::class, 'share'])->name('sections.share');
        Route::post('/sections/{section}/detach', [AdminSectionController::class, 'detach'])->name('sections.detach');
        Route::delete('/sections/{section}', [AdminSectionController::class, 'destroy'])->name('sections.destroy');

        Route::get('/shared-sections', [AdminSharedSectionController::class, 'index'])->name('shared-sections.index');
        Route::post('/shared-sections', [AdminSharedSectionController::class, 'store'])->name('shared-sections.store');

        // Header, footer, menus and site-wide widgets
        Route::get('/layout', [AdminLayoutController::class, 'index'])->name('layout.index');
        Route::put('/layout/{key}', [AdminLayoutController::class, 'update'])->name('layout.update');

        // Image uploads for CMS fields
        Route::post('/uploads', [AdminUploadController::class, 'store'])->name('uploads.store');

        // Domain Pricing
        Route::get('/domain-pricing', [AdminDomainPricingController::class, 'index'])->name('domain-pricing.index');
        Route::post('/domain-pricing', [AdminDomainPricingController::class, 'store'])->name('domain-pricing.store');
        Route::put('/domain-pricing/{tld}', [AdminDomainPricingController::class, 'update'])->name('domain-pricing.update');
        Route::patch('/domain-pricing/{tld}/toggle-popular', [AdminDomainPricingController::class, 'togglePopular'])->name('domain-pricing.toggle-popular');
        Route::delete('/domain-pricing/{tld}', [AdminDomainPricingController::class, 'destroy'])->name('domain-pricing.destroy');

        // Blog
        Route::get('/content/blog', [AdminBlogController::class, 'index'])->name('blog.index');
        Route::post('/content/blog', [AdminBlogController::class, 'store'])->name('blog.store');
        Route::put('/content/blog/{blog}', [AdminBlogController::class, 'update'])->name('blog.update');
        Route::delete('/content/blog/{blog}', [AdminBlogController::class, 'destroy'])->name('blog.destroy');

        // Global Settings
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

        // Email SMTP Settings
        Route::get('/smtp', [AdminSmtpController::class, 'index'])->name('smtp.index');
        Route::post('/smtp', [AdminSmtpController::class, 'update'])->name('smtp.update');
        Route::post('/smtp/test', [AdminSmtpController::class, 'sendTestEmail'])->name('smtp.test');

        // User Management
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // System Cache Clear
        Route::match(['get', 'post'], '/system/cache-clear', [AdminSystemController::class, 'clearCache'])->name('system.cache-clear');
    });
});

/*
|--------------------------------------------------------------------------
| CMS page catch-all — must stay last so it never shadows other routes.
|--------------------------------------------------------------------------
*/

Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '^(?!admin|api|login|logout|register|user|storage|build|assets|livewire|sanctum|up$)[a-z0-9-]+$')
    ->name('page');
