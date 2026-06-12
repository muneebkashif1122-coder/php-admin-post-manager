<?php
ob_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

include_once "../../../database/db.php"; 
require_once __DIR__ . '/../Author.php'; 

if (isset($_POST['action_type']) && $_POST['action_type'] === 'update_author_record') {
    $authorObj = new Author();
    
    $id         = $_POST['id'] ?? '';
    $author     = $_POST['author'] ?? '';
    $bio        = $_POST['bio'] ?? '';
    $image_path = ''; 

    // Handle file upload sequence
    if (isset($_FILES['author_image']) && $_FILES['author_image']['error'] === UPLOAD_ERR_OK && $_FILES['author_image']['size'] > 0) {
        $fileTmpPath = $_FILES['author_image']['tmp_name'];
        $fileName    = $_FILES['author_image']['name'];
        
        $newFileName = time() . '_' . preg_replace("/[^A-Za-z0-9.]/", "_", $fileName);
        $target_dir  = "uploads/author_uploads/";
        $dest_path   = "../../" . $target_dir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $image_path = $target_dir . $newFileName; 

            // 🚀 OOP Pattern: Object method encapsulates asset management
            $authorObj->purgeOldAuthorImage($id, $conn, $target_dir);
        }
    }

    // Class update orchestration
    if (!empty($id) && !empty($author)) {
        $result = $authorObj->update_author($id, $author, $bio, $image_path);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Author updated successfully!']);
            exit;
        }
        echo json_encode(['status' => 'error', 'message' => 'Database operation failed inside Author class.']);
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Missing ID or Author Name parameters.']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action type parameter check.']);
exit;
?>


