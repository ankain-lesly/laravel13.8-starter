<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Requests\Api\V1\ResetPasswordRequest;
use App\Http\Requests\Api\V1\UpdateAvatarRequest;
use App\Http\Requests\Api\V1\UpdateMeRequest;
use App\Http\Requests\Api\V1\VerifyEmailRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Http\Responses\ApiResponse;
use App\Mail\PasswordReset;
use App\Models\User;
// use App\Services\EmailVerificationService;
use App\Support\UsernameGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    private const AVATAR_DISK = 'public';

    public function __construct(
        // private EmailVerificationService $emailVerification,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $email = $request->string('email')->toString();

        $user = User::create([
            'first_name' => $request->string('first_name')->toString(),
            'last_name' => $request->string('last_name')->toString(),
            'username' => UsernameGenerator::fromEmail($email),
            'email' => $email,
            'password' => Hash::make($request->string('password')->toString()),
            'role' => UserRole::User,
        ]);


        $token = $user->createToken('api')->plainTextToken;

        return ApiResponse::success([
            'token' => $token,
            'user' => new UserResource($user),
        ], 'Registered successfully.', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()
            ->where('email', $request->string('email')->toString())
            ->first();

        if (! $user || ! Hash::check($request->string('password')->toString(), $user->password)) {
            return ApiResponse::error('Invalid login credentials.', 401);
        }

        if ($user->isBlocked()) {
            return ApiResponse::error('This account is blocked.', 403);
        }

        return $this->issueTokenResponse($user);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $email = $request->string('email')->toString();
        $user = User::query()->where('email', $email)->first();

        if ($user && ! $user->isBlocked()) {
            $token = Password::createToken($user);
        }

        return ApiResponse::success(
            null,
            'If an account exists for that email, a reset link has been sent.',
        );
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return ApiResponse::success(null, 'Password reset successfully.');
        }

        return ApiResponse::error(__($status), 422);
    }

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(new UserResource($request->user()));
    }

    public function updateMe(UpdateMeRequest $request): JsonResponse
    {
        $request->user()->fill($request->validated())->save();

        return ApiResponse::success(
            new UserResource($request->user()->fresh()),
            'Profile updated successfully.',
        );
    }

    public function updateAvatar(UpdateAvatarRequest $request): JsonResponse
    {
        $user = $request->user();
        $file = $request->file('avatar');
        if (! $file instanceof UploadedFile) {
            return ApiResponse::error('A photo file is required.', 422);
        }

        $this->storeAvatar($user, $file);

        return ApiResponse::success(
            new UserResource($user->fresh()),
            'Profile photo updated successfully.',
        );
    }

    public function destroyAvatar(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->deleteStoredAvatar($user);
        $user->forceFill(['avatar' => null])->save();

        return ApiResponse::success(
            new UserResource($user->fresh()),
            'Profile photo removed.',
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, 'Logged out successfully.');
    }

    public function resendVerification(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->email_verified_at) {
        }

        return ApiResponse::success(
            null,
            'If this email still needs confirming, a new link has been sent.',
        );
    }

    // NOTE IMPLEMENTED
    // public function verifyEmail(VerifyEmailRequest $request): JsonResponse
    // {
    //     $ok = $this->emailVerification->verify(
    //         $request->string('email')->toString(),
    //         $request->string('token')->toString(),
    //     );

    //     if (! $ok) {
    //         return ApiResponse::error('This confirmation link is invalid or has expired.', 422);
    //     }

    //     return ApiResponse::success(null, 'Email confirmed.');
    // }

    private function issueTokenResponse(User $user): JsonResponse
    {
        $token = $user->createToken('api')->plainTextToken;

        return ApiResponse::success([
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    private function storeAvatar(User $user, UploadedFile $file): void
    {
        $this->deleteStoredAvatar($user);

        $extension = strtolower((string) ($file->guessExtension() ?: $file->getClientOriginalExtension() ?: 'jpg'));
        $filename = $user->id.'.'.$extension;
        $path = $file->storeAs('avatars', $filename, self::AVATAR_DISK);

        $user->forceFill(['avatar' => $path])->save();
    }

    private function deleteStoredAvatar(User $user): void
    {
        $path = (string) $user->avatar;

        if ($path === '' || str_starts_with($path, 'data:') || str_starts_with($path, 'http')) {
            return;
        }

        Storage::disk(self::AVATAR_DISK)->delete($path);
    }
}
