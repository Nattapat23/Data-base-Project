<?php
session_start();
include 'config.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$userId = $_SESSION['user_id'];
$contentId = $_POST['content_id'] ?? '';

if (empty($contentId)) {
    echo json_encode(['success' => false, 'message' => 'Content ID is required']);
    exit;
}

// Insert or update watch history
// history_id จะ auto increment เอง, ถ้า duplicate (account_id + content_id) จะ update timeWach
$sql = "INSERT INTO watchHistory (account_id, content_id, timeWach) 
        VALUES (?, ?, NOW()) 
        ON DUPLICATE KEY UPDATE timeWach = NOW()";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ss", $userId, $contentId);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Watch history saved',
        'history_id' => $stmt->insert_id // ส่ง history_id กลับไป
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>