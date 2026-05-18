<?php
// confirmreservation.php — confirms a reservation, sends email, logs activity
header('Content-Type: application/json');
include 'conn.php';
include 'activity_log.php';
include 'mailer.php';

try {
    $reserve_id = intval($_POST['reserve_id'] ?? 0);

    if ($reserve_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Reservation ID is required']);
        exit();
    }

    // ── Fetch reservation details BEFORE updating ─────────────────────────
    $fetch = $conn->prepare(
        "SELECT fullname, email, date, time, status FROM tb_reserve WHERE reserve_id = ?"
    );
    $fetch->execute([$reserve_id]);
    $reservation = $fetch->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        echo json_encode(['status' => 'error', 'message' => 'Reservation not found']);
        exit();
    }

    // Already confirmed — idempotent guard
    if ($reservation['status'] === 'confirmed') {
        echo json_encode(['status' => 'success', 'message' => 'Already confirmed.', 'email_sent' => false]);
        exit();
    }

    // ── Update status to confirmed ────────────────────────────────────────
    $sql  = "UPDATE tb_reserve
             SET status = 'confirmed', confirmed_at = NOW()
             WHERE reserve_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt->execute([$reserve_id])) {
        throw new PDOException('Status update failed');
    }

    // ── Send confirmation email ───────────────────────────────────────────
    $emailSent = false;
    if (!empty($reservation['email'])) {
        $emailSent = sendBookingConfirmationEmail(
            $reservation['email'],
            $reservation['fullname'],
            $reserve_id,
            $reservation['date'],
            $reservation['time']
        );

        // Mark email_sent flag in DB
        if ($emailSent) {
            $markEmail = $conn->prepare(
                "UPDATE tb_reserve SET email_sent = 1 WHERE reserve_id = ?"
            );
            $markEmail->execute([$reserve_id]);
        }
    }

    // ── Log the action ────────────────────────────────────────────────────
    $actor = $_SESSION['admin_email'] ?? 'admin';
    logActivity(
        'admin',
        $actor,
        'booking_confirmed',
        "reserve_id={$reserve_id}, user_email={$reservation['email']}, email_sent=" . ($emailSent ? 'yes' : 'no')
    );

    echo json_encode([
        'status'     => 'success',
        'message'    => 'Reservation confirmed successfully!' . ($emailSent ? ' Confirmation email sent.' : ' (Email delivery pending — check SMTP config.)'),
        'email_sent' => $emailSent
    ]);

    $stmt->closeCursor();
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>