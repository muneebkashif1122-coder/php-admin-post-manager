<?php

header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(E_ALL);

// Load the class file exactly as it is
require_once __DIR__ . '/../Category.php';

if (isset($_GET['action_type']) && $_GET['action_type'] == 'fetch_catgories') {
    // 1. Create the class object
    $categoryObj = new Category();

    $dbConnection = $categoryObj->conn;
    
    $sql = "SELECT * FROM categories";
    $result = mysqli_query($dbConnection, $sql);
    
    $categories = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
    }
    
    // 4. Return the data to your AJAX script
    echo json_encode(['status' => 'success', 'data' => $categories]);
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid routing request parameters detected.']);
    exit;
}
?>
