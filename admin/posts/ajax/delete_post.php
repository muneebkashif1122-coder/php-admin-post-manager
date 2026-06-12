<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

include_once "../../../database/db.php"; 
require_once __DIR__ . '/../Post.php';

if (isset($_POST['action_type']) && $_POST['action_type'] === 'delete_post_record') {
    $postObj = new Post();
    
    $id = $_POST['id'] ?? '';

    if (!empty($id)) {
        
        $target_dir = "uploads/post_uploads/";
        
        // 🚀 OOP Pattern: Call the encapsulated method directly from your object instance
        $postObj->purgeOldPostImage($id, $conn, $target_dir);

        // Execute the native post deletion routine
        $result = $postObj->delete_post($id);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Post deleted successfully.']);
            exit;
        }
        echo json_encode(['status' => 'error', 'message' => 'Database operation failed or missing database link.']);
        exit;
    }
    
    echo json_encode(['status' => 'error', 'message' => 'Missing ID parameter.']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action type parameter.']);
exit;
?>
