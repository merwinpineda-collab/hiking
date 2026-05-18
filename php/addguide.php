<?php
include 'conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid method']);
    exit;
}

$name = $_POST['name'] ?? '';
$age = (int)($_POST['age'] ?? 0);
$address = $_POST['address'] ?? '';
$gender = $_POST['gender'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$experience_years = (int)($_POST['experience_years'] ?? 0);
$specialization = $_POST['specialization'] ?? '';
$contact_details = $_POST['contact_details'] ?? '';

$profile_picture = null;

// Handle file upload
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
    $upload_dir = '../assets/guides/';
    $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
        $profile_picture = $new_filename;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'File upload failed']);
        exit;
    }
}

try {
    // Check email unique
    $check_sql = "SELECT email FROM tb_tourguides WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
$check_stmt->execute([$email]);
    if ($check_stmt->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
        exit;
    }

$sql = "INSERT INTO tb_tourguides (name, age, address, gender, profile_picture, phone, email, experience_years, specialization, contact_details) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
$stmt->execute([$name, $age, $address, $gender, $profile_picture, $phone, $email, $experience_years, $specialization, $contact_details]);

    echo json_encode(['status' => 'success', 'message' => 'Guide added successfully']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
