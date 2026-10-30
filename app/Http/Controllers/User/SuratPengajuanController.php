<?php
// app/Http/Controllers/User/SuratPengajuanController.php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SuratPupuk;
use App\Models\SuratSolar;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuratPengajuanController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('user.surat-pengajuan.index', [
            'bolehPupuk' => $user->poktanPengelola() !== null,
            'totalSolar' => SuratSolar::where('user_id', $user->id)->count(),
            'totalPupuk' => SuratPupuk::where('user_id', $user->id)->count(),
            'terakhirSolar' => SuratSolar::where('user_id', $user->id)->latest()->value('status'),
            'terakhirPupuk' => SuratPupuk::where('user_id', $user->id)->latest()->value('status'),
        ]);
    }
}