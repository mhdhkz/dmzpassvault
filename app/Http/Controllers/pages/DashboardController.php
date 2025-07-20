<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Identity;
use App\Models\PasswordRequest;
use App\Models\PasswordJob;
use App\Models\PasswordAuditLog;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  public function index()
  {
    // Statistik utama
    $totalIdentities = Identity::count();
    $totalRequests = PasswordRequest::count();
    $totalUsers = User::count();
    $jobSuccess = PasswordJob::where('status', 'done')->count();
    $jobFailed = PasswordJob::where('status', 'failed')->count();
    $totalJobs = $jobSuccess + $jobFailed;

    // Bar Chart: 10 hari terakhir
    $startDate = Carbon::now()->subDays(9)->startOfDay();
    $endDate = Carbon::now()->endOfDay();

    $rawLogs = DB::table('password_audit_logs')
      ->selectRaw("DATE(event_time) as date, event_type, COUNT(*) as total")
      ->whereBetween('event_time', [$startDate, $endDate])
      ->whereIn('event_type', ['created', 'accessed', 'rotated'])
      ->groupBy(DB::raw("DATE(event_time)"), 'event_type')
      ->orderBy('date')
      ->get();

    $labels = collect();
    $events = ['created' => [], 'accessed' => [], 'rotated' => []];

    for ($i = 0; $i < 10; $i++) {
      $date = $startDate->copy()->addDays($i);
      $key = $date->format('Y-m-d');
      $labels->push($date->translatedFormat('j M'));

      foreach (array_keys($events) as $type) {
        $match = $rawLogs->first(fn($log) => $log->date === $key && $log->event_type === $type);
        $events[$type][] = $match ? $match->total : 0;
      }
    }

    $chartData = [
      'labels' => $labels,
      'created' => $events['created'],
      'accessed' => $events['accessed'],
      'rotated' => $events['rotated'],
    ];

    // Pie Chart: status request keseluruhan (default bulan saat ini)
    $monthStart = Carbon::now()->startOfMonth();
    $monthEnd = Carbon::now()->endOfMonth();

    $requestStatusCounts = PasswordRequest::whereBetween('created_at', [$monthStart, $monthEnd])
      ->select('status', DB::raw('COUNT(*) as total'))
      ->groupBy('status')
      ->pluck('total', 'status');

    $requestStatusData = [
      'labels' => ['Pending', 'Approved', 'Rejected', 'Expired'],
      'series' => [
        $requestStatusCounts['pending'] ?? 0,
        $requestStatusCounts['approved'] ?? 0,
        $requestStatusCounts['rejected'] ?? 0,
        $requestStatusCounts['expired'] ?? 0
      ]
    ];

    // Aktivitas terbaru
    $recentActivities = PasswordAuditLog::with('user')
      ->latest('event_time')
      ->limit(5)
      ->get();

    return view('content.pages.dashboard', compact(
      'totalIdentities',
      'totalRequests',
      'totalUsers',
      'jobSuccess',
      'jobFailed',
      'totalJobs',
      'chartData',
      'requestStatusData',
      'recentActivities'
    ));
  }

  public function getChartDataByMonth(Request $request)
  {
    $month = $request->input('month') ?: now()->format('m');
    $year = $request->input('year') ?: now()->format('Y');

    $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
    $endDate = $startDate->copy()->endOfMonth();

    // Pie Chart only (bar chart tidak difilter)
    $requestStatusCounts = PasswordRequest::whereBetween('created_at', [$startDate, $endDate])
      ->select('status', DB::raw('COUNT(*) as total'))
      ->groupBy('status')
      ->pluck('total', 'status');

    $requestStatusData = [
      'labels' => ['Pending', 'Approved', 'Rejected', 'Expired'],
      'series' => [
        $requestStatusCounts['pending'] ?? 0,
        $requestStatusCounts['approved'] ?? 0,
        $requestStatusCounts['rejected'] ?? 0,
        $requestStatusCounts['expired'] ?? 0
      ]
    ];

    return response()->json([
      'requestStatusData' => $requestStatusData
    ]);
  }
}
