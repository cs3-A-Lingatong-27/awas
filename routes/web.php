<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

/**
 * PUBLIC ROUTES
 */
Route::get('/', function () {
    return view('welcome');
});

/**
 * AUTHENTICATED ROUTES
 * All routes inside this group require a logged-in user.
 */
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard - The main entry point after login
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management (Standard Breeze/Fortify)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Assessment Management
    Route::post('/assessments', [AssessmentController::class, 'store'])->name('assessments.store');
    Route::delete('/assessments/{assessment}', [AssessmentController::class, 'destroy'])->name('assessments.destroy');

    /**
     * API: FETCH ASSESSMENTS FOR THE SIDE PANEL
     * Used by the calendar to show details for a specific day.
     */
    Route::get('/api/assessments-by-date', function (Request $request) {
        $user = auth()->user();
        $targetDate = $request->query('date');
        if (!$targetDate) return response()->json([]);

        $query = Assessment::whereDate('scheduled_at', $targetDate);
        
        // Students only see assessments for their grade level
        if ($user && $user->role === 'student') {
            $query->where('grade_level', $user->grade_level);
        }

        return $query->get()->map(function($a) {
            return [
                'id' => $a->id,
                'title' => $a->title,
                'subject' => $a->subject ? $a->subject->name : $a->type,
                'description' => $a->description,
                'due_time' => $a->scheduled_at ? Carbon::parse($a->scheduled_at)->format('g:i A') : 'No time set'
            ];
        });
    });

    /**
     * API: FETCH NOTIFICATION DOTS FOR CALENDAR
     * Used to show the red dots on the dashboard calendar.
     */
    Route::get('/api/assessment-notifications', function (Request $request) {
        $user = auth()->user();
        $month = $request->query('month', date('m'));
        $year = $request->query('year', date('Y'));

        $query = Assessment::whereMonth('scheduled_at', $month)->whereYear('scheduled_at', $year);
        
        if ($user && $user->role === 'student') {
            $query->where('grade_level', $user->grade_level);
        }

        return $query->get()
            ->groupBy(fn($val) => Carbon::parse($val->scheduled_at)->format('j'))
            ->map->count();
    });

    /**
     * ADMIN: ENROLL LOGIC
     * Allows admins to register new students manually.
     */
    Route::post('/admin/enroll', function (Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'grade_level' => 'required',
            'section' => 'required|string',
            'password' => 'required|min:8',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => 'student',
            'grade_level' => $validated['grade_level'],
            'section' => $validated['section'],
        ]);

        return back()->with('success', 'Student enrolled successfully!');
    })->name('admin.enroll');

    /**
     * ADMIN: DAILY EMAIL SUMMARY
     * Triggers an email to all admins with today's schedule.
     */
    Route::get('/admin/send-daily-summary', function () {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $today = now()->toDateString();
        $assessments = Assessment::whereDate('scheduled_at', $today)
            ->orderBy('grade_level', 'asc')
            ->orderBy('scheduled_at', 'asc')
            ->get();

        if ($assessments->isEmpty()) {
            return "No assessments scheduled for today ($today). Email not sent.";
        }

        $teachers = User::where('role', 'admin')->get();

        foreach ($teachers as $teacher) {
            Mail::send([], [], function ($message) use ($teacher, $assessments, $today) {
                $formattedDate = Carbon::parse($today)->format('F j, Y');
                
                $html = "<div style='font-family: sans-serif; color: #333;'>
                    <h2 style='color: #2563eb;'>Daily Assessment Summary</h2>
                    <p>Hello {$teacher->name},</p>
                    <p>Today's schedule (<strong>{$formattedDate}</strong>):</p>
                    <table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>
                        <tr style='background: #f3f4f6;'><th>Grade</th><th>Subject</th><th>Title</th><th>Time</th></tr>";
                
                foreach ($assessments as $a) {
                    $time = Carbon::parse($a->scheduled_at)->format('g:i A');
                    $html .= "<tr><td>{$a->grade_level}</td><td>" . ($a->subject ? $a->subject->name : $a->type) . "</td><td>{$a->title}</td><td>{$time}</td></tr>";
                }
                
                $html .= "</table></div>";

                $message->to($teacher->email)
                    ->subject("📅 Daily Schedule - {$formattedDate}")
                    ->html($html);
            });
        }

        return "Summary sent to " . $teachers->count() . " teachers.";
    });
});

/**
 * AUTHENTICATION ROUTES
 * This loads the default routes for login, registration, and password resets.
 */
require __DIR__.'/auth.php';