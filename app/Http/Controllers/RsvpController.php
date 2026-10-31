<?php

namespace App\Http\Controllers;

use App\Models\Rsvp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RsvpController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'attendance' => ['required', 'in:hadir,tidak_hadir'],
            'guests' => ['required', 'integer', 'min:1', 'max:10'],
            'message' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'Nama tamu wajib diisi.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'attendance.required' => 'Silakan pilih konfirmasi kehadiran.',
            'attendance.in' => 'Pilihan kehadiran tidak valid.',
            'guests.required' => 'Jumlah tamu wajib diisi.',
            'guests.integer' => 'Jumlah tamu harus berupa angka.',
            'guests.min' => 'Jumlah tamu minimal 1 orang.',
            'guests.max' => 'Jumlah tamu maksimal 10 orang.',
            'message.max' => 'Pesan maksimal 1000 karakter.',
        ]);

        Rsvp::create($validated);

        return redirect()
            ->route('invitation')
            ->with(
                'rsvp_success',
                'Terima kasih! Konfirmasi kehadiranmu berhasil dikirim.'
            );
    }
}
