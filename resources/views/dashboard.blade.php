<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
  @if(session('success'))
    <div id="successToast" class="toast-notification">
        <span class="icon">✅</span> {{ session('success') }}
    </div>

    <script>
        setTimeout(() => {
            const toast = document.getElementById('successToast');
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    </script>
@endif
    <div class="app">
        <header class="topbar">
            <div class="logo">
                <div class="logo-circle"></div>
                <div class="logo-text">
                    <div>PHILIPPINE SCIENCE HIGH SCHOOL</div>
                    <div class="logo-sub">Caraga Region Campus in Butuan City</div>
                </div>
            </div>

 <div class="top-actions">
    @auth
        <button class="scholar-btn" onclick="openScholarPanel()">
            <span class="user-icon">👤</span> Scholar: {{ auth()->user()->name }}
        </button>
    @endauth

    @guest
        <a href="{{ route('login') }}" class="top-link">Log in</a>
        <a href="{{ route('register') }}" class="top-link">Register</a>
    @endguest
</div>
        </header>

<main class="main">
    <aside class="sidebar">
        <div class="menu-icon">☰</div>
        <div class="menu-title">Menu</div>
        
        <div class="menu-item active" onclick="showTab('calendar')">Dashboard</div>
        
        @if(auth()->user()->role === 'admin')
            <div class="menu-item" onclick="showTab('enrollment')">Enrollment</div>
        @endif

        <div class="menu-item">Subjects</div>
        <div class="menu-item">Grades</div>
    </aside>

    <section id="calendar-tab" class="content-tab">
        <section class="calendar-section">
            <div class="calendar-title">{{ $date->format('F Y') }}</div>
            
            <div class="calendar-grid">
                <div class="calendar-header">Sun</div>
                <div class="calendar-header">Mon</div>
                <div class="calendar-header">Tue</div>
                <div class="calendar-header">Wed</div>
                <div class="calendar-header">Thu</div>
                <div class="calendar-header">Fri</div>
                <div class="calendar-header">Sat</div>

                @for ($i = 0; $i < $firstDayOfMonth; $i++)
                    <div class="calendar-day empty" style="background: #fafafa;"></div>
                @endfor

                @for ($day = 1; $day <= $daysInMonth; $day++)
                    <div class="calendar-day" 
                         onclick="openPanel({{ $day }}, '{{ $date->format('F') }} {{ $day }}, {{ $date->year }}')">
                        {{ $day }}
                        @if(isset($notifications[$day]))
                            <div class="notification-dot">{{ $notifications[$day] }}</div>
                        @endif
                    </div>
                @endfor
            </div>
        </section>
    </section>

    @if(auth()->user()->role === 'admin')
    <section id="enrollment-tab" class="content-tab" style="display: none; padding: 40px;">
        <div class="enrollment-card">
            <h2>Student Enrollment</h2>
            <p>Register a new scholar into the system.</p>
            <hr style="margin: 20px 0; opacity: 0.2;">
            
            <form action="{{ route('admin.enroll') }}" method="POST" class="mini-form">
                @csrf
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="Juan Dela Cruz" required>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="juan@pshs.edu.ph" required>
                </div>

                <div class="form-row" style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Grade Level</label>
                        <select name="grade_level" style="width: 100%;">
                            <option value="7">Grade 7</option>
                            <option value="8">Grade 8</option>
                            <option value="9">Grade 9</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Section</label>
                        <input type="text" name="section" placeholder="Diamond" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Temporary Password</label>
                    <input type="password" name="password" required value="PSHS_2026">
                </div>

                <button type="submit" class="scholar-btn" style="width: 100%; margin-top: 10px;">Enroll Student</button>
            </form>
        </div>
    </section>
    @endif
</main>
    </div>



<div id="scholarPanel" class="side-panel">
    <div class="panel-header">
        <h3>Scholar Details</h3>
        <button onclick="closeAllPanels()" class="close-btn">&times;</button>
    </div>
    <div class="panel-body">
        <div class="profile-card">
            <div class="profile-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
            <h4>{{ auth()->user()->name }}</h4>
            <span class="badge">{{ ucfirst(auth()->user()->role) }}</span>
        </div>

        <div class="academic-info">
            @if(auth()->user()->role === 'student')
                <div class="info-group">
                    <label>Current Standing</label>
                    <div class="stat-grid">
                        <div class="stat-item">
                            <span>Grade</span>
                            <strong>{{ auth()->user()->grade_level }}</strong>
                        </div>
                        <div class="stat-item">
                            <span>Section</span>
                            <strong>{{ auth()->user()->section }}</strong>
                        </div>
                    </div>
                </div>

                <div class="info-group">
                    <label>Recent Grades</label>
                    <ul class="grade-list">
                        <li><span>Mathematics</span> <strong class="grade">94</strong></li>
                        <li><span>Science</span> <strong class="grade">91</strong></li>
                        <li><span>English</span> <strong class="grade">88</strong></li>
                    </ul>
                </div>
            @else
                <p class="text-muted">Accessing administrative dashboard tools.</p>
            @endif
        </div>

        <div class="menu-divider"></div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-action-btn">Logout Account</button>
        </form>
    </div>
</div>

    <script>
        function openPanel(day, dateString) {
            document.getElementById('panelDateTitle').innerText = dateString;
            document.getElementById('assessmentPanel').classList.add('open');
            document.getElementById('panelOverlay').classList.add('active');
        }

        function openScholarPanel() {
            document.getElementById('scholarPanel').classList.add('open');
            document.getElementById('panelOverlay').classList.add('active');
        }

        function closeAllPanels() {
            document.querySelectorAll('.side-panel').forEach(p => p.classList.remove('open'));
            document.getElementById('panelOverlay').classList.remove('active');
        }
        function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.content-tab').forEach(tab => {
        tab.style.display = 'none';
    });

    // Show the selected tab
    document.getElementById(tabName + '-tab').style.display = 'block';

    // Update active state in sidebar
    document.querySelectorAll('.menu-item').forEach(item => {
        item.classList.remove('active');
        if(item.innerText.toLowerCase().includes(tabName)) {
            item.classList.add('active');
        }
    });
}
    </script>
</body>
</html>