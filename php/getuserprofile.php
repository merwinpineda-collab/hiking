<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'conn.php';

try {
    $email = $_POST['email'] ?? $_GET['email'] ?? '';
    if (empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Email is required']);
        exit();
    }

    $stmt = $conn->prepare(
        "SELECT firstname, middlename, lastname, email
         FROM tb_userinfo
         WHERE email = ?"
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit();
    }

    $firstname = trim((string)($user['firstname'] ?? ''));
    $middlename = trim((string)($user['middlename'] ?? ''));
    $lastname = trim((string)($user['lastname'] ?? ''));

    $fullname = trim($firstname . ' ' . ($middlename !== '' ? $middlename . ' ' : '') . $lastname);
    if ($fullname === '') $fullname = (string)($user['email'] ?? '');

    echo json_encode([
        'status' => 'success',
        'data' => [
            'fullname' => $fullname,
            'email' => (string)$user['email']
        ]
    ]);

    $stmt->closeCursor();
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>

