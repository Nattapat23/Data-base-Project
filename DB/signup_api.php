<?php
include 'config.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$firstname = trim($_POST['firstname'] ?? '');
$lastname = trim($_POST['lastname'] ?? '');

if (empty($email) || empty($password) || empty($firstname) || empty($lastname)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit;
}

// Check if email already exists
$check = $conn->prepare("SELECT account_id FROM accounts WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'This email is already in use']);
    exit;
}
$check->close();

// สร้าง UUID สำหรับ account_id
$uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    mt_rand(0, 0xffff), mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0x0fff) | 0x4000,
    mt_rand(0, 0x3fff) | 0x8000,
    mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
);

// เข้ารหัสรหัสผ่าน
$hashed = password_hash($password, PASSWORD_DEFAULT);

// เพิ่มข้อมูลผู้ใช้
$stmt = $conn->prepare("INSERT INTO accounts (account_id, email, password_hash, userName, lastName) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $uuid, $email, $hashed, $firstname, $lastname);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'สมัครสำเร็จ!']);
} else {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการสมัคร: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>