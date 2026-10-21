<?php
 
use App\Http\Controllers\Api\V1\AuthController; 
use Illuminate\Support\Facades\Route;
 
Route::prefix('auth')->group(function () {
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::post('/email/verify', [AuthController::class, 'verifyEmail']);
    });

    Route::middleware(['auth:sanctum', 'blocked'])->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::patch('/me', [AuthController::class, 'updateMe'])->middleware('verified');
        Route::post('/me/avatar', [AuthController::class, 'updateAvatar'])->middleware('verified');
        Route::delete('/me/avatar', [AuthController::class, 'destroyAvatar'])->middleware('verified');
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/email/resend', [AuthController::class, 'resendVerification'])
            ->middleware('throttle:5,1');
    });
}); 

// Cleanup
// Route::middleware(['auth:sanctum', 'blocked', 'verified', 'kyc'])->group(function () {
     
// });
 