<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;

Route::get('/', function () {
    return view('welcome');
});

// THIS SECTION IS THE FIX
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // API for the View Panel
    Route::get('/api/assessments-by-date', function (Request $request) {
        return \App\Models\Assessment::whereDate('scheduled_at', $request->date)
            ->get()
            ->map(function($a) {
                return [
                    'id' => $a->id,
                    'title' => $a->title,
                    'description' => $a->description,
                    'due_time' => \Carbon\Carbon::parse($a->scheduled_at)->format('g:i A')
                ];
            });
    });

    // Delete Route
    Route::delete('/assessments/{assessment}', [AssessmentController::class, 'destroy'])->name('assessments.destroy');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    
    // The rest of your authenticated routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/assessments', [AssessmentController::class, 'store'])->name('assessments.store');

    // Enroll Logic
    Route::post('/admin/enroll', function (Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'grade_level' => 'required',
            'section' => 'required|string',
            'password' => 'required|min:8',
        ]);

        \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => 'student',
            'grade_level' => $validated['grade_level'],
            'section' => $validated['section'],
        ]);

        return back()->with('success', 'Student enrolled successfully!');
    })->name('admin.enroll');
});

require __DIR__.'/auth.php';