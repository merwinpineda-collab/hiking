<?php
include 'conn.php'; // Include the database connection

// Get POST data with correct keys matching form input names
$firstname = $_POST['xfirstname'] ?? '';
$middlename = $_POST['xmiddlename'] ?? '';
$lastname = $_POST['xlastname'] ?? '';
$gender = $_POST['xgender'] ?? '';
$age = $_POST['xage'] ?? '';
$number = $_POST['xnumber'] ?? '';
$email = $_POST['xemail'] ?? '';
$password = $_POST['xpassword'] ?? '';

try {
    // Check if email already exists
    $check_sql = "SELECT email FROM tb_userinfo WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bindParam(1, $email, PDO::PARAM_STR);
    $check_stmt->execute();
    if ($check_stmt->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already exists.']);
        $check_stmt->closeCursor();
        exit();
    }
    $check_stmt->closeCursor();

    // Prepare and bind using PDO
    $sql = "INSERT INTO tb_userinfo (firstname, middlename, lastname, gender, age, number, email, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $firstname, PDO::PARAM_STR);
    $stmt->bindParam(2, $middlename, PDO::PARAM_STR);
    $stmt->bindParam(3, $lastname, PDO::PARAM_STR);
    $stmt->bindParam(4, $gender, PDO::PARAM_STR);
    $stmt->bindParam(5, $age, PDO::PARAM_STR);
    $stmt->bindParam(6, $number, PDO::PARAM_STR);
    $stmt->bindParam(7, $email, PDO::PARAM_STR);
    $stmt->bindParam(8, $password, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Registration successful!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Execute failed']);
    }

    $stmt->closeCursor();
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
