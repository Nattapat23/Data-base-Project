<?php
include 'config.php';
header('Content-Type: application/json; charset=utf-8');

$contentId = $_GET['id'] ?? 'tt0000009'; // Miss Jerry เป็น default

// เช็คว่าหนังมีในฐานข้อมูลหรือไม่
$sqlCheck = "SELECT c.ContentID, c.Title, c.ContentType, c.ReleaseYear,
                    COUNT(DISTINCT cp.PersonID) as actor_count
             FROM Content c
             LEFT JOIN ContentPerson cp ON c.ContentID = cp.ContentID 
                 AND cp.RoleCategory IN ('actor', 'actress')
             WHERE c.ContentID = ?
             GROUP BY c.ContentID";

$stmt = $conn->prepare($sqlCheck);
$stmt->bind_param("s", $contentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'exists' => false,
        'message' => 'Movie not found in database',
        'contentId' => $contentId,
        'solution' => 'This ContentID does not exist in your Content table'
    ]);
} else {
    $movie = $result->fetch_assoc();
    
    // ดึงตัวอย่างนักแสดง 3 คน - แก้ไข Collation
    $sqlActors = "SELECT p.Name, cp.Characters 
                  FROM ContentPerson cp
                  JOIN Person p ON cp.PersonID COLLATE utf8mb4_general_ci = p.PersonID COLLATE utf8mb4_general_ci
                  WHERE cp.ContentID = ?
                  AND cp.RoleCategory IN ('actor', 'actress')
                  ORDER BY cp.Ordering ASC
                  LIMIT 3";
    
    $stmtActors = $conn->prepare($sqlActors);
    $stmtActors->bind_param("s", $contentId);
    $stmtActors->execute();
    $resultActors = $stmtActors->get_result();
    
    $sampleActors = [];
    while ($row = $resultActors->fetch_assoc()) {
        $sampleActors[] = [
            'name' => $row['Name'],
            'character' => $row['Characters']
        ];
    }
    $stmtActors->close();
    
    echo json_encode([
        'exists' => true,
        'movie' => [
            'id' => $movie['ContentID'],
            'title' => $movie['Title'],
            'type' => $movie['ContentType'],
            'year' => $movie['ReleaseYear'],
            'actorCount' => $movie['actor_count']
        ],
        'sampleActors' => $sampleActors,
        'hasActors' => $movie['actor_count'] > 0,
        'status' => $movie['actor_count'] > 0 ? 'OK - Has actors' : 'WARNING - No actors in database'
    ]);
}

$stmt->close();
$conn->close();
?>