#!/bin/bash
# ============================================================
# Script: make_structure.sh
# Fungsi: Membuat struktur file & folder proyek E-Learning Laravel
# Cara pakai:
#   cd /path/ke/project-laravel-kamu
#   bash make_structure.sh
# ============================================================

set -e

echo "🚀 Membuat struktur E-Learning Laravel..."

# ─────────────────────────────────────────
# 1. CONTROLLERS
# ─────────────────────────────────────────
mkdir -p app/Http/Controllers/Admin
mkdir -p app/Http/Controllers/Guru
mkdir -p app/Http/Controllers/Siswa

touch app/Http/Controllers/AuthController.php

# Admin Controllers
touch app/Http/Controllers/Admin/DashboardController.php
touch app/Http/Controllers/Admin/UserController.php
touch app/Http/Controllers/Admin/RombelController.php
touch app/Http/Controllers/Admin/ImportController.php
touch app/Http/Controllers/Admin/AcademicYearController.php
touch app/Http/Controllers/Admin/AnnouncementController.php
touch app/Http/Controllers/Admin/ReportController.php

# Guru Controllers
touch app/Http/Controllers/Guru/DashboardController.php
touch app/Http/Controllers/Guru/ClassController.php
touch app/Http/Controllers/Guru/MeetingController.php
touch app/Http/Controllers/Guru/MaterialController.php
touch app/Http/Controllers/Guru/AssignmentController.php
touch app/Http/Controllers/Guru/SubmissionController.php
touch app/Http/Controllers/Guru/QuizController.php
touch app/Http/Controllers/Guru/QuestionController.php
touch app/Http/Controllers/Guru/GradeController.php
touch app/Http/Controllers/Guru/AttendanceController.php
touch app/Http/Controllers/Guru/AnnouncementController.php
touch app/Http/Controllers/Guru/MessageController.php
touch app/Http/Controllers/Guru/DiscussionController.php

# Siswa Controllers
touch app/Http/Controllers/Siswa/DashboardController.php
touch app/Http/Controllers/Siswa/ClassController.php
touch app/Http/Controllers/Siswa/MaterialController.php
touch app/Http/Controllers/Siswa/AssignmentController.php
touch app/Http/Controllers/Siswa/QuizController.php
touch app/Http/Controllers/Siswa/GradeController.php
touch app/Http/Controllers/Siswa/AttendanceController.php
touch app/Http/Controllers/Siswa/MessageController.php
touch app/Http/Controllers/Siswa/CalendarController.php
touch app/Http/Controllers/Siswa/DiscussionController.php

echo "  ✅ Controllers selesai"

# ─────────────────────────────────────────
# 2. MIDDLEWARE & REQUESTS
# ─────────────────────────────────────────
mkdir -p app/Http/Middleware
mkdir -p app/Http/Requests

touch app/Http/Middleware/EnsureRole.php

touch app/Http/Requests/LoginRequest.php
touch app/Http/Requests/RegisterSiswaRequest.php
touch app/Http/Requests/StoreUserRequest.php
touch app/Http/Requests/StoreMaterialRequest.php
touch app/Http/Requests/StoreAssignmentRequest.php
touch app/Http/Requests/SubmitAssignmentRequest.php
touch app/Http/Requests/StoreQuizRequest.php
touch app/Http/Requests/StoreQuestionRequest.php
touch app/Http/Requests/StoreGradeRequest.php
touch app/Http/Requests/StoreAttendanceRequest.php
touch app/Http/Requests/CheckinRequest.php
touch app/Http/Requests/StoreAnnouncementRequest.php
touch app/Http/Requests/SendMessageRequest.php

echo "  ✅ Middleware & Requests selesai"

# ─────────────────────────────────────────
# 3. MODELS
# ─────────────────────────────────────────
mkdir -p app/Models

touch app/Models/User.php
touch app/Models/Admin.php
touch app/Models/Teacher.php
touch app/Models/Student.php
touch app/Models/Rombel.php
touch app/Models/SchoolClass.php
touch app/Models/ClassEnrollment.php
touch app/Models/Meeting.php
touch app/Models/Material.php
touch app/Models/ContentBlock.php
touch app/Models/MaterialProgress.php
touch app/Models/Assignment.php
touch app/Models/AssignmentSubmission.php
touch app/Models/SubmissionRevision.php
touch app/Models/Quiz.php
touch app/Models/Question.php
touch app/Models/QuestionOption.php
touch app/Models/QuizAttempt.php
touch app/Models/QuizAnswer.php
touch app/Models/GradeComponent.php
touch app/Models/Grade.php
touch app/Models/AttendanceSession.php
touch app/Models/Attendance.php
touch app/Models/Announcement.php
touch app/Models/AnnouncementRead.php
touch app/Models/Message.php
touch app/Models/Notification.php
touch app/Models/Discussion.php

echo "  ✅ Models selesai"

# ─────────────────────────────────────────
# 4. JOBS
# ─────────────────────────────────────────
mkdir -p app/Jobs

touch app/Jobs/ConvertPdfToFlipbook.php
touch app/Jobs/ZipAssignmentSubmissions.php
touch app/Jobs/SendDeadlineReminder.php
touch app/Jobs/ExportGradesToExcel.php

echo "  ✅ Jobs selesai"

# ─────────────────────────────────────────
# 5. NOTIFICATIONS
# ─────────────────────────────────────────
mkdir -p app/Notifications

touch app/Notifications/NewMaterialPublished.php
touch app/Notifications/NewAssignmentCreated.php
touch app/Notifications/AssignmentDeadlineReminder.php
touch app/Notifications/AssignmentGraded.php
touch app/Notifications/QuizStartingSoon.php
touch app/Notifications/QuizResultReleased.php
touch app/Notifications/StudentCheatingDetected.php
touch app/Notifications/ZipReadyToDownload.php

echo "  ✅ Notifications selesai"

# ─────────────────────────────────────────
# 6. POLICIES
# ─────────────────────────────────────────
mkdir -p app/Policies

touch app/Policies/SchoolClassPolicy.php
touch app/Policies/MaterialPolicy.php
touch app/Policies/AssignmentPolicy.php
touch app/Policies/QuizPolicy.php
touch app/Policies/GradePolicy.php

echo "  ✅ Policies selesai"

# ─────────────────────────────────────────
# 7. IMPORTS
# ─────────────────────────────────────────
mkdir -p app/Imports

touch app/Imports/StudentsImport.php

echo "  ✅ Imports selesai"

# ─────────────────────────────────────────
# 8. VIEWS
# ─────────────────────────────────────────
mkdir -p resources/views/auth
mkdir -p resources/views/layouts
mkdir -p resources/views/components
mkdir -p resources/views/admin/users
mkdir -p resources/views/admin/rombels
mkdir -p resources/views/admin/announcements
mkdir -p resources/views/admin/reports
mkdir -p resources/views/guru/classes
mkdir -p resources/views/guru/meetings
mkdir -p resources/views/guru/materials
mkdir -p resources/views/guru/assignments
mkdir -p resources/views/guru/quizzes
mkdir -p resources/views/guru/grades
mkdir -p resources/views/guru/attendance
mkdir -p resources/views/guru/messages
mkdir -p resources/views/guru/discussions
mkdir -p resources/views/siswa/classes
mkdir -p resources/views/siswa/materials
mkdir -p resources/views/siswa/assignments
mkdir -p resources/views/siswa/quizzes
mkdir -p resources/views/siswa/grades
mkdir -p resources/views/siswa/attendance
mkdir -p resources/views/siswa/messages
mkdir -p resources/views/siswa/calendar

# Auth views
touch resources/views/auth/login.blade.php
touch resources/views/auth/register.blade.php

# Layout views
touch resources/views/layouts/app.blade.php
touch resources/views/layouts/guest.blade.php

# Components
touch resources/views/components/sidebar.blade.php
touch resources/views/components/navbar.blade.php
touch resources/views/components/notification-badge.blade.php
touch resources/views/components/websocket-indicator.blade.php

# Admin views
touch resources/views/admin/dashboard.blade.php

# Guru views
touch resources/views/guru/dashboard.blade.php

# Siswa views
touch resources/views/siswa/dashboard.blade.php

echo "  ✅ Views selesai"

# ─────────────────────────────────────────
# 9. ROUTES
# ─────────────────────────────────────────
# (web.php & console.php biasanya sudah ada di Laravel)
# Hanya buat channels.php jika belum ada
mkdir -p routes
if [ ! -f routes/channels.php ]; then
  touch routes/channels.php
  echo "  ✅ routes/channels.php dibuat"
else
  echo "  ⏭️  routes/channels.php sudah ada, dilewati"
fi

# ─────────────────────────────────────────
# SELESAI
# ─────────────────────────────────────────
echo ""
echo "🎉 Struktur proyek berhasil dibuat!"
echo "   Jalankan 'php artisan serve' untuk memulai."