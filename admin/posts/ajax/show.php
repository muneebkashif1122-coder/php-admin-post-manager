<?php

header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(E_ALL);

// Ensure your Post.php file has a clean semicolon on line 9 as well!
require_once __DIR__ . '/../Post.php';

if (isset($_GET['action_type']) && $_GET['action_type'] == 'fetch_posts') {
    
    // 🚀 FIXED: Renamed variables from categoryObj to postObj for code clarity
    $postObj = new Post();

    $dbConnection = $postObj->conn;
    
       $sql = "SELECT posts.*, 
               authors.author AS author_name, 
               categories.name AS category_name 
        FROM posts 
        LEFT JOIN authors ON posts.author_id = authors.id 
        LEFT JOIN categories ON posts.category_id = categories.id 
        ORDER BY posts.id DESC";
    $result = mysqli_query($dbConnection, $sql);
    
    $posts = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $posts[] = $row;
        }
    }
    
    echo json_encode(['status' => 'success', 'data' => $posts]);
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid routing request parameters detected.']);
    exit;
}
?>