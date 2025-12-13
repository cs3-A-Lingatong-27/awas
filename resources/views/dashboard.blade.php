<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">
                    Welcome, {{ Auth::user()->name }}!
                </h3>

                @php
                    $role = Auth::user()->role;
                @endphp

                @if($role === 'admin')
                    <p>You are logged in as <strong>Admin</strong>.</p>
                    <ul>
                        <li><a href="{{ route('admin.students') }}">Manage Students</a></li>
                        <li><a href="{{ route('admin.teachers') }}">Manage Teachers</a></li>
                    </ul>
                @elseif($role === 'teacher')
                    <p>You are logged in as <strong>Teacher</strong>.</p>
                    <ul>
                        <li><a href="{{ route('teacher.subjects') }}">My Subjects</a></li>
                        <li><a href="{{ route('teacher.assessments') }}">Assessments</a></li>
                    </ul>
                @elseif($role === 'student')
                    <p>You are logged in as <strong>Student</strong>.</p>
                    <ul>
                        <li><a href="{{ route('student.subjects') }}">My Subjects</a></li>
                        <li><a href="{{ route('student.grades') }}">My Grades</a></li>
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
