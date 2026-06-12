<?php
ob_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

include_once "../../../database/db.php"; 
require_once __DIR__ . '/../Post.php'; 

if (isset($_POST['action_type']) && $_POST['action_type'] === 'update_post_record') {
    $postObj = new Post();
    
    $id          = $_POST['id'] ?? '';
    $author_id   = $_POST['author_id'] ?? '';
    $title       = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $image_path  = ''; 

    // Handle physical file uploads
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK && $_FILES['post_image']['size'] > 0) {
        $fileTmpPath = $_FILES['post_image']['tmp_name'];
        $fileName    = $_FILES['post_image']['name'];
        
        $newFileName = time() . '_' . preg_replace("/[^A-Za-z0-9.]/", "_", $fileName);
        $target_dir  = "uploads/post_uploads/";
        $dest_path   = "../../" . $target_dir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $image_path = $target_dir . $newFileName; 

            // 🚀 OOP Pattern: Call the encapsulated method directly from your object instance
            $postObj->purgeOldPostImage($id, $conn, $target_dir);
        }
    }

    // Validation and business execution pipeline 
    if (!empty($id) && !empty($title)) {
        
        $result = $postObj->update_post($id, $author_id, $title, $description, $category_id, $image_path);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Post updated successfully!']);
            exit;
        }
        
        echo json_encode(['status' => 'error', 'message' => 'Database operation failed inside Post class.']);
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Missing ID or Post Title parameters.']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action type parameter check.']);
exit;
?>





