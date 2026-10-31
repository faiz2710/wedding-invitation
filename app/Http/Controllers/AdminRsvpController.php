<?php

namespace App\Http\Controllers;

use App\Models\Rsvp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class AdminRsvpController extends Controller
{
    /**
     * Menampilkan halaman login admin.
     */
    public function login(): View|RedirectResponse
    {
        if (session('admin_authenticated')) {
            return redirect()->route('admin.rsvps');
        }

        return view('admin.login');
    }

    /**
     * Memeriksa password admin.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $adminPassword = (string) config('admin.password');

        if (
            $adminPassword === '' ||
            ! hash_equals($adminPassword, $request->input('password'))
        ) {
            return back()
                ->withErrors([
                    'password' => 'Password admin salah.',
                ])
                ->onlyInput();
        }

        $request->session()->regenerate();
        $request->session()->put('admin_authenticated', true);

        return redirect()->route('admin.rsvps');
    }

    /**
     * Menampilkan dashboard data RSVP.
     */
   public function index(): View
{
    $rsvps = Rsvp::latest()->paginate(20);

    $totalResponses = Rsvp::count();

    $attendingResponses = Rsvp::where(
        'attendance',
        'hadir'
    )->count();

    $notAttendingResponses = Rsvp::where(
        'attendance',
        'tidak_hadir'
    )->count();

    $attendingGuests = (int) Rsvp::where(
        'attendance',
        'hadir'
    )->sum('guests');

    return view('admin.rsvps', compact(
        'rsvps',
        'totalResponses',
        'attendingResponses',
        'notAttendingResponses',
        'attendingGuests'
    ));
}

    /**
     * Logout admin.
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin_authenticated');
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Kamu berhasil logout.');
    }
}
