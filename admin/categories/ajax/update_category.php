<?php
ob_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Move up folders to find the database connection and the Category class file
include_once "../../../database/db.php"; 
require_once __DIR__ . '/../Category.php'; 

if (isset($_POST['action_type']) && $_POST['action_type'] === 'update_category_record') {
    $categoryObj = new Category();
    
    // Capture payload parameters safely
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';

    if (!empty($id) && !empty($name)) {
        // Trigger the database update function
        $result = $categoryObj->update_category($id, $name);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Category updated successfully!']);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database update operation failed inside Category class.']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing ID or Category Name fields parameters.']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action type parameter handler check.']);
    exit;
}
?>