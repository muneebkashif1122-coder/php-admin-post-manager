<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../Category.php';

if (isset($_POST['action_type']) && $_POST['action_type'] === 'delete_category_record') {
    // 🚀 FIXED: Standardized the variable naming string wrapper to categoryObj
    $categoryObj = new Category();
    
    // Capture the incoming primary row key ID safely
    $id = $_POST['id'] ?? '';

    if (!empty($id)) {
        // 🚀 FIXED: Cleaned up instance name variables and removed the broken echo debug lines
        $result = $categoryObj->delete_category($id);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Category deleted successfully.']);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database operation failed or missing database link.']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing ID parameter.']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action type parameter.']);
    exit;
}
?>