<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class SecurityAuditLogger
{
    /**
     * Log successful authentication event.
     */
    public static function logLoginSuccess(User $user): void
    {
        Log::channel('single')->info("Security Audit [LOGIN_SUCCESS]: User {$user->id} ({$user->email}) logged in successfully.", [
            'user_id' => $user->id,
            'email' => $user->email,
            'account_type' => $user->account_type?->value,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log failed authentication attempt (Possible Brute-Force / Password Guessing).
     */
    public static function logLoginFailure(string $email, string $reason = 'Invalid credentials'): void
    {
        Log::channel('single')->warning("Security Audit [LOGIN_FAILED]: Failed login attempt for email '{$email}'. Reason: {$reason}", [
            'email' => $email,
            'reason' => $reason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log account suspension block event.
     */
    public static function logSuspendedAccess(User $user): void
    {
        Log::channel('single')->alert("Security Audit [SUSPENDED_ACCESS_ATTEMPT]: Suspended user {$user->id} ({$user->email}) tried to login.", [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log suspicious file upload rejection.
     */
    public static function logBlockedFileUpload(string $originalName, string $detectedMime): void
    {
        Log::channel('single')->warning("Security Audit [BLOCKED_FILE_UPLOAD]: Rejected suspicious file '{$originalName}'. Detected MIME: '{$detectedMime}'", [
            'file_name' => $originalName,
            'detected_mime' => $detectedMime,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log general security & domain action event.
     */
    public static function logAction(
        string $action,
        string $description,
        ?string $userId = null,
        ?string $modelType = null,
        ?string $modelId = null,
        array $metadata = []
    ): void {
        Log::channel('single')->info("Security Audit [{$action}]: {$description}", [
            'action' => $action,
            'description' => $description,
            'user_id' => $userId ?? (auth()->check() ? auth()->id() : null),
            'model_type' => $modelType,
            'model_id' => $modelId,
            'metadata' => $metadata,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
