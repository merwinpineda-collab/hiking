<?php
header('Content-Type: application/json');
include 'conn.php';

try {
    // Get user email from POST or session/localStorage equivalent
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Email is required']);
        exit();
    }

    // Prepare and bind
    $sql = "SELECT reserve_id, fullname, email, number, num_people, date, time, difficulty, notes, status FROM tb_reserve";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $reservations = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $reservations[] = [
            'reserve_id' => $row['reserve_id'],
            'fullname' => $row['fullname'],
            'email' => $row['email'],
            'number' => $row['number'],
            'num_people' => $row['num_people'],
            'date' => $row['date'],
            'start_time' => $row['time'],
            'difficulty' => $row['difficulty'],
            'notes' => $row['notes'],
            'status' => $row['status']
        ];
    }

    echo json_encode(['status' => 'success', 'reservations' => $reservations]);
    $stmt->closeCursor();
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
