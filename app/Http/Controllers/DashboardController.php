<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $date = Carbon::createFromDate($year, $month, 1);

        $daysInMonth = $date->daysInMonth;
        $firstDayOfMonth = $date->dayOfWeek;

        $user = auth()->user();

        // 1. Start the query for the dots
        $query = Assessment::whereMonth('due_date', $date->month)
                           ->whereYear('due_date', $date->year);

        // 2. CRITICAL: If student, filter by their grade_level
        // We use string conversion to ensure '12' matches '12' regardless of type
        if ($user->role === 'student') {
            $query->where('grade_level', (string)$user->grade_level);
        }

        $notifications = $query->get()
            ->groupBy(function($val) {
                return Carbon::parse($val->due_date)->format('j');
            })
            ->map->count();

        return view('dashboard', compact('date', 'notifications', 'daysInMonth', 'firstDayOfMonth'));
    }
}