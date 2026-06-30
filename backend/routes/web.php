<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\SystemHealthController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AiSettingsController;

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Redirect root to dashboard
// Route::redirect('/', '/admin/dashboard');

Route::get('/', function () {
    return view('welcome');
});

// Admin Panel RoutesProtected by Session Authentication
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    // Dashboard Core Overview
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Case Management (Hazards)
    Route::get('cases', [CaseController::class, 'index'])->name('cases.index');
    Route::get('cases/{id}', [CaseController::class, 'show'])->name('cases.show');
    Route::post('cases/{id}/upload-image', [CaseController::class, 'uploadImage'])->name('cases.upload-image');
    Route::post('cases/{id}/verify', [CaseController::class, 'verify'])->name('cases.verify');
    Route::post('cases/{id}/reject', [CaseController::class, 'reject'])->name('cases.reject');
    Route::post('cases/{id}/resolve', [CaseController::class, 'resolve'])->name('cases.resolve');
    Route::post('cases/{id}/archive', [CaseController::class, 'archive'])->name('cases.archive');
    Route::delete('cases/{id}/delete', [CaseController::class, 'destroy'])->name('cases.destroy');

    // Map Center (Live Hazard Monitoring Map)
    Route::get('maps', [MapController::class, 'index'])->name('maps.index');

    // User Directory
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::post('users/{id}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
    Route::post('users/{id}/activate', [UserController::class, 'activate'])->name('users.activate');

    // AI Intelligence Center
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('dashboard', [AiController::class, 'dashboard'])->name('dashboard');
        Route::get('logs', [AiController::class, 'logs'])->name('logs');
    });

    // AI Settings Management (Config)
    Route::get('ai-settings', [AiSettingsController::class, 'index'])->name('ai-settings.index');
    Route::put('ai-settings', [AiSettingsController::class, 'update'])->name('ai-settings.update');
    Route::get('ai-settings/history', [AiSettingsController::class, 'history'])->name('ai-settings.history');

    // Notifications Hub (FCM)
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/send', [NotificationController::class, 'send'])->name('notifications.send');

    // Analytics Reporting
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Activity Audit Logs
    Route::get('logs', [LogController::class, 'index'])->name('logs.index');

    // System Health Metrics
    Route::get('health', [SystemHealthController::class, 'index'])->name('health.index');

    // System Settings & CRUD Categories
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/alerts', [SettingsController::class, 'updateAlerts'])->name('settings.alerts');
    Route::post('settings/system', [SettingsController::class, 'updateSystem'])->name('settings.system');
    Route::post('settings/showcase', [SettingsController::class, 'updateShowcase'])->name('settings.showcase');
    Route::post('settings/maintenance', [SettingsController::class, 'updateMaintenance'])->name('settings.maintenance');
    Route::post('settings/categories', [SettingsController::class, 'storeCategory'])->name('settings.categories.store');
    Route::post('settings/categories/{id}', [SettingsController::class, 'updateCategory'])->name('settings.categories.update');
    Route::delete('settings/categories/{id}/delete', [SettingsController::class, 'destroyCategory'])->name('settings.categories.destroy');

    // Developer Portal Docs Page
    Route::get('dev-docs', [\App\Http\Controllers\DeveloperPortalController::class, 'index'])->name('dev-docs');
});

// Municipal Panel Routes
Route::prefix('municipality')->name('municipality.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\MunicipalityPanelController::class, 'dashboard'])->name('dashboard');
    Route::get('cases', [\App\Http\Controllers\MunicipalityPanelController::class, 'indexCases'])->name('cases.index');
    Route::get('cases/{id}', [\App\Http\Controllers\MunicipalityPanelController::class, 'showCase'])->name('cases.show');
    Route::post('cases/{id}/status', [\App\Http\Controllers\MunicipalityPanelController::class, 'updateStatus'])->name('cases.status');
    Route::post('cases/{id}/comment', [\App\Http\Controllers\MunicipalityPanelController::class, 'storeComment'])->name('cases.comment');
});

// API Routes for Android Client
Route::prefix('api')->group(function () {
    // Developer API endpoints
    Route::prefix('developer')->group(function () {
        Route::get('billboards', [\App\Http\Controllers\Api\DeveloperApiController::class, 'billboards']);
        Route::get('advertisements', [\App\Http\Controllers\Api\DeveloperApiController::class, 'advertisements']);
        Route::get('road-notices', [\App\Http\Controllers\Api\DeveloperApiController::class, 'roadNotices']);
        Route::get('ev-hazards', [\App\Http\Controllers\Api\DeveloperApiController::class, 'evHazards']);
    });

    // Public Authentication endpoints
    Route::post('auth/register', [\App\Http\Controllers\Api\AuthApiController::class, 'register']);
    Route::post('auth/login', [\App\Http\Controllers\Api\AuthApiController::class, 'login']);
    Route::post('auth/google-login', [\App\Http\Controllers\Api\AuthApiController::class, 'googleLogin']);

    // Public Hazards endpoints (accessible by guest and authenticated users)
    Route::get('hazards', [\App\Http\Controllers\Api\HazardApiController::class, 'getHazards']);

    // Authenticated actions (verify/resolve/profile/settings require login)
    Route::middleware('api.auth')->group(function () {
        Route::post('hazards', [\App\Http\Controllers\Api\HazardApiController::class, 'storeHazard']);
        Route::post('hazards/{id}/verify', [\App\Http\Controllers\Api\HazardApiController::class, 'verifyHazard']);
        Route::post('hazards/{id}/resolve', [\App\Http\Controllers\Api\HazardApiController::class, 'resolveHazard']);

        // Profile routes
        Route::get('profile/stats', [\App\Http\Controllers\Api\ProfileApiController::class, 'stats']);
        Route::get('profile/reports', [\App\Http\Controllers\Api\ProfileApiController::class, 'reports']);
        Route::get('profile/saved', [\App\Http\Controllers\Api\ProfileApiController::class, 'savedAlerts']);
        Route::put('profile', [\App\Http\Controllers\Api\ProfileApiController::class, 'update']);
        Route::put('profile/password', [\App\Http\Controllers\Api\ProfileApiController::class, 'changePassword']);
        Route::put('profile/security', [\App\Http\Controllers\Api\ProfileApiController::class, 'updateSecurity']);
        Route::put('profile/verification', [\App\Http\Controllers\Api\ProfileApiController::class, 'updateVerification']);

        // Settings routes
        Route::get('settings/preferences', [\App\Http\Controllers\Api\SettingsApiController::class, 'getPreferences']);
        Route::put('settings/preferences', [\App\Http\Controllers\Api\SettingsApiController::class, 'updatePreferences']);
    });

    // AI Processing
    Route::post('ai/analyze', [\App\Http\Controllers\Api\AiApiController::class, 'analyze']);
    Route::post('ai/regenerate-description', [\App\Http\Controllers\Api\AiApiController::class, 'regenerateDescription']);

    // Comments
    Route::get('hazards/{id}/comments', [\App\Http\Controllers\Api\CommentApiController::class, 'index']);
    Route::post('hazards/{id}/comments', [\App\Http\Controllers\Api\CommentApiController::class, 'store']);
    Route::delete('comments/{id}', [\App\Http\Controllers\Api\CommentApiController::class, 'destroy']);

    // Bookmarks & Likes
    Route::post('hazards/{id}/bookmark', [\App\Http\Controllers\Api\BookmarkApiController::class, 'store']);
    Route::delete('hazards/{id}/bookmark', [\App\Http\Controllers\Api\BookmarkApiController::class, 'destroy']);
    Route::post('hazards/{id}/like', [\App\Http\Controllers\Api\LikeApiController::class, 'store']);
    Route::delete('hazards/{id}/like', [\App\Http\Controllers\Api\LikeApiController::class, 'destroy']);

    // Maps & Navigation
    Route::get('maps/hazards', [\App\Http\Controllers\Api\MapApiController::class, 'hazards']);
    Route::get('maps/nearby', [\App\Http\Controllers\Api\MapApiController::class, 'nearby']);
    Route::post('maps/safe-route', [\App\Http\Controllers\Api\MapApiController::class, 'safeRoute']);
    Route::get('maps/live-alerts', [\App\Http\Controllers\Api\MapApiController::class, 'liveAlerts']);

    // Safe Ride
    Route::post('ride/start', [\App\Http\Controllers\Api\RideApiController::class, 'start']);
    Route::post('ride/end', [\App\Http\Controllers\Api\RideApiController::class, 'end']);
    Route::post('ride/share-location', [\App\Http\Controllers\Api\RideApiController::class, 'shareLocation']);

    // Notifications
    Route::get('notifications', [\App\Http\Controllers\Api\NotificationApiController::class, 'index']);
    Route::put('notifications/{id}/read', [\App\Http\Controllers\Api\NotificationApiController::class, 'markAsRead']);
    Route::delete('notifications/{id}', [\App\Http\Controllers\Api\NotificationApiController::class, 'destroy']);

    // General app settings
    Route::get('settings', [\App\Http\Controllers\Api\SettingsApiController::class, 'index']);
    Route::put('settings', [\App\Http\Controllers\Api\SettingsApiController::class, 'update']);
});

