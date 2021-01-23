<?php

use App\Http\Controllers\BitbucketController;
use App\Http\Middleware\TokenExistence;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::middleware([TokenExistence::class])->group(function(){
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
        Route::get('/', [BitbucketController::class, 'index'])->name('root');
        Route::get('test', [BitbucketController::class, 'test'])->name('test');
        Route::get('workspaces', [BitbucketController::class, 'workspaces'])
            ->name('workspaces');
        Route::get('repositories/{workspace}', [BitbucketController::class, 'repositories'])
            ->name('repositories');
        Route::get('pullRequests/{workspace}/{repository}', [BitbucketController::class, 'pullRequests'])
            ->name('pullRequests');
        Route::get('comments/{workspace}/{repository}/{pullRequestId}', [BitbucketController::class, 'comments'])
            ->name('comments');
    });

    Route::get('auth', [BitbucketController::class, 'auth'])->name('auth');
    Route::get('receiveOAuthCode', [BitbucketController::class, 'receiveOAuthCode'])->name('receiveOAuthCode');
});