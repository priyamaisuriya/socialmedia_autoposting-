<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;

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
Route::get('/auth/facebook/callback', [FacebookController::class, 'callback']);

Route::middleware('auth')->group(function () {
    Route::get('/facebook/accounts', [FacebookController::class, 'index'])
        ->name('facebook.index');
    Route::delete('/facebook/accounts/{account}', [FacebookController::class, 'destroy'])
        ->name('facebook.destroy');
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

    // Meta Ads Manager Routes
    Route::get('/ads', [App\Http\Controllers\AdCampaignController::class, 'index'])->name('ads.index');
    Route::get('/ads/create', [App\Http\Controllers\AdCampaignController::class, 'create'])->name('ads.create');
    Route::post('/ads', [App\Http\Controllers\AdCampaignController::class, 'store'])->name('ads.store');
    Route::post('/ads/{campaign}/toggle', [App\Http\Controllers\AdCampaignController::class, 'toggleStatus'])->name('ads.toggle');
    Route::delete('/ads/{campaign}', [App\Http\Controllers\AdCampaignController::class, 'destroy'])->name('ads.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';