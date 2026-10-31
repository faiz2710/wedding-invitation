<?php

use App\Http\Controllers\AdminRsvpController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\WishController;
use Illuminate\Support\Facades\Route;

Route::get('/', [InvitationController::class, 'index'])
    ->name('invitation');

Route::post('/rsvp', 'App\\Http\\Controllers\\RsvpController@store')
    ->name('rsvp.store');

Route::post('/wishes', [WishController::class, 'store'])
    ->name('wishes.store');

Route::get('/admin/login', [AdminRsvpController::class, 'login'])
    ->name('admin.login');

Route::post('/admin/login', [AdminRsvpController::class, 'authenticate'])
    ->name('admin.authenticate');

Route::prefix('admin')
    ->middleware(\App\Http\Middleware\AdminAuth::class)
    ->group(function () {
        Route::get('/rsvps', [AdminRsvpController::class, 'index'])
            ->name('admin.rsvps');

        Route::post('/logout', [AdminRsvpController::class, 'logout'])
            ->name('admin.logout');
    });
