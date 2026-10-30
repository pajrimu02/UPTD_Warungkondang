<?php 
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SuratPupuk;
use App\Models\SuratSolar;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $id = $request->user()->id;

        $terakhir = collect([
            SuratSolar::where('user_id', $id)->latest()->first(),
            SuratPupuk::where('user_id', $id)->latest()->first(),
        ])->filter()->sortByDesc('created_at')->first();

        return view('user.dashboard', [
            'totalPengajuan' => SuratSolar::where('user_id', $id)->count() + SuratPupuk::where('user_id', $id)->count(),
            'statusTerakhir' => $terakhir?->status,
        ]);
    }
}