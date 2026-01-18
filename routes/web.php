<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    // 1. Get current date and user
  $date = \Carbon\Carbon::now();
    return view('dashboard', [ // <--- This MUST match your filename
        'date' => $date,
        'daysInMonth' => $date->daysInMonth,
        'firstDayOfMonth' => $date->copy()->startOfMonth()->dayOfWeek,
        'notifications' => [6 => 2, 20 => 5]
    ]);

    // 2. Prepare Calendar Data
    $data = [
        'date' => $date,
        'daysInMonth' => $date->daysInMonth,
        'firstDayOfMonth' => $date->copy()->startOfMonth()->dayOfWeek,
        'rooms' => \App\Models\Room::all(),
        'notifications' => [6 => 2, 20 => 5] // Placeholder for dots on calendar
    ];

    // 3. Smart Redirect: If student, show student dashboard, else show main calendar
    if ($user->role === 'student') {
        return view('student.dashboard', $data);
    }

    return view('dashboard', $data);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Keep the enroll logic so it's ready when you need it
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
});

require __DIR__.'/auth.php';