<?php
// 1. Force strict JSON encoding definitions right at the top
header('Content-Type: application/json');

// 2. Suppress raw engine errors from corrupting the outgoing AJAX message layout
chdir(dirname(__DIR__)); 

require_once 'Post.php';

if (isset($_POST['action_type']) && $_POST['action_type'] == 'create_post') {

    $postObj = new Post();
    
    // Extracting user inputs cleanly from the incoming POST array payload
    $title       = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $author_id   = $_POST['author_id'] ?? '';

    // Pass your operational database variable down to the class model execution engine
    global $conn; 

    if (!$conn) {
        echo json_encode(['status' => 'error', 'message' => 'Database connection variable is completely missing.']);
        exit;
    }

    $result = $postObj->set_details($conn, $author_id, $title, $description, $category_id);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Post saved successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database Query Error: Failed to execute INSERT.']);
    }
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing action type parameter.']);
    exit;
}
?>

