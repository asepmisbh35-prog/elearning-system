<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Guru\DashboardController as GuruDashboard;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Guru\ClassController as GuruClassController;
use App\Http\Controllers\Siswa\ClassController as SiswaClassController;
use App\Http\Controllers\Guru\MaterialController as GuruMaterialController;
use App\Http\Controllers\Siswa\MaterialController as SiswaMaterialController;
use App\Http\Controllers\Guru\MeetingController as GuruMeetingController;
use App\Http\Controllers\Guru\AssignmentController as GuruAssignmentController;
use App\Http\Controllers\Siswa\AssignmentController as SiswaAssignmentController;
use App\Http\Controllers\Guru\SubmissionController as GuruSubmissionController;
use App\Http\Controllers\Guru\QuestionController as GuruQuestionController;
use App\Http\Controllers\Guru\QuizController as GuruQuizController;
use App\Http\Controllers\Siswa\QuizController as SiswaQuizController;
use App\Http\Controllers\Guru\QuizResultController;
use App\Http\Controllers\Guru\GradeComponentController;
use App\Http\Controllers\Admin\KkmController as AdminKkmController;
use App\Http\Controllers\Guru\KkmController as GuruKkmController;
use App\Http\Controllers\Guru\GradeController;
use App\Http\Controllers\Guru\GradeExportController;
use App\Http\Controllers\Siswa\GradeController as SiswaGradeController;
use App\Http\Controllers\Guru\AttendanceController as GuruAttendanceController;
use App\Http\Controllers\Siswa\AttendanceController as SiswaAttendanceController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Guru\AnnouncementController as GuruAnnouncementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AnnouncementReadController;
use App\Http\Controllers\Guru\MessageController as GuruMessageController;
use App\Http\Controllers\Siswa\MessageController as SiswaMessageController;
use App\Http\Controllers\Admin\SchoolSettingController;


// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// ── Auth ───────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Profil (semua role) ────────────────────────────────
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/',          [ProfileController::class, 'show'])->name('show');
    Route::patch('/',        [ProfileController::class, 'update'])->name('update');
    Route::post('/photo',    [ProfileController::class, 'updatePhoto'])->name('photo.update');
    Route::delete('/photo',  [ProfileController::class, 'deletePhoto'])->name('photo.delete');
    Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
});


// ── Notifikasi & Pengumuman (semua role) ───────────────
Route::middleware('auth')->group(function () {
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',              [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read',    [NotificationController::class, 'markRead'])->name('read');
        Route::post('/read-all',     [NotificationController::class, 'markAllRead'])->name('read-all');
        Route::get('/unread-count',  [NotificationController::class, 'unreadCount'])->name('unread-count');
    });

    Route::prefix('pengumuman')->name('announcements.')->group(function () {
        Route::get('/',        [AnnouncementReadController::class, 'index'])->name('index');
        Route::get('/{announcement}', [AnnouncementReadController::class, 'show'])->name('show');
    });
    Route::get('/notifications/recent', [NotificationController::class, 'recent'])->name('notifications.recent');
});

// ── Admin ──────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/',                    [UserController::class, 'index'])->name('index');
        Route::get('/guru/create',         [UserController::class, 'createGuru'])->name('guru.create');
        Route::post('/guru',               [UserController::class, 'storeGuru'])->name('guru.store');
        Route::get('/siswa/create',        [UserController::class, 'createSiswa'])->name('siswa.create');
        Route::post('/siswa',              [UserController::class, 'storeSiswa'])->name('siswa.store');
        Route::get('/import',              [UserController::class, 'showImport'])->name('import');
        Route::post('/import',             [UserController::class, 'import'])->name('import.store');
        Route::get('/{user}/edit',         [UserController::class, 'edit'])->name('edit');
        Route::patch('/{user}',            [UserController::class, 'update'])->name('update');
        Route::patch('/{user}/deactivate', [UserController::class, 'deactivate'])->name('deactivate');
        Route::patch('/{user}/activate',   [UserController::class, 'activate'])->name('activate');
    });
    Route::prefix('kkm')->name('kkm.')->group(function () {
        Route::get('/',  [AdminKkmController::class, 'index'])->name('index');
        Route::post('/', [AdminKkmController::class, 'store'])->name('store');
    });

    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/',       [AdminAnnouncementController::class, 'index'])->name('index');
        Route::get('/create', [AdminAnnouncementController::class, 'create'])->name('create');
        Route::post('/',      [AdminAnnouncementController::class, 'store'])->name('store');
        Route::delete('/{announcement}', [AdminAnnouncementController::class, 'destroy'])->name('destroy');
    });

    Route::get('settings', [SchoolSettingController::class, 'edit'])->name('settings.edit');
    Route::patch('settings', [SchoolSettingController::class, 'update'])->name('settings.update');
});

// ── Guru ───────────────────────────────────────────────
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruDashboard::class, 'index'])->name('dashboard');

    Route::prefix('classes')->name('classes.')->group(function () {
        Route::get('/',                                 [GuruClassController::class, 'index'])->name('index');
        Route::get('/create',                           [GuruClassController::class, 'create'])->name('create');
        Route::post('/',                                [GuruClassController::class, 'store'])->name('store');
        Route::get('/{class}/edit',                     [GuruClassController::class, 'edit'])->name('edit');
        Route::get('/{class}/qr',                       [GuruClassController::class, 'qrCode'])->name('qr');
        Route::patch('/{class}',                        [GuruClassController::class, 'update'])->name('update');
        Route::patch('/{class}/reset-code',             [GuruClassController::class, 'resetCode'])->name('reset-code');
        Route::delete('/{class}/students/{enrollment}', [GuruClassController::class, 'removeStudent'])->name('remove-student');

        Route::get('/{class}',                          [GuruClassController::class, 'show'])->name('show');
    });

    Route::prefix('classes/{class}/assignments')->name('classes.assignments.')->group(function () {
        Route::get('/',                  [GuruAssignmentController::class, 'index'])->name('index');
        Route::get('/create',            [GuruAssignmentController::class, 'create'])->name('create');
        Route::post('/',                 [GuruAssignmentController::class, 'store'])->name('store');
        Route::get('/{assignment}/edit', [GuruAssignmentController::class, 'edit'])->name('edit');
        Route::patch('/{assignment}',    [GuruAssignmentController::class, 'update'])->name('update');
        Route::delete('/{assignment}',   [GuruAssignmentController::class, 'destroy'])->name('destroy');
    });

    // 2) TAMBAHKAN BLOK INI — CRUD Pertemuan, taruh di sini
    Route::prefix('classes/{class}/meetings')->name('classes.meetings.')->group(function () {
        Route::get('/',               [GuruMeetingController::class, 'index'])->name('index');
        Route::get('/create',         [GuruMeetingController::class, 'create'])->name('create');
        Route::post('/',              [GuruMeetingController::class, 'store'])->name('store');
        Route::get('/{meeting}/edit', [GuruMeetingController::class, 'edit'])->name('edit');
        Route::patch('/{meeting}',    [GuruMeetingController::class, 'update'])->name('update');
        Route::delete('/{meeting}',   [GuruMeetingController::class, 'destroy'])->name('destroy');

        
    });

    Route::prefix('classes/{class}/meetings/{meeting}')->name('classes.meetings.')->group(function () {
        Route::get('attendance', [GuruAttendanceController::class, 'show'])->name('attendance.show');
        Route::post('attendance', [GuruAttendanceController::class, 'store'])->name('attendance.store');
    });

    Route::prefix('attendance-sessions/{session}')->name('attendance-sessions.')->group(function () {
        Route::patch('extend', [GuruAttendanceController::class, 'extend'])->name('extend');
        Route::patch('close', [GuruAttendanceController::class, 'close'])->name('close');
    });

    Route::patch('attendances/{attendance}/status', [GuruAttendanceController::class, 'updateStatus'])
        ->name('attendances.status.update');

    // Materi — nested di bawah meeting (KODE LAMA, TETAP SEPERTI SEMULA, TIDAK DIUBAH)
    Route::prefix('meetings/{meeting}')->name('meetings.')->group(function () {
        Route::resource('materials', GuruMaterialController::class)
            ->except(['show']);

        Route::get('materials/{material}/preview', [GuruMaterialController::class, 'show'])
            ->name('materials.show');

        Route::prefix('materials/{material}/blocks')->name('materials.blocks.')->group(function () {
            Route::post('/',         [GuruMaterialController::class, 'storeBlock'])->name('store');
            Route::delete('/{block}', [GuruMaterialController::class, 'destroyBlock'])->name('destroy');
            Route::post('/reorder',  [GuruMaterialController::class, 'reorderBlocks'])->name('reorder');
        });
    });
    Route::prefix('classes/{class}/assignments/{assignment}/grade')->name('classes.assignments.grade.')->group(function () {
        Route::get('/',                                              [GuruSubmissionController::class, 'show'])->name('show');
        Route::post('/submissions/{submission}',                     [GuruSubmissionController::class, 'grade'])->name('store');
        Route::post('/submissions/{submission}/revise',              [GuruSubmissionController::class, 'requestRevision'])->name('revise');
        Route::patch('/submissions/{submission}/revision-deadline',  [GuruSubmissionController::class, 'updateRevisionDeadline'])->name('revision-deadline.update');
        Route::post('/zip',                                          [GuruSubmissionController::class, 'generateZip'])->name('zip.generate');
        Route::get('/zip/download',                                  [GuruSubmissionController::class, 'downloadZip'])->name('zip.download');
    });

    Route::prefix('quizzes/questions')->name('quizzes.questions.')->group(function () {
        Route::get('/',                [GuruQuestionController::class, 'index'])->name('index');
        Route::get('/create',          [GuruQuestionController::class, 'create'])->name('create');
        Route::post('/',               [GuruQuestionController::class, 'store'])->name('store');
        Route::get('/create-bulk',     [GuruQuestionController::class, 'createBulk'])->name('create-bulk');   // ← baru
        Route::post('/store-bulk',     [GuruQuestionController::class, 'storeBulk'])->name('store-bulk');     // ← baru
        Route::get('/{question}/edit', [GuruQuestionController::class, 'edit'])->name('edit');
        Route::patch('/{question}',    [GuruQuestionController::class, 'update'])->name('update');
        Route::delete('/{question}',   [GuruQuestionController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('classes/{class}/quizzes')->name('classes.quizzes.')->group(function () {
        Route::get('/',              [GuruQuizController::class, 'index'])->name('index');
        Route::get('/create',        [GuruQuizController::class, 'create'])->name('create');
        Route::post('/',             [GuruQuizController::class, 'store'])->name('store');
        Route::get('/{quiz}/edit',   [GuruQuizController::class, 'edit'])->name('edit');
        Route::patch('/{quiz}',      [GuruQuizController::class, 'update'])->name('update');
        Route::delete('/{quiz}',     [GuruQuizController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('classes/{class}/quizzes/{quiz}/results')->name('classes.quizzes.results.')->group(function () {
        Route::get('/',                              [QuizResultController::class, 'show'])->name('index');
        Route::get('/attempt/{attempt}',             [QuizResultController::class, 'attempt'])->name('attempt');
        Route::post('/attempt/{attempt}/answers/{answer}', [QuizResultController::class, 'gradeAnswer'])->name('grade-answer');
        Route::post('/release',                      [QuizResultController::class, 'releaseScores'])->name('release');
    });

    Route::prefix('classes/{class}/grade-components')->name('classes.grade-components.')->group(function () {
        Route::get('/',            [GradeComponentController::class, 'index'])->name('index');
        Route::post('/',           [GradeComponentController::class, 'store'])->name('store');
        Route::patch('/{gradeComponent}',  [GradeComponentController::class, 'update'])->name('update');
        Route::delete('/{gradeComponent}', [GradeComponentController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('classes/{class}/kkm')->name('classes.kkm.')->group(function () {
        Route::get('/',  [GuruKkmController::class, 'index'])->name('index');
        Route::post('/', [GuruKkmController::class, 'store'])->name('store');
    });

    Route::prefix('classes/{class}/grades')->name('classes.grades.')->group(function () {
        Route::get('/',              [GradeController::class, 'index'])->name('index');
        Route::post('/manual',       [GradeController::class, 'storeManual'])->name('manual.store');
        Route::post('/recalculate',  [GradeController::class, 'recalculate'])->name('recalculate');
        Route::get('/export/excel', [GradeExportController::class, 'excel'])->name('export.excel');
        Route::get('/export/pdf',   [GradeExportController::class, 'pdf'])->name('export.pdf');
    });

    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/',       [GuruAnnouncementController::class, 'index'])->name('index');
        Route::get('/create', [GuruAnnouncementController::class, 'create'])->name('create');
        Route::post('/',      [GuruAnnouncementController::class, 'store'])->name('store');
        Route::delete('/{announcement}', [GuruAnnouncementController::class, 'destroy'])->name('destroy');
    });

    // ── Pesan (Chat) ───────────────────────────────────────────────
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/',                    [GuruMessageController::class, 'index'])->name('index');
        Route::post('/start/{studentUser}', [GuruMessageController::class, 'startDirect'])->name('start');
        Route::get('/{conversation}',      [GuruMessageController::class, 'show'])->name('show');
        Route::post('/{conversation}/send', [GuruMessageController::class, 'send'])->name('send');
        Route::post('/broadcast/{class}',  [GuruMessageController::class, 'broadcast'])->name('broadcast.store');
        Route::get('/broadcast/{conversation}/status', [GuruMessageController::class, 'broadcastShow'])->name('broadcast.show');
    });
    
    
    Route::get('calendar', [\App\Http\Controllers\Guru\CalendarController::class, 'index'])->name('calendar.index');

    Route::post('discussions/{material}', [\App\Http\Controllers\Guru\DiscussionController::class, 'store'])->name('discussions.store');
    Route::delete('discussions/{discussion}', [\App\Http\Controllers\Guru\DiscussionController::class, 'destroy'])->name('discussions.destroy');
});

// ── Siswa ──────────────────────────────────────────────
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaDashboard::class, 'index'])->name('dashboard');

    Route::prefix('classes')->name('classes.')->group(function () {
        Route::get('/',       [SiswaClassController::class, 'index'])->name('index');

        Route::get('/join',   [SiswaClassController::class, 'showJoin'])->name('join');
        Route::post('/join',  [SiswaClassController::class, 'join'])->name('join.post');

        Route::get('/{class}', [SiswaClassController::class, 'show'])->name('show');
        
    });

    // Materi siswa — nested di bawah kelas
    Route::prefix('kelas/{schoolClass}')->name('classes.')->group(function () {
        Route::get('materi',                      [SiswaMaterialController::class, 'index'])->name('materials.index');
        Route::get('materi/{meeting}/{material}', [SiswaMaterialController::class, 'show'])->name('materials.show');
    });
    Route::prefix('classes/{class}/meetings/{meeting}')->name('classes.meetings.')->group(function () {
        Route::resource('materials', GuruMaterialController::class)->except(['show']);
    });
    Route::prefix('classes/{class}/assignments')->name('classes.assignments.')->group(function () {
        Route::get('/',                [SiswaAssignmentController::class, 'index'])->name('index');
        Route::get('/{assignment}',    [SiswaAssignmentController::class, 'show'])->name('show');
        Route::post('/{assignment}',   [SiswaAssignmentController::class, 'submit'])->name('submit');
    });

    // Progress — standalone (tidak perlu schoolClass)
    Route::post('materi/{material}/progress', [SiswaMaterialController::class, 'updateProgress'])->name('materials.progress');
    Route::post('materi/{material}/selesai',  [SiswaMaterialController::class, 'markComplete'])->name('materials.complete');
    Route::post('materi/{material}/blocks/{contentBlock}/selesai', [SiswaMaterialController::class, 'completeBlock'])->name('materials.blocks.complete');

    Route::prefix('classes/{schoolClass}/quizzes')->name('classes.quizzes.')->group(function () {
        Route::get('/',                                   [SiswaQuizController::class, 'index'])->name('index');
        Route::post('/{quiz}/start',                      [SiswaQuizController::class, 'start'])->name('start');
        Route::get('/{quiz}/attempt/{attempt}',           [SiswaQuizController::class, 'attempt'])->name('attempt');
        Route::post('/{quiz}/attempt/{attempt}/answer',   [SiswaQuizController::class, 'saveAnswer'])->name('answer');
        Route::post('/{quiz}/attempt/{attempt}/tab-switch', [SiswaQuizController::class, 'reportTabSwitch'])->name('tab-switch');
        Route::post('/{quiz}/attempt/{attempt}/submit',   [SiswaQuizController::class, 'submit'])->name('submit');
        Route::get('/{quiz}/attempt/{attempt}/result',    [SiswaQuizController::class, 'result'])->name('result');
    });
    
    Route::get('classes/{schoolClass}/grades', [SiswaGradeController::class, 'index'])->name('classes.grades.index');

    Route::prefix('classes/{schoolClass}')->name('classes.attendance.')->group(function () {
        Route::get('attendance', [SiswaAttendanceController::class, 'index'])->name('index');
        Route::post('attendance/checkin', [SiswaAttendanceController::class, 'checkin'])->name('checkin');
    });

    // ── Pesan (Chat) ───────────────────────────────────────────────
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/',                    [SiswaMessageController::class, 'index'])->name('index');
        Route::post('/start/{teacherUser}', [SiswaMessageController::class, 'startDirect'])->name('start');
        Route::get('/{conversation}',      [SiswaMessageController::class, 'show'])->name('show');
        Route::post('/{conversation}/send', [SiswaMessageController::class, 'send'])->name('send');
        Route::post('/broadcast/{broadcast}/reply', [SiswaMessageController::class, 'replyBroadcast'])->name('broadcast.reply');
    });

    // ── Kalender ───────────────────────────────────────────────
    Route::get('calendar', [\App\Http\Controllers\Siswa\CalendarController::class, 'index'])->name('calendar.index');

    Route::post('discussions/{material}', [\App\Http\Controllers\Siswa\DiscussionController::class, 'store'])->name('discussions.store');
    Route::delete('discussions/{discussion}', [\App\Http\Controllers\Siswa\DiscussionController::class, 'destroy'])->name('discussions.destroy');

    Route::get('dashboard/progress', [\App\Http\Controllers\Siswa\DashboardController::class, 'progress'])
        ->name('dashboard.progress');

    Route::post('dashboard/heartbeat', [\App\Http\Controllers\Siswa\DashboardController::class, 'heartbeat'])
        ->name('dashboard.heartbeat');
});
