<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = "activity_logs";
    protected $primaryKey = "log_id";

    protected $useAutoIncrement = true;
    protected $returnType = "array";

    protected $allowedFields = [
        "user_id",
        "action",
        "description",
        "ip_address",
        "user_agent",
        "additional_data",
        "created_at",
    ];

    protected $useTimestamps = false;

    /**
     * Log user activity
     *
     * @param int    $userId        User ID
     * @param string $action        Action performed
     * @param string $description   Description of the action
     * @param string $ipAddress     IP Address
     * @param array  $additionalData Any additional data to log
     * @return bool
     */
    public function logActivity(
        int $userId,
        string $action,
        string $description,
        string $ipAddress = "",
        array $additionalData = []
    ) {
        $data = [
            "user_id" => $userId,
            "action" => $action,
            "description" => $description,
            "ip_address" => $ipAddress,
            "user_agent" => $_SERVER["HTTP_USER_AGENT"] ?? "",
            "additional_data" => !empty($additionalData)
                ? json_encode($additionalData)
                : null,
            "created_at" => date("Y-m-d H:i:s"),
        ];

        return $this->insert($data);
    }

    /**
     * Get activity logs for a specific user
     *
     * @param int $userId User ID
     * @param int $limit  Number of records to return
     * @param int $offset Offset for pagination
     * @return array
     */
    public function getUserLogs(int $userId, int $limit = 10, int $offset = 0)
    {
        return $this->where("user_id", $userId)
            ->orderBy("created_at", "DESC")
            ->limit($limit, $offset)
            ->findAll();
    }

    /**
     * Get all recent activity logs
     *
     * @param int $limit  Number of records to return
     * @param int $offset Offset for pagination
     * @return array
     */
    public function getRecentLogs(int $limit = 50, int $offset = 0)
    {
        return $this->orderBy("created_at", "DESC")
            ->limit($limit, $offset)
            ->findAll();
    }
}
