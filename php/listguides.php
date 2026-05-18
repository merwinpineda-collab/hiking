<?php
include 'conn.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->query(
        "SELECT guide_id, name, age, gender, address, phone, email,
                experience_years, specialization, contact_details,
                profile_picture, created_at
         FROM tb_tourguides
         ORDER BY created_at DESC"
    );
    $guides = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    echo json_encode([
        'status' => 'success',
        'guides' => $guides,
        'count'  => count($guides)
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
}
?>