<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Admin & eksekutif langsung diarahkan ke dashboard monitoring.
        if ($user->hasRole('admin', 'eksekutif')) {
            return redirect()->route('monitoring.index');
        }

        $laporans = Laporan::with('kategori')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('dashboard', compact('laporans'));
    }
}
