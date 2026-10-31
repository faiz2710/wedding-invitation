<?php

namespace App\Http\Controllers;

use App\Models\Wish;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // Field memakai array wishes[...] agar old input kedua form tidak bentrok.
        $validator = Validator::make($request->all(), [
            'wishes.name' => ['required', 'string', 'max:100'],
            'wishes.message' => ['required', 'string', 'max:500'],
        ], [
            'wishes.name.required' => 'Nama wajib diisi.',
            'wishes.name.max' => 'Nama maksimal 100 karakter.',
            'wishes.message.required' => 'Ucapan tidak boleh kosong.',
            'wishes.message.max' => 'Ucapan maksimal 500 karakter.',
        ], [
            'wishes.name' => 'nama',
            'wishes.message' => 'ucapan',
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator, 'wishes');
        }

        Wish::create($validator->validated()['wishes']);

        return redirect()
            ->route('invitation')
            ->with(
                'wishes_success',
                'Terima kasih! Ucapan dan doamu berhasil dikirim.'
            );
    }
}
