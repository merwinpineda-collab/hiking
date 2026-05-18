<?php
include 'conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid method']);
    exit;
}

$guide_id = $_POST['guide_id'] ?? 0;
$name = $_POST['name'] ?? '';
$age = $_POST['age'] ?? 0;
$address = $_POST['address'] ?? '';
$gender = $_POST['gender'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$experience_years = (int)($_POST['experience_years'] ?? 0);
$specialization = $_POST['specialization'] ?? '';
$contact_details = $_POST['contact_details'] ?? '';

$profile_picture_update = null;

// Handle new profile picture upload (optional)
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
    // Delete old pic if exists
    $old_sql = "SELECT profile_picture FROM tb_tourguides WHERE guide_id = ?";
    $old_stmt = $conn->prepare($old_sql);
$old_stmt->execute(array($guide_id));
    $old_guide = $old_stmt->fetch(PDO::FETCH_ASSOC);
    if ($old_guide['profile_picture'] && file_exists('../assets/guides/' . $old_guide['profile_picture'])) {
        unlink('../assets/guides/' . $old_guide['profile_picture']);
    }

    $upload_dir = '../assets/guides/';
    $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
        $profile_picture_update = $new_filename;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'File upload failed']);
        exit;
    }
}

try {
    // Check email unique (exclude self)
    $check_sql = "SELECT email FROM tb_tourguides WHERE email = ? AND guide_id != ?";
    $check_stmt = $conn->prepare($check_sql);
$check_stmt->execute(array($email, $guide_id));
    if ($check_stmt->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already exists for another guide']);
        exit;
    }

    $sql = "UPDATE tb_tourguides SET name = ?, age = ?, address = ?, gender = ?, phone = ?, email = ?, experience_years = ?, specialization = ?, contact_details = ?";
    $params = [$name, $age, $address, $gender, $phone, $email, $experience_years, $specialization, $contact_details];

    if ($profile_picture_update) {
        $sql .= ", profile_picture = ?";
        $params[] = $profile_picture_update;
    }

    $sql .= " WHERE guide_id = ?";
    $params[] = $guide_id;

    $stmt = $conn->prepare($sql);
$stmt->execute(array_values($params));

    echo json_encode(['status' => 'success', 'message' => 'Guide updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
