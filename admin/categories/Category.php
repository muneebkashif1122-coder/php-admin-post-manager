<?php 
// 1. FIXED: Changed backslashes to correct relative path forward slashes
include_once __DIR__ . "/../../database/db.php";

class Category { // Class names are usually singular by standard convention
    public $category;
    public $conn;

    // 2. FIXED: Added a constructor to safely pull your database connection into the class object
    function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    function set_details($category){
        // 3. FIXED: Cleaned up variable consistency and added SQL protection
       // 1. Sanitize the input
        $this->category = mysqli_real_escape_string($this->conn, trim($category));

        // 🚀 2. CHECK FOR DUPLICATES: Search for the exact name in your table
        $checkSql = "SELECT id FROM categories WHERE name = '$this->category'";
        $checkResult = mysqli_query($this->conn, $checkSql);

        // If a row is found, it means the category already exists
        if (mysqli_num_rows($checkResult) > 0) {
            return false; // Stop execution and return false
        }

        // 3. Proceed to insert only if it is a unique name
        $sql = "INSERT INTO categories (name) VALUES ('$this->category')";
        return mysqli_query($this->conn, $sql);
    }

    function get_details(){ // Fixed typo from get_detils
        echo "Category: " . $this->category;
    }
    function update_category($id, $name) {
    // 1. Sanitize incoming variables to protect against SQL Injection
    $sanitized_id = mysqli_real_escape_string($this->conn, trim($id));
    $sanitized_name = mysqli_real_escape_string($this->conn, trim($name));

    // Validation check for primary required fields
    if (empty($sanitized_id) || empty($sanitized_name)) {
        return false;
    }

    // 2. Build and execute the SQL update query string
    $sql = "UPDATE categories SET name = '$sanitized_name' WHERE id = '$sanitized_id'";
    return mysqli_query($this->conn, $sql);
}

function delete_category($id) {
        $sanitized_id = mysqli_real_escape_string($this->conn, trim($id));

        if (empty($sanitized_id)) {
            return false;
        }

        $sql = "DELETE FROM categories WHERE id = '$sanitized_id'";
        return mysqli_query($this->conn, $sql);
    }
}
?>