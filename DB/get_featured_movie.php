<?php
include 'config.php';
header('Content-Type: application/json; charset=utf-8');

$featuredMovieId = 'tt0304142'; 


// ดึงข้อมูลหนังจาก Database
$sql = "SELECT c.ContentID, c.Title, c.OriginalTitle, c.ReleaseYear, 
               c.RuntimeMinutes, c.Genres, r.AverageRating, r.NumVotes
        FROM Content c 
        LEFT JOIN Rating r ON c.ContentID = r.ContentID
        WHERE c.ContentID = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $conn->error
    ]);
    exit;
}

$stmt->bind_param("s", $featuredMovieId);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $movie = $result->fetch_assoc();
    
    // สร้าง description
    $genres = explode(',', $movie['Genres']);
    $genreText = isset($genres[0]) ? strtolower(trim($genres[0])) : 'film';
    
    $description = "A compelling " . $genreText;
    if (isset($genres[1])) {
        $description .= " with elements of " . strtolower(trim($genres[1]));
    }
    $description .= ". Released in " . $movie['ReleaseYear'];
    
    if ($movie['AverageRating']) {
        $description .= ". Rated " . number_format($movie['AverageRating'], 1) . "/10";
    }
    
    echo json_encode([
        'success' => true,
        'movie' => [
            'id' => $movie['ContentID'],
            'title' => $movie['Title'],
            'description' => $description,
            'year' => $movie['ReleaseYear'],
            'genres' => $movie['Genres'],
            'rating' => $movie['AverageRating']
        ]
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Movie not found: ' . $featuredMovieId
    ]);
}

$stmt->close();
$conn->close();
?>