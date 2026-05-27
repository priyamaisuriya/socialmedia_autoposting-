<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StoryController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/auth/facebook', [FacebookController::class, 'redirect']);
Route::get('/auth/instagram', [FacebookController::class, 'redirectInstagram']);
Route::get('/auth/facebook/callback', [FacebookController::class, 'callback']);

Route::middleware('auth')->group(function () {
    Route::get('/facebook/accounts', [FacebookController::class, 'index'])
        ->name('facebook.index');
    Route::delete('/facebook/accounts/{account}', [FacebookController::class, 'destroy'])
        ->name('facebook.destroy');

    Route::get('/instagram/accounts', [\App\Http\Controllers\InstagramController::class, 'index'])
        ->name('instagram.index');
    Route::post('/instagram/accounts/{id}/toggle', [\App\Http\Controllers\InstagramController::class, 'toggleConnection'])
        ->name('instagram.toggle');
    Route::get('/pages', function () {
        return view('pages.index', [
            'pages' => auth()->user()->facebookPages
        ]);
    })->name('pages.index');

    Route::get('/posts', [PostController::class, 'index'])
        ->name('posts.index');

    Route::get('/posts/create', [PostController::class, 'create'])
        ->name('posts.create');

    Route::post('/posts', [PostController::class, 'store'])
        ->name('posts.store');

    Route::get('/posts/{post}', [PostController::class, 'show'])
        ->name('posts.show');

    Route::delete('/posts/{post}', [PostController::class, 'destroy'])
        ->name('posts.destroy');

    Route::post('/posts/{post}/archive', [PostController::class, 'toggleArchive'])
        ->name('posts.archive');

    Route::get('/comments', [App\Http\Controllers\CommentController::class, 'index'])
        ->name('comments.index');

    Route::get('/comments/sync/{post}', [App\Http\Controllers\CommentController::class, 'sync'])
        ->name('comments.sync');

    Route::post('/comments/{comment}/reply', [App\Http\Controllers\CommentController::class, 'reply'])
        ->name('comments.reply');

    Route::delete('/comments/{comment}', [App\Http\Controllers\CommentController::class, 'destroy'])
        ->name('comments.destroy');

    Route::get('/analytics', [App\Http\Controllers\AnalyticsController::class, 'index'])
        ->name('analytics.index');

    Route::post('/ai-assistant/generate', [\App\Http\Controllers\AiAssistantController::class, 'generate'])->name('ai.generate');
    Route::post('/ai-assistant/reply', [\App\Http\Controllers\AiAssistantController::class, 'generateReply'])->name('ai.reply');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/facebook/toggle-archive/{post}', [PostController::class, 'toggleArchive'])->name('facebook.toggle-archive');

    // Ads Manager Routes
    Route::get('/ads', [App\Http\Controllers\AdManagerController::class, 'index'])->name('ads.index');
    Route::post('/ads/fetch', [App\Http\Controllers\AdManagerController::class, 'fetchAccounts'])->name('ads.fetch');
    Route::get('/ads/create', [App\Http\Controllers\AdManagerController::class, 'create'])->name('ads.create');
    Route::post('/ads/store', [App\Http\Controllers\AdManagerController::class, 'store'])->name('ads.store');

    // Social Media Management Routes
    Route::resource('posts', PostController::class);
    Route::resource('stories', StoryController::class);
    
    // Calendar Routes
    Route::get('/calendar', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [App\Http\Controllers\CalendarController::class, 'events'])->name('calendar.events');

    // WhatsApp Routes
    Route::get('/whatsapp/create', [App\Http\Controllers\WhatsAppController::class, 'create'])->name('whatsapp.create');
    Route::post('/whatsapp/send', [App\Http\Controllers\WhatsAppController::class, 'send'])->name('whatsapp.send');
});

require __DIR__.'/auth.php';