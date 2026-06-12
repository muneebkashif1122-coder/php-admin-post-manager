<?php
// 1. Force valid JSON headers right at the very top of the script execution loop
header('Content-Type: application/json');

// 2. Hide raw engine error strings from corrupting the outgoing AJAX message payload
ini_set('display_errors', 0);
error_reporting(E_ALL);

// FIXED: Adjusted path to step back ONE level out of ajax_handler/ directory to find Author.php
// require_once '../Author.php';
require_once dirname(__DIR__, 1) . '/Author.php';

// FIXED: Cleaned up duplicate nested if statement conditions and unclosed bracket syntax errors
if (isset($_POST['action_type']) && $_POST['action_type'] == 'register') {

    $author = new Author();
    $name   = $_POST['author'] ?? '';
    $bio    = $_POST['bio'] ?? '';
    
    $result = $author->set_details($name, $bio);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Author saved successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save Author profile data to database.']);
    }
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing action type parameter string payload.']);
    exit;
}
?>
