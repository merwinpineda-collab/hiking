<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'conn.php';

try {
    $reserve_id = $_POST['reserve_id'] ?? '';
    $email = $_POST['email'] ?? '';

    if (empty($reserve_id) || empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Reservation ID and Email are required']);
        exit();
    }

    // First, verify the reservation belongs to the user and fetch its status
    $check_sql = "SELECT reserve_id, status FROM tb_reserve WHERE reserve_id = ? AND email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bindParam(1, $reserve_id, PDO::PARAM_INT);
    $check_stmt->bindParam(2, $email, PDO::PARAM_STR);
    $check_stmt->execute();

    $row = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['status' => 'error', 'message' => 'Reservation not found or access denied']);
        exit();
    }

    // Lock confirmed reservations (cannot be cancelled by user)
    if (isset($row['status']) && $row['status'] === 'confirmed') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Confirmed reservations cannot be cancelled.'
        ]);
        exit();
    }

    // Delete the reservation (pending/other statuses)
    $delete_sql = "DELETE FROM tb_reserve WHERE reserve_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bindParam(1, $reserve_id, PDO::PARAM_INT);

    if ($delete_stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Reservation cancelled successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to cancel reservation']);
    }

    $check_stmt->closeCursor();
    $delete_stmt->closeCursor();
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
