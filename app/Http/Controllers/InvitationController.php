<?php

namespace App\Http\Controllers;

use App\Models\Wish;
use Illuminate\Contracts\View\View;

class InvitationController extends Controller
{
    /**
     * Halaman undangan.
     * Route::view diganti controller agar daftar Wedding Wishes bisa dikirim ke view.
     */
    public function index(): View
    {
        return view('invitation', [
            'wishes' => Wish::latest()->limit(30)->get(),
        ]);
    }
}
