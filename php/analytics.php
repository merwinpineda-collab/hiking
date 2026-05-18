<?php
include 'conn.php';

header('Content-Type: application/json');

try {
    // 1. Total Users — count all registered users
    $users_stmt = $conn->query("SELECT COUNT(*) as total FROM tb_userinfo");
    $totalUsers = (int)$users_stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 2. Reservations This Month — filtered by current month & year
    $currentYear  = date('Y');
    $currentMonth = date('m');
    $res_stmt = $conn->prepare(
        "SELECT COUNT(*) as total FROM tb_reserve
         WHERE YEAR(date) = ? AND MONTH(date) = ?"
    );
    $res_stmt->execute([$currentYear, $currentMonth]);
    $reservationsThisMonth = (int)$res_stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 3. Active Tour Guides — total guides in the system
    $guides_stmt = $conn->query("SELECT COUNT(*) as total FROM tb_tourguides");
    $activeGuides = (int)$guides_stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 4. STC Count — Successful Tours Completed (confirmed reservations)
    $stc_stmt = $conn->query("SELECT COUNT(*) as total FROM tb_reserve WHERE status = 'confirmed'");
    $stcCount = (int)$stc_stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 5. Walk-in Bookings (total count for dashboard card)
    $walkin_count = (int)$conn->query("SELECT COUNT(*) as total_walkins FROM walkin_bookings")->fetch(PDO::FETCH_ASSOC)['total_walkins'];

    // Pie chart data (real-time)
    // Online bookings = tb_reserve (online)
    // Walk-in bookings = walkin_bookings (walk-in)
    $online_count = (int)$conn->query("SELECT COUNT(*) as total_online FROM tb_reserve")->fetch(PDO::FETCH_ASSOC)['total_online'];

    // Combined status overview for BOTH tables
    // Online status values are lowercase: pending/confirmed/cancelled
    // Walk-in status values are capitalized: Pending/Confirmed/Completed
    $online_pending_count = (int)$conn->query("SELECT COUNT(*) as total FROM tb_reserve WHERE status='pending'")->fetch(PDO::FETCH_ASSOC)['total'];
    $online_confirmed_count = (int)$conn->query("SELECT COUNT(*) as total FROM tb_reserve WHERE status='confirmed'")->fetch(PDO::FETCH_ASSOC)['total'];
    $online_cancelled_count = (int)$conn->query("SELECT COUNT(*) as total FROM tb_reserve WHERE status='cancelled'")->fetch(PDO::FETCH_ASSOC)['total'];

    $walkin_pending_count = (int)$conn->query("SELECT COUNT(*) as total FROM walkin_bookings WHERE booking_status='Pending'")->fetch(PDO::FETCH_ASSOC)['total'];
    // For dashboard purposes: treat walk-in 'Confirmed' and 'Completed' as Confirmed bucket
    $walkin_confirmed_count = (int)$conn->query("SELECT COUNT(*) as total FROM walkin_bookings WHERE booking_status IN ('Confirmed','Completed')")->fetch(PDO::FETCH_ASSOC)['total'];
    $walkin_cancelled_count = (int)0; // table doesn't define cancelled in requirements

    $confirmed_total = $online_confirmed_count + $walkin_confirmed_count;
    $pending_total = $online_pending_count + $walkin_pending_count;
    $cancelled_total = $online_cancelled_count + $walkin_cancelled_count;

    // Monthly reservation trend (for Admin Monthly Reservation Trend bar chart)
    // We consider both online tb_reserve and walk-in walkin_bookings.
    // For online: use tb_reserve.date (date column)
    // For walk-in: use walkin_bookings.booking_date (date column)
    // Output format must match existing dashboard JS: array of { month: 'YYYY-MM', count: n }

    $monthly_trend = [];

    // Determine latest year-months to show: take the last 12 months (including current)
    $start = new DateTime('first day of this month');
    $start->modify('-11 months');
    $startYm = $start->format('Y-m');

    // Create a map with default zeros for each month in the range
    $cursor = clone $start;
    $monthsMap = [];
    for ($i = 0; $i < 12; $i++) {
        $monthsMap[$cursor->format('Y-m')] = 0;
        $cursor->modify('+1 month');
    }

    // Online (tb_reserve)
    $onlineStmt = $conn->prepare(
        "SELECT DATE_FORMAT(date, '%Y-%m') AS ym, COUNT(*) AS cnt
         FROM tb_reserve
         WHERE date >= ?
         GROUP BY ym"
    );
    $onlineStmt->execute([$startYm . '-01']);
    while ($row = $onlineStmt->fetch(PDO::FETCH_ASSOC)) {
        $ym = $row['ym'];
        if (isset($monthsMap[$ym])) {
            $monthsMap[$ym] += (int)$row['cnt'];
        }
    }

    // Walk-in (walkin_bookings)
    $walkinStmt = $conn->prepare(
        "SELECT DATE_FORMAT(booking_date, '%Y-%m') AS ym, COUNT(*) AS cnt
         FROM walkin_bookings
         WHERE booking_date >= ?
         GROUP BY ym"
    );
    $walkinStmt->execute([$startYm . '-01']);
    while ($row = $walkinStmt->fetch(PDO::FETCH_ASSOC)) {
        $ym = $row['ym'];
        if (isset($monthsMap[$ym])) {
            $monthsMap[$ym] += (int)$row['cnt'];
        }
    }

    // Convert map into required array
    foreach ($monthsMap as $ym => $count) {
        $monthly_trend[] = ['month' => $ym, 'count' => (int)$count];
    }


    echo json_encode([
        'status' => 'success',
        'data'   => [
            'total_users'                 => $totalUsers,
            'reservations_this_month'   => $reservationsThisMonth,
            'active_guides'               => $activeGuides,
            'stc_count'                   => $stcCount,

            // Walk-in card = total walk-ins
            'walkin_count'               => $walkin_count,

            // Booking Type Distribution Chart
            'online_count'              => $online_count,
            'walkin_count_total'        => $walkin_count,

            // Booking Status Overview Chart (Confirmed / Pending / Cancelled)
            'confirmed_count'           => $confirmed_total,
            'pending_count'             => $pending_total,
            'cancelled_count'           => $cancelled_total,

            // Optional paid/unpaid breakdown for future charts
            'paid_total'                 => 0,
            'unpaid_total'               => 0,

            'monthly_trend'             => $monthly_trend,
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
}
?>