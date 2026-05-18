<?php
session_start();
include 'conn.php';

$email = $_POST['xusername'];
$password = $_POST['xpassword'];

try {
    // Prepare statement to get admin by email
    $stmt = $conn->prepare("SELECT * FROM tb_admin WHERE email=?");
    $stmt->bindParam(1, $email, PDO::PARAM_STR);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        // Check if password is hashed (starts with $2y$ for bcrypt)
        if (password_get_info($admin['password'])['algoName'] === 'bcrypt') {
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_email'] = $email;
                echo json_encode(["status" => "success", "message" => "Admin found"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
            }
        } else {
            // Password is stored in plain text, compare directly
            if ($password === $admin['password']) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_email'] = $email;
                echo json_encode(["status" => "success", "message" => "Admin found"]);
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
