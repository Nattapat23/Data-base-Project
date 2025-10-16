<?php
session_start();
include 'config.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userid = $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้ + subscription plan
$sqlUser = "SELECT a.account_id, a.email, a.userName, a.lastName, 
            sp.planName, sp.price, s.endDate
            FROM accounts a 
            LEFT JOIN subscription s ON a.account_id = s.account_id AND s.endDate >= CURDATE()
            LEFT JOIN subscriptionPlan sp ON s.subplan_type = sp.subplan_type 
            WHERE a.account_id = ?";
$stmt = $conn->prepare($sqlUser);
$stmt->bind_param("s", $userid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'User not found']);
    exit;
}

$user = $result->fetch_assoc();

// ดึงประวัติการดู (จาก watchHistory)
$watchHistory = [];
$sqlWatch = "SELECT c.Title, wh.timeWach 
             FROM watchHistory wh 
             JOIN Content c ON wh.content_id = c.ContentID 
             WHERE wh.account_id = ? 
             ORDER BY wh.timeWach DESC 
             LIMIT 10";
$stmtWatch = $conn->prepare($sqlWatch);
if ($stmtWatch) {
    $stmtWatch->bind_param("s", $userid);
    $stmtWatch->execute();
    $resultWatch = $stmtWatch->get_result();
    while ($row = $resultWatch->fetch_assoc()) {
        $watchHistory[] = [
            "title" => $row['Title'],
            "date" => date('Y-m-d H:i', strtotime($row['timeWach']))
        ];
    }
    $stmtWatch->close();
}

// Check subscription status
$planName = $user['planName'] ?? 'No Plan';
$planPrice = $user['price'] ?? 0;
$planStatus = 'Active';
if (!$user['planName']) {
    $planStatus = 'Not Subscribed';
} else if ($user['endDate']) {
    $endDate = new DateTime($user['endDate']);
    $now = new DateTime();
    if ($endDate < $now) {
        $planStatus = 'Expired';
    } else {
        $daysLeft = $now->diff($endDate)->days;
        $planStatus = 'Active (' . $daysLeft . ' days left)';
    }
}

echo json_encode([
    "success" => true,
    "id" => substr($user['account_id'], 0, 8),
    "firstname" => $user['userName'],
    "lastname" => $user['lastName'],
    "email" => $user['email'],
    "avatar" => 'img/profile.jpg',
    "plan" => $planName,
    "planPrice" => $planPrice,
    "planStatus" => $planStatus,
    "planEndDate" => $user['endDate'] ?? null,
    "watchHistory" => $watchHistory
]);

$stmt->close();
$conn->close();
?>