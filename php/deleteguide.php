<?php
include 'conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid method']);
    exit;
}

$guide_id = $_POST['guide_id'] ?? 0;

try {
    // Get old pic to delete
    $sql = "SELECT profile_picture FROM tb_tourguides WHERE guide_id = ?";
    $stmt = $conn->prepare($sql);
$stmt->execute(array($guide_id));
    $guide = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($guide && $guide['profile_picture'] && file_exists('../assets/guides/' . $guide['profile_picture'])) {
        unlink('../assets/guides/' . $guide['profile_picture']);
    }

    // Delete record
    $delete_sql = "DELETE FROM tb_tourguides WHERE guide_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->execute(array($guide_id));

    echo json_encode(['status' => 'success', 'message' => 'Guide deleted successfully']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
