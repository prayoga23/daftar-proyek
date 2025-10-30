<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Kelompokkan baris berdasarkan nama proyek agar perhitungan unik per judul proyek
        $projectsByName = Project::select('nama_proyek', 'status_pekerjaan', 'highlighted')->get()
            ->groupBy('nama_proyek');

        // Proyek Yang Berjalan = Total semua proyek unik (tanpa filter)
        $proyekBerjalan = $projectsByName->count();
        
        $proyekMenunggu = 0;
        $proyekSelesai = 0;

        foreach ($projectsByName as $projectName => $rows) {
            // Cek apakah ada row yang di-highlight
            $hasHighlighted = $rows->contains('highlighted', true);
            
            // Cek apakah semua row memiliki status "Selesai"
            $allSelesai = $rows->every(function ($row) {
                $status = trim(mb_strtolower((string) $row->status_pekerjaan));
                return str_contains($status, 'selesai');
            });

            // Kategori untuk statistik tambahan:
            // 1. Jika ada yang di-highlight, masuk kategori "Mau Berjalan"
            // 2. Jika semua selesai dan tidak di-highlight, masuk kategori "Selesai"
            
            if ($hasHighlighted) {
                $proyekMenunggu++;
            } elseif ($allSelesai) {
                $proyekSelesai++;
            }
        }

        $totalProyek = $projectsByName->count();

        // Data untuk grafik per bulan (6 bulan terakhir)
        $chartData = $this->getMonthlyProjectData();

        return view('dashboard', compact('proyekBerjalan', 'proyekSelesai', 'proyekMenunggu', 'totalProyek', 'chartData'));
    }

    private function getMonthlyProjectData()
    {
        $months = [];
        $counts = [];
        
        // Ambil data 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthYear = $date->format('Y-m');
            $monthName = $date->locale('id')->translatedFormat('M Y');
            
            // Hitung proyek unik yang dibuat pada bulan tersebut
            $projectsInMonth = Project::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->select('nama_proyek')
                ->get()
                ->unique('nama_proyek')
                ->count();
            
            $months[] = $monthName;
            $counts[] = $projectsInMonth;
        }
        
        return [
            'labels' => $months,
            'data' => $counts
        ];
    }
}
