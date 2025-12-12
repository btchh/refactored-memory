<?php

namespace App\Services;

use App\Models\LoginAttempt;

class RateLimitService
{
    /**
     * Action type configurations.
     * Each action has: warning threshold, redirect threshold, spam threshold, reset hours
     */
    protected array $config = [
        'login' => [
            'warning' => 3,      // Show "forgot password?" warning
            'redirect' => 5,     // Redirect to forgot password
            'spam' => 15,        // Block IP (obvious spam)
            'reset_hours' => 1,  // Reset after 1 hour
        ],
        'otp' => [
            'warning' => 2,
            'redirect' => 4,
            'spam' => 10,
            'reset_hours' => 1,
        ],
        'register' => [
            'warning' => 2,
            'redirect' => 4,
            'spam' => 10,
            'reset_hours' => 1,
        ],
        'booking' => [
            'warning' => 7,
            'redirect' => 12,
            'spam' => 25,
            'reset_hours' => 1,
        ],
    ];

    /**
     * Record a failed attempt and return the warning state.
     */
    public function recordFailedAttempt(string $identifier, string $ipAddress, string $actionType = 'login'): array
    {
        $attempt = LoginAttempt::findOrCreateFor($identifier, $ipAddress, $actionType);
        $config = $this->getConfig($actionType);
        
        // Reset if configured hours have passed
        if ($attempt->shouldResetAttempts($config['reset_hours'])) {
            $attempt->resetAttempts();
        }
        
        $attempt->incrementAttempts();
        
        return $this->getWarningState($attempt->failed_attempts, $actionType);
    }

    /**
     * Clear attempts on successful action.
     */
    public function clearAttempts(string $identifier, string $ipAddress, string $actionType = 'login'): void
    {
        LoginAttempt::where('identifier', strtolower($identifier))
            ->where('ip_address', $ipAddress)
            ->where('action_type', $actionType)
            ->delete();
    }

    /**
     * Get current attempt count.
     */
    public function getAttemptCount(string $identifier, string $ipAddress, string $actionType = 'login'): int
    {
        $attempt = LoginAttempt::where('identifier', strtolower($identifier))
            ->where('ip_address', $ipAddress)
            ->where('action_type', $actionType)
            ->first();
            
        if (!$attempt) {
            return 0;
        }
        
        $config = $this->getConfig($actionType);
        
        if ($attempt->shouldResetAttempts($config['reset_hours'])) {
            $attempt->resetAttempts();
            return 0;
        }
        
        return $attempt->failed_attempts;
    }

    /**
     * Determine the warning state based on attempt count.
     */
    public function getWarningState(int $attempts, string $actionType = 'login'): array
    {
        $config = $this->getConfig($actionType);
        
        // Spam attack - block completely
        if ($attempts >= $config['spam']) {
            return [
                'attempts' => $attempts,
                'action' => 'block',
                'message' => 'Too many attempts detected. Please try again later.',
            ];
        }
        
        // Redirect threshold reached
        if ($attempts >= $config['redirect']) {
            return [
                'attempts' => $attempts,
                'action' => 'redirect',
                'message' => $this->getRedirectMessage($actionType),
            ];
        }
        
        // Warning threshold reached
        if ($attempts >= $config['warning']) {
            $remaining = $config['redirect'] - $attempts;
            return [
                'attempts' => $attempts,
                'action' => 'warning',
                'message' => $this->getWarningMessage($actionType, $remaining),
            ];
        }
        
        return [
            'attempts' => $attempts,
            'action' => 'none',
            'message' => null,
        ];
    }

    /**
     * Check current state without incrementing.
     */
    public function checkState(string $identifier, string $ipAddress, string $actionType = 'login'): array
    {
        $attempts = $this->getAttemptCount($identifier, $ipAddress, $actionType);
        return $this->getWarningState($attempts, $actionType);
    }

    /**
     * Check if action should be blocked (spam detection).
     */
    public function shouldBlock(string $identifier, string $ipAddress, string $actionType = 'login'): bool
    {
        $config = $this->getConfig($actionType);
        return $this->getAttemptCount($identifier, $ipAddress, $actionType) >= $config['spam'];
    }

    /**
     * Check if should redirect (too many attempts but not spam).
     */
    public function shouldRedirect(string $identifier, string $ipAddress, string $actionType = 'login'): bool
    {
        $config = $this->getConfig($actionType);
        $attempts = $this->getAttemptCount($identifier, $ipAddress, $actionType);
        return $attempts >= $config['redirect'] && $attempts < $config['spam'];
    }

    /**
     * Get config for action type.
     */
    protected function getConfig(string $actionType): array
    {
        return $this->config[$actionType] ?? $this->config['login'];
    }

    /**
     * Get redirect message based on action type.
     */
    protected function getRedirectMessage(string $actionType): string
    {
        return match ($actionType) {
            'login' => 'Multiple failed attempts. Please reset your password or try again later.',
            'otp' => 'Too many OTP requests. Please wait before trying again.',
            'register' => 'Too many registration attempts. Please try again later.',
            'booking' => 'Too many booking attempts. Please slow down.',
            default => 'Too many attempts. Please try again later.',
        };
    }

    /**
     * Get warning message based on action type.
     */
    protected function getWarningMessage(string $actionType, int $remaining): string
    {
        return match ($actionType) {
            'login' => "Forgot your password? {$remaining} attempt(s) remaining.",
            'otp' => "{$remaining} OTP request(s) remaining.",
            'register' => "{$remaining} registration attempt(s) remaining.",
            'booking' => "{$remaining} booking attempt(s) remaining.",
            default => "{$remaining} attempt(s) remaining.",
        };
    }

    /**
     * Clean up old attempts (for scheduled task).
     */
    public function cleanupOldAttempts(): int
    {
        return LoginAttempt::where('last_attempt_at', '<', now()->subHours(24))->delete();
    }
}
