<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access";
    exit;
}

$userId = $_SESSION['user_id'];

$sql = "DELETE FROM watchHistory WHERE account_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo "Failed to prepare statement";
    exit;
}

$stmt->bind_param("s", $userId);

if ($stmt->execute()) {
    echo "Your history has been deleted.";
} else {
    echo "Failed to delete watch history.";
}

$stmt->close();
$conn->close();
?>