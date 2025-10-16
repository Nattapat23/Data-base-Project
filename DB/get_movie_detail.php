<?php
include 'config.php';
header('Content-Type: application/json; charset=utf-8');

$contentId = $_GET['id'] ?? '';

if (empty($contentId)) {
    echo json_encode(['success' => false, 'message' => 'Content ID is required']);
    exit;
}

try {
    // ดึงข้อมูลหนังหลัก
    $sqlMovie = "SELECT c.ContentID, c.Title, c.OriginalTitle, c.ReleaseYear, 
                        c.RuntimeMinutes, c.Genres, c.ContentType,
                        r.AverageRating, r.NumVotes
                 FROM Content c
                 LEFT JOIN Rating r ON c.ContentID = r.ContentID
                 WHERE c.ContentID = ?";

    $stmt = $conn->prepare($sqlMovie);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $contentId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'Movie not found in database. ContentID: ' . $contentId
        ]);
        $stmt->close();
        $conn->close();
        exit;
    }

    $movie = $result->fetch_assoc();
    $stmt->close();

    // ดึงข้อมูลนักแสดง
    $sqlCast = "SELECT cp.PersonID, cp.RoleCategory, cp.Characters, cp.Ordering,
                       p.Name, p.BirthYear
                FROM ContentPerson cp
                JOIN Person p ON cp.PersonID COLLATE utf8mb4_general_ci = p.PersonID COLLATE utf8mb4_general_ci
                WHERE cp.ContentID = ?
                AND cp.RoleCategory IN ('actor', 'actress')
                ORDER BY cp.Ordering ASC
                LIMIT 20";

    $stmtCast = $conn->prepare($sqlCast);
    $actors = [];
    
    if ($stmtCast) {
        $stmtCast->bind_param("s", $contentId);
        $stmtCast->execute();
        $resultCast = $stmtCast->get_result();

        while ($row = $resultCast->fetch_assoc()) {
            $characters = '';
            if (!empty($row['Characters'])) {
                $charArray = json_decode($row['Characters'], true);
                if (is_array($charArray) && count($charArray) > 0) {
                    $characters = $charArray[0];
                } else {
                    $characters = trim(str_replace(['[', ']', '"', '\\'], '', $row['Characters']));
                }
            }
            
            $actors[] = [
                'id' => $row['PersonID'],
                'name' => $row['Name'],
                'character' => $characters ?: 'Unknown Role',
                'birthYear' => $row['BirthYear']
            ];
        }
        $stmtCast->close();
    }

    // ดึงข้อมูล Directors และ Writers (แก้ไขตรงนี้!)
    $directorsText = 'N/A';
    $writersText = 'N/A';
    
    // ฟังก์ชันแปลง PersonID เป็นชื่อ
    function getPersonNames($conn, $personIds) {
        if (empty($personIds)) return 'N/A';
        
        // แยก IDs (อาจเป็น "nm123,nm456" หรือ "nm123")
        $ids = array_map('trim', explode(',', $personIds));
        if (empty($ids)) return 'N/A';
        
        // สร้าง placeholders สำหรับ IN clause
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        
        $sql = "SELECT Name FROM Person WHERE PersonID IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) return $personIds; // ถ้า prepare ไม่สำเร็จ ให้คืน ID กลับไป
        
        // Bind parameters dynamically
        $types = str_repeat('s', count($ids));
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $names = [];
        while ($row = $result->fetch_assoc()) {
            $names[] = $row['Name'];
        }
        $stmt->close();
        
        return empty($names) ? 'N/A' : implode(', ', $names);
    }
    
    // ดึง Directors และ Writers IDs จาก ContentCrew
    $sqlCrew = "SELECT Directors, Writers FROM ContentCrew WHERE ContentID = ?";
    $stmtCrew = $conn->prepare($sqlCrew);
    if ($stmtCrew) {
        $stmtCrew->bind_param("s", $contentId);
        $stmtCrew->execute();
        $resultCrew = $stmtCrew->get_result();
        
        if ($row = $resultCrew->fetch_assoc()) {
            $directorsText = getPersonNames($conn, $row['Directors']);
            $writersText = getPersonNames($conn, $row['Writers']);
        }
        $stmtCrew->close();
    }

    // สร้าง response
    echo json_encode([
        'success' => true,
        'movie' => [
            'id' => $movie['ContentID'],
            'title' => $movie['Title'],
            'originalTitle' => $movie['OriginalTitle'] ?? $movie['Title'],
            'year' => $movie['ReleaseYear'],
            'runtime' => $movie['RuntimeMinutes'] ?? 0,
            'genres' => $movie['Genres'] ?? 'Unknown',
            'type' => $movie['ContentType'],
            'rating' => $movie['AverageRating'] ?? 'N/A',
            'votes' => $movie['NumVotes'] ?? 0,
            'poster' => 'img/default.jpg',
            'directors' => $directorsText,
            'writers' => $writersText,
            'actors' => $actors
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'contentId' => $contentId
    ]);
}

$conn->close();
?>