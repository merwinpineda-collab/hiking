<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'conn.php';

try {
    // Admin will submit walk-in booking data
    $full_name = trim($_POST['full_name'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $booking_date = $_POST['booking_date'] ?? '';
    $time_slot = trim($_POST['time_slot'] ?? '');
    $number_of_guests = $_POST['number_of_guests'] ?? 1;
    $service_name = trim($_POST['service_name'] ?? '');

    $created_by_admin = $_POST['created_by_admin'] ?? null;
    $notes = trim($_POST['notes'] ?? '');

    // Defaults per requirements
    $booking_type = 'Walk-in';
    $booking_status = 'Confirmed'; // walk-ins usually instant
    $payment_method = 'Cash';
    $payment_status = 'Paid';

    // Basic validation
    if ($full_name === '' || $contact_number === '' || $booking_date === '' || $time_slot === '') {

        echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
        exit();
    }

    if (!is_numeric($number_of_guests) || (int)$number_of_guests < 1) {
        $number_of_guests = 1;
    }

    // Fixed amount: ₱100 per head (always computed server-side)
    $amount_per_head = 100;
    $amount_paid = (int)$number_of_guests * $amount_per_head;

    $sql = "INSERT INTO walkin_bookings (
                full_name,
                contact_number,
                email,
                booking_date,
                time_slot,
                number_of_guests,
                service_name,
                booking_type,
                booking_status,
                payment_status,
                payment_method,
                amount_paid,
                created_by_admin,
                notes
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";

    $stmt = $conn->prepare($sql);

    $stmt->bindParam(1, $full_name, PDO::PARAM_STR);
    $stmt->bindParam(2, $contact_number, PDO::PARAM_STR);
    $stmt->bindParam(3, $email, PDO::PARAM_STR);
    $stmt->bindParam(4, $booking_date, PDO::PARAM_STR);
    $stmt->bindParam(5, $time_slot, PDO::PARAM_STR);
    $stmt->bindParam(6, $number_of_guests, PDO::PARAM_INT);
    $stmt->bindParam(7, $service_name, PDO::PARAM_STR);
    $stmt->bindParam(8, $booking_type, PDO::PARAM_STR);
    $stmt->bindParam(9, $booking_status, PDO::PARAM_STR);
    $stmt->bindParam(10, $payment_status, PDO::PARAM_STR);
    $stmt->bindParam(11, $payment_method, PDO::PARAM_STR);
    $stmt->bindParam(12, $amount_paid, $amount_paid === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindParam(13, $created_by_admin, $created_by_admin === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->bindParam(14, $notes, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Walk-in booking added successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to insert walk-in booking.']);
    }

    $stmt->closeCursor();
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>

