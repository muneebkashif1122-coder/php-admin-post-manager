<?php
// FIXED: Force standard JSON payloads at the very top of execution loop
header('Content-Type: application/json');

// FIXED: Suppress raw text errors from popping out into the template wrapper
ini_set('display_errors', 0);
error_reporting(E_ALL);

// FIXED: Looking one step backward from ajax/ folder to extract Author class rules
require_once '../Author.php';

if (isset($_GET['action_type']) && $_GET['action_type'] == 'fetch_authors') {
    $authorObj = new Author();
    $authors = $authorObj->get_authors(); 
    
    echo json_encode(['status' => 'success', 'data' => $authors]);
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid routing request parameters detected.']);
    exit;
}
?>