<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log an action
     */
    public function log(
        string $action,
        string $description,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): AuditLog {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            throw new \Exception('No authenticated admin found for audit log');
        }

        return AuditLog::create([
            'admin_id' => $admin->id,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    /**
     * Log a create action
     */
    public function logCreate(string $modelType, $model, string $description): AuditLog
    {
        return $this->log(
            'created',
            $description,
            $modelType,
            $model->id,
            null,
            $model->toArray()
        );
    }

    /**
     * Log an update action
     */
    public function logUpdate(string $modelType, $model, array $oldValues, string $description): AuditLog
    {
        return $this->log(
            'updated',
            $description,
            $modelType,
            $model->id,
            $oldValues,
            $model->toArray()
        );
    }

    /**
     * Log a delete action
     */
    public function logDelete(string $modelType, $model, string $description): AuditLog
    {
        return $this->log(
            'deleted',
            $description,
            $modelType,
            $model->id,
            $model->toArray(),
            null
        );
    }

    /**
     * Log a status change
     */
    public function logStatusChange(string $modelType, $model, string $oldStatus, string $newStatus, string $description): AuditLog
    {
        return $this->log(
            'status_changed',
            $description,
            $modelType,
            $model->id,
            ['status' => $oldStatus],
            ['status' => $newStatus]
        );
    }

    /**
     * Log a login action
     */
    public function logLogin(): AuditLog
    {
        $admin = Auth::guard('admin')->user();
        
        return AuditLog::create([
            'admin_id' => $admin->id,
            'action' => 'login',
            'description' => "Admin {$admin->fname} {$admin->lname} logged in",
        ]);
    }

    /**
     * Log a logout action
     */
    public function logLogout(): void
    {
        $admin = Auth::guard('admin')->user();
        
        if ($admin) {
            AuditLog::create([
                'admin_id' => $admin->id,
                'action' => 'logout',
                'description' => "Admin {$admin->fname} {$admin->lname} logged out",
            ]);
        }
    }
}
