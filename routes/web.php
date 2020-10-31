<?php

use App\Http\Controllers\BitbucketController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BitbucketController::class, 'index'])->name('root');
Route::get('test', [BitbucketController::class, 'test'])->name('test');
Route::get('test/{account}/{repo}/{pullRequestId}', [BitbucketController::class, 'test']);
Route::get('auth', [BitbucketController::class, 'auth'])->name('auth');
Route::get('receiveOAuthCode', [BitbucketController::class, 'receiveOAuthCode'])->name('receiveOAuthCode');