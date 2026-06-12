<?php
// 1. Move this to the absolute top so JavaScript ALWAYS gets valid JSON
header('Content-Type: application/json');

// 🚀 REQUIRED FIX: Changes the execution path so Category.php can find your database file without crashing
chdir(dirname(__DIR__)); 

require_once 'Category.php';

if (isset($_POST['action_type']) && $_POST['action_type'] == 'add_category') {
    $categoryObj = new Category();
    $category = $_POST['category_name'] ?? '';
    $result = $categoryObj->set_details($category);

    if ($result) {
        // Updated text string to match categories instead of authors
        echo json_encode(['status' => 'success', 'message' => 'Category saved successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'The category name already exists. Please choose a different name.']);
    }
    exit;
} else {
    // Safety fallback prevents JSON parser crash if action_type fails
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing action type parameter.']);
    exit;
}
?>
