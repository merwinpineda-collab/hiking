<?php
session_start();
include 'conn.php';

$email = $_POST['xemail'];
$password = $_POST['xpassword'];

try {
    // Prepare statement to get user by username
    $stmt = $conn->prepare("SELECT * FROM tb_userinfo WHERE email=?");
    $stmt->bindParam(1, $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Check if password is hashed (starts with $2y$ for bcrypt)
        if (password_get_info($user['password'])['algoName'] === 'bcrypt') {
            if (password_verify($password, $user['password'])) {
                echo json_encode(["status" => "success", "message" => "User found"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
            }
        } else {
            // Password is stored in plain text, compare directly
            if ($password === $user['password']) {
                echo json_encode(["status" => "success", "message" => "User found"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
            }
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
    }

    $stmt->closeCursor();
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
