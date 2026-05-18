<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'conn.php';

try {
    $reserve_id = $_POST['reserve_id'] ?? '';
    $email = $_POST['email'] ?? '';
    $fullname = $_POST['fullname'] ?? '';
    $number = $_POST['number'] ?? '';
    $num_people = $_POST['num_people'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $difficulty = $_POST['difficulty'] ?? '';
    $notes = $_POST['notes'] ?? '';

    if (empty($reserve_id) || empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Reservation ID and Email are required']);
        exit();
    }

    // First, verify the reservation belongs to the user
    $check_sql = "SELECT reserve_id FROM tb_reserve WHERE reserve_id = ? AND email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bindParam(1, $reserve_id, PDO::PARAM_INT);
    $check_stmt->bindParam(2, $email, PDO::PARAM_STR);
    $check_stmt->execute();

    if ($check_stmt->rowCount() == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Reservation not found or access denied']);
        exit();
    }

    // Update the reservation
    $update_sql = "UPDATE tb_reserve SET fullname = ?, email = ?, number = ?, num_people = ?, date = ?, time = ?, difficulty = ?, notes = ? WHERE reserve_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bindParam(1, $fullname, PDO::PARAM_STR);
    $update_stmt->bindParam(2, $email, PDO::PARAM_STR);
    $update_stmt->bindParam(3, $number, PDO::PARAM_STR);
    $update_stmt->bindParam(4, $num_people, PDO::PARAM_INT);
    $update_stmt->bindParam(5, $date, PDO::PARAM_STR);
    $update_stmt->bindParam(6, $time, PDO::PARAM_STR);
    $update_stmt->bindParam(7, $difficulty, PDO::PARAM_STR);
    $update_stmt->bindParam(8, $notes, PDO::PARAM_STR);
    $update_stmt->bindParam(9, $reserve_id, PDO::PARAM_INT);

    if ($update_stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Reservation updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update reservation']);
    }

    $check_stmt->closeCursor();
    $update_stmt->closeCursor();
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
