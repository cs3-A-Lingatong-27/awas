<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\AdminTeacherController;
use App\Http\Controllers\TeacherSubjectController;
use App\Http\Controllers\TeacherAssessmentController;
use App\Http\Controllers\StudentSubjectController;
use App\Http\Controllers\StudentGradeController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
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


require __DIR__.'/auth.php';
