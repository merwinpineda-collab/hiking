<?php
// activity_log.php — Logs user and admin actions for accountability
include 'conn.php';

/**
 * Log an activity to the tb_activity_log table.
 *
 * @param string      $actor_type  'admin' | 'user'
 * @param string|null $actor_email Email of the actor (null = system)
 * @param string      $action      e.g. 'login', 'logout', 'booking_created', 'booking_updated', 'booking_deleted'
 * @param string|null $detail      Optional extra context
 */
function logActivity(string $actor_type, ?string $actor_email, string $action, ?string $detail = null): void {
    global $conn;
    try {
        $ip  = $_SERVER['REMOTE_ADDR'] ?? null;
        $ua  = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $sql = "INSERT INTO tb_activity_log (actor_type, actor_email, action, detail, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$actor_type, $actor_email, $action, $detail, $ip, $ua]);
    } catch (Throwable $e) {
        // Silently swallow logging errors — never break the main flow
        error_log('activity_log error: ' . $e->getMessage());
    }
}
?>