<?php

namespace App\Services;

/**
 * @deprecated Use RateLimitService instead. This class is kept for backward compatibility.
 */
class LoginAttemptService extends RateLimitService
{
    /**
     * Record a failed login attempt.
     */
    public function recordFailedAttempt(string $identifier, string $ipAddress, string $actionType = 'login'): array
    {
        return parent::recordFailedAttempt($identifier, $ipAddress, $actionType);
    }

    /**
     * Clear login attempts.
     */
    public function clearAttempts(string $identifier, string $ipAddress, string $actionType = 'login'): void
    {
        parent::clearAttempts($identifier, $ipAddress, $actionType);
    }

    /**
     * Get login attempt count.
     */
    public function getAttemptCount(string $identifier, string $ipAddress, string $actionType = 'login'): int
    {
        return parent::getAttemptCount($identifier, $ipAddress, $actionType);
    }

    /**
     * Check if should redirect to forgot password.
     */
    public function shouldRedirectToForgotPassword(string $identifier, string $ipAddress): bool
    {
        return parent::shouldRedirect($identifier, $ipAddress, 'login');
    }
}
