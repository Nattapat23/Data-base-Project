<?php
session_start();
include 'config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Please fill in all fields"]);
    exit;
}

// Fetch user data
$stmt = $conn->prepare("SELECT account_id, email, password_hash, userName, lastName FROM accounts WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Account not found"]);
    exit;
}

$user = $result->fetch_assoc();
$stored_password = $user['password_hash'];

// Verify password - รองรับทั้ง hashed และ plain text
$password_valid = false;

// ตรวจสอบว่าเป็น bcrypt hash หรือไม่
if (strlen($stored_password) === 60 && substr($stored_password, 0, 4) === '$2y$') {
    // เป็น hashed password -> ใช้ password_verify
    $password_valid = password_verify($password, $stored_password);
} else {
    // เป็น plain text -> เปรียบเทียบตรงๆ (ไม่ปลอดภัย!)
    $password_valid = ($password === $stored_password);
    
    // ถ้าตรงกัน ให้ hash และอัพเดทในฐานข้อมูล
    if ($password_valid) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE accounts SET password_hash = ? WHERE account_id = ?");
        $update->bind_param("ss", $hashed, $user['account_id']);
        $update->execute();
        $update->close();
    }
}

if (!$password_valid) {
    echo json_encode(["success" => false, "message" => "Incorrect password"]);
    exit;
}

// Create session
$_SESSION['user_id'] = $user['account_id'];
$_SESSION['user_email'] = $user['email'];

echo json_encode([
    "success" => true,
    "message" => "Login successful",
    "user" => [
        "id" => $user['account_id'],
        "firstname" => $user['userName'],
        "lastname" => $user['lastName'],
        "email" => $user['email']
    ]
]);

$stmt->close();
$conn->close();
?>