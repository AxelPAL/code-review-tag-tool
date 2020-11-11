<?php

use App\Http\Controllers\BitbucketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified', 'token.existence'])->group(function () {
    Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
    Route::get('/', [BitbucketController::class, 'index'])->name('root');
    Route::get('test', [BitbucketController::class, 'test'])->name('test');
    Route::get('workspaces', [BitbucketController::class, 'workspaces'])->name('workspaces');
    Route::get('repositories/{workspace}', [BitbucketController::class, 'repositories'])->name('repositories');
    Route::get('pullRequests/{workspace}/{repository}', [BitbucketController::class, 'pullRequests'])->name('pullRequests');
    Route::get('comments/{workspace}/{repository}/{pullRequestId}', [BitbucketController::class, 'comments'])->name('comments');

    Route::get('auth', [BitbucketController::class, 'auth'])->name('auth')->withoutMiddleware('token.existence');
    Route::get('receiveOAuthCode', [BitbucketController::class, 'receiveOAuthCode'])->name('receiveOAuthCode')->withoutMiddleware('token.existence');
});