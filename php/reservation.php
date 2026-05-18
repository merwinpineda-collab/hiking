<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'conn.php';

try {
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $number = $_POST['number'] ?? '';
    $num_people = $_POST['num_people'] ?? '1';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $difficulty = $_POST['difficulty'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $status = 'pending';

    // Prepare and bind
    $sql = "INSERT INTO tb_reserve (fullname, email, number, num_people, date, time, difficulty, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $fullname, PDO::PARAM_STR);
    $stmt->bindParam(2, $email, PDO::PARAM_STR);
    $stmt->bindParam(3, $number, PDO::PARAM_STR);
    $stmt->bindParam(4, $num_people, PDO::PARAM_STR);
    $stmt->bindParam(5, $date, PDO::PARAM_STR);
    $stmt->bindParam(6, $time, PDO::PARAM_STR);
    $stmt->bindParam(7, $difficulty, PDO::PARAM_STR);
    $stmt->bindParam(8, $notes, PDO::PARAM_STR);
    $stmt->bindParam(9, $status, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Reservation submitted successfully!']);
    } else {
        throw new PDOException('Execute failed');
    }

    $stmt->closeCursor();
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
