<?php
header('Content-Type: application/json');
include 'conn.php';

try {
    // Combined booking listing for admin: online (tb_reserve) + walk-in (walkin_bookings)
    // Keep existing online reservations unchanged.

    $stmtOnline = $conn->prepare(
        "SELECT reserve_id AS booking_id,
                fullname AS full_name,
                email,
                date AS booking_date,
                time AS time_slot,
                num_people AS number_of_guests,
                difficulty AS service_name,
                'Online' AS booking_type,
                CASE status WHEN 'confirmed' THEN 'Confirmed'
                            WHEN 'pending' THEN 'Pending'
                            WHEN 'cancelled' THEN 'Cancelled'
                            ELSE status END AS booking_status,
                NULL AS payment_status,
                NULL AS payment_method,
                NULL AS amount_paid,
                NULL AS created_by_admin,
                notes
         FROM tb_reserve"
    );
    $stmtOnline->execute();

    $rows = [];
    while ($r = $stmtOnline->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $r;
    }

    $stmtWalkin = $conn->prepare(
        "SELECT id AS booking_id,
                full_name,
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
         FROM walkin_bookings"
    );
    $stmtWalkin->execute();
    while ($r = $stmtWalkin->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $r;
    }

    // Sort by date desc, then time_slot asc (best-effort; time_slot is string in walk-ins)
    usort($rows, function($a, $b) {
        if ($a['booking_date'] === $b['booking_date']) {
            return strcmp((string)$a['time_slot'], (string)$b['time_slot']);
        }
        return strcmp((string)$b['booking_date'], (string)$a['booking_date']);
    });

    echo json_encode(['status' => 'success', 'bookings' => $rows]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>

