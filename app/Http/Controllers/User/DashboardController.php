<?php
// app/Http/Controllers/User/DashboardController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SuratSolar;  

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalPengajuan = SuratSolar::where('user_id', $user->id)->count();

        $statusTerakhir = SuratSolar::where('user_id', $user->id)
            ->latest()
            ->value('status');  

        return view('user.dashboard', compact('totalPengajuan', 'statusTerakhir'));
    }
}