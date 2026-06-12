<?php
header('Content-Type: application/json');
// ini_set('display_errors', 0);
// error_reporting(E_ALL);

// require_once __DIR__ . '/../Author.php';
require_once dirname(__DIR__, 1) . '/Author.php';

if (isset($_POST['action_type']) && $_POST['action_type'] === 'delete_author_record') {
    $authorObj = new Author();
    
    $id = $_POST['id'] ?? '';

    if (!empty($id)) {
        
        $target_dir = "uploads/author_uploads/";
        
        // 🚀 OOP Pattern: Call the encapsulated method directly from your object
        $authorObj->purgeOldAuthorImage($id, $conn, $target_dir);

        // Execute the database row deletion routine
        $result = $authorObj->delete_author($id);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Author deleted successfully.']);
            exit;
        }
        echo json_encode(['status' => 'error', 'message' => 'Database operation failed or missing database link.']);
        exit;
    }
    
    echo json_encode(['status' => 'error', 'message' => 'Missing ID parameter.']);
    exit;
}