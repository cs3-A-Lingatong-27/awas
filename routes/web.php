<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\AdminTeacherController;
use App\Http\Controllers\TeacherSubjectController;
use App\Http\Controllers\TeacherAssessmentController;
use App\Http\Controllers\StudentSubjectController;
use App\Http\Controllers\StudentGradeController;
use App\Models\Room;     
use App\Models\Assessment;
use Carbon\Carbon;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $date = Carbon::now();
    $rooms = App\Models\Room::all(); // Fetch rooms from your schema

    return view('dashboard', [
        'date' => $date,
        'daysInMonth' => $date->daysInMonth,
        'firstDayOfMonth' => $date->copy()->startOfMonth()->dayOfWeek,
        'rooms' => $rooms, // Pass rooms to the blade
        'notifications' => [6 => 2, 20 => 5]
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/students', [AdminController::class, 'students'])->name('admin.students');
    Route::get('/admin/students/create', [AdminController::class, 'createStudent'])->name('admin.students.create');
    Route::post('/admin/students', [AdminController::class, 'storeStudent'])->name('admin.students.store');
});
// Admin
Route::get('/admin/students', [AdminStudentController::class, 'index'])->name('admin.students');
Route::get('/admin/teachers', [AdminTeacherController::class, 'index'])->name('admin.teachers');

// Teacher
Route::get('/teacher/subjects', [TeacherSubjectController::class, 'index'])->name('teacher.subjects');
Route::get('/teacher/assessments', [TeacherAssessmentController::class, 'index'])->name('teacher.assessments');

// Student
Route::get('/student/subjects', [StudentSubjectController::class, 'index'])->name('student.subjects');
Route::get('/student/grades', [StudentGradeController::class, 'index'])->name('student.grades');

// Student Dashboard
Route::get('/student/dashboard', function () {
    return view('student.dashboard');
})->name('student.dashboard');
Route::post('/admin/enroll', function (Request $request) { // Pass $request here
    $validated = $request->validate([ // Use $request (lowercase)
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


require __DIR__.'/auth.php';
