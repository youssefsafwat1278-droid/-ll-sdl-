<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\ScoutController;
use App\Http\Controllers\PatrolController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\GameweekController;
use App\Http\Controllers\ChipController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PointsController;
use App\Http\Controllers\Admin\GameweekManagementController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\SummaryController;
use App\Http\Controllers\Admin\ScoutImageController;
use App\Http\Controllers\Admin\UserRegistrationController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    
    Route::get('/my-team', [TeamController::class, 'index'])->name('my-team');
    Route::get('/team/builder', [TeamController::class, 'showBuilder'])->name('team.builder');
    Route::post('/team/select', [TeamController::class, 'selectTeam'])->name('team.select');
    Route::put('/team/captain', [TeamController::class, 'changeCaptain'])->name('team.captain');
    Route::post('/team/triple-captain', [TeamController::class, 'activateTripleCaptain'])->name('team.triple-captain');
    Route::post('/team/free-hit', [TeamController::class, 'activateFreeHit'])->name('team.free-hit');

    Route::get('/transfers', [TransferController::class, 'index'])->name('transfers');
    Route::post('/transfers', [TransferController::class, 'makeTransfers'])->name('transfers.make');
    Route::get('/transfers/history', [TransferController::class, 'history'])->name('transfers.history');
    
    Route::get('/scouts', [ScoutController::class, 'index'])->name('scouts.index');
    Route::get('/scouts/{scout}', [ScoutController::class, 'show'])->name('scouts.show');
    Route::get('/scouts/search', [ScoutController::class, 'search'])->name('scouts.search');
    
    Route::get('/patrols', [PatrolController::class, 'index'])->name('patrols.index');
    Route::get('/patrols/{patrol}', [PatrolController::class, 'show'])->name('patrols.show');
    
    Route::get('/rankings', [RankingController::class, 'overall'])->name('rankings');
    Route::get('/rankings/me', [RankingController::class, 'myRanking'])->name('rankings.me');
    Route::get('/rankings/gameweek', [RankingController::class, 'topGameweek'])->name('rankings.gameweek');
    
    Route::get('/gameweeks', [GameweekController::class, 'index'])->name('gameweeks.index');
    Route::get('/gameweeks/current', [GameweekController::class, 'current'])->name('gameweeks.current');
    Route::get('/gameweeks/{gameweek}', [GameweekController::class, 'show'])->name('gameweeks.show');
    
    Route::post('/chips/use', [ChipController::class, 'use'])->name('chips.use');
    
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('/notifications', [HomeController::class, 'notifications'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [HomeController::class, 'markAsRead'])->name('notifications.read');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/summary', [SummaryController::class, 'index'])->name('summary');
    Route::get('/summary/{scout}', [SummaryController::class, 'show'])->name('summary.show');
    Route::get('/attendance-stats', [SummaryController::class, 'attendanceStats'])->name('attendance-stats');

    Route::get('/points', [PointsController::class, 'index'])->name('points.index');
    Route::get('/points/{scout}', [PointsController::class, 'show'])->name('points.show');
    Route::post('/points', [PointsController::class, 'store'])->name('points.store');
    Route::delete('/points/{scout}', [PointsController::class, 'destroy'])->name('points.destroy');
    Route::post('/points/upload-excel', [PointsController::class, 'uploadExcel'])->name('points.upload');
    
    Route::post('/gameweeks', [GameweekManagementController::class, 'store'])->name('gameweeks.store');
    Route::put('/gameweeks/{gameweek}', [GameweekManagementController::class, 'update'])->name('gameweeks.update');
    Route::delete('/gameweeks/{gameweek}', [GameweekManagementController::class, 'destroy'])->name('gameweeks.destroy');
    Route::match(['get', 'post'], '/gameweeks/{gameweek}/finalize', [GameweekManagementController::class, 'finalize'])->name('gameweeks.finalize');
    Route::post('/gameweeks/{gameweek}/refresh-points', [GameweekManagementController::class, 'refreshPoints'])->name('gameweeks.refresh-points');
    Route::get('/gameweeks', [GameweekManagementController::class, 'index'])->name('gameweeks.index');
    
    Route::get('/news', [NewsController::class, 'index'])->name('news.index');
    Route::get('/news/create', [NewsController::class, 'create'])->name('news.create');
    Route::post('/news', [NewsController::class, 'store'])->name('news.store');
    Route::get('/news/{news}/edit', [NewsController::class, 'edit'])->name('news.edit');
    Route::put('/news/{news}', [NewsController::class, 'update'])->name('news.update');
    Route::delete('/news/{news}', [NewsController::class, 'destroy'])->name('news.destroy');
    Route::post('/news/{news}/toggle-featured', [NewsController::class, 'toggleFeatured'])->name('news.toggle-featured');
    
    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics');
    Route::get('/user-registrations', [UserRegistrationController::class, 'index'])->name('user-registrations.index');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');

    Route::get('/scout-images', [ScoutImageController::class, 'index'])->name('scout-images.index');
    Route::post('/scout-images/{scout}/upload', [ScoutImageController::class, 'uploadScoutPhoto'])->name('scout-images.upload');
    Route::delete('/scout-images/{scout}', [ScoutImageController::class, 'deleteScoutPhoto'])->name('scout-images.delete');
    Route::post('/patrol-images/{patrol}/upload', [ScoutImageController::class, 'uploadPatrolLogo'])->name('patrol-images.upload');
    Route::delete('/patrol-images/{patrol}', [ScoutImageController::class, 'deletePatrolLogo'])->name('patrol-images.delete');
});
