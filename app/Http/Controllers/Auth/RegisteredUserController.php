<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Poktan;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public const KONSUMEN = [
        'usaha_mikro'               => 'Usaha Mikro',
        'usaha_pertanian'           => 'Usaha Pertanian',
        'usaha_perikanan'           => 'Usaha Perikanan',
        'transportasi_motor_tempel' => 'Transportasi Motor Tempel',
        'pelayanan_umum'            => 'Pelayanan Umum',
    ];

    public function create(): View
    {
        return view('auth.register', [
            'poktans'  => Poktan::orderBy('nama_kelompok')->get(),
            'konsumen' => self::KONSUMEN,
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'nik'               => ['required', 'digits:16', 'unique:users,nik'],
            'no_hp'             => ['required', 'string', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
            'alamat'            => ['required', 'string', 'max:500'],
            'konsumen_pengguna' => ['required', Rule::in(array_keys(self::KONSUMEN))],
            'jenis_usaha'       => ['required', 'string', 'max:255'],
            'nama_kapal'        => ['nullable', 'string', 'max:255'],
            'poktan_id'         => ['required', 'exists:poktans,id'],
            'email'             => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'          => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'nik.digits'  => 'NIK harus 16 digit angka.',
            'nik.unique'  => 'NIK sudah terdaftar.',
            'no_hp.regex' => 'Format No. HP tidak valid (contoh: 081234567890).',
        ]);

        $user = User::create([
            'name'              => $data['name'],
            'nik'               => $data['nik'],
            'no_hp'             => $data['no_hp'],
            'alamat'            => $data['alamat'],
            'konsumen_pengguna' => $data['konsumen_pengguna'],
            'jenis_usaha'       => $data['jenis_usaha'],
            'nama_kapal'        => $data['nama_kapal'] ?? null,
            'poktan_id'         => $data['poktan_id'],
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'role'              => 'user',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('user.dashboard');
    }
}