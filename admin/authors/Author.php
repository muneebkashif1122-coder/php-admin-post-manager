<?php

include dirname(__DIR__, 2) . '/database/db.php';

class Author {
    public $author;
    public $bio;
    public $image_path;
    public $conn;

    function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    function set_details($author, $bio) {
        $this->author     = $author;
        $this->bio        = $bio;
        $this->image_path = ""; // Start as empty string fallback [1]
        $target_dir       = "uploads/author_uploads/";

        if(isset($_FILES['post_image']) && $_FILES['post_image']['error'] == 0) {
            $filename = time() . "_" . $_FILES['post_image']['name'];
            $tmp_name = $_FILES['post_image']['tmp_name'];
            
            $full_destination = "../../" . $target_dir . $filename;

            if(move_uploaded_file($tmp_name, $full_destination)) {
                $this->image_path = $target_dir . $filename; 
            }
            else {
                $this->image_path = ""; 
            }
        }

        $sql = "INSERT INTO authors (author, bio, image) VALUES ('$this->author', '$this->bio', '$this->image_path')";
        return mysqli_query($this->conn, $sql);
    }

    function get_details() {
        echo "Author: " . $this->author . " | Bio: " . $this->bio . " | Image: " . $this->image_path . ".<br>";
    }
    
    // Senior-Approved Object Getter Method
    function get_authors() {
        $authors_list = []; // Initialize an empty data array

        if (!$this->conn) {
            return $authors_list;
        }

        $sql = "SELECT * FROM authors ORDER BY id DESC";
        $result = mysqli_query($this->conn, $sql);

        // Process raw MySQL resource rows directly into a clean PHP array structure
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $authors_list[] = $row;
            }
        }

        return $authors_list; // Returns a clean array, hiding raw SQL from your frontend
    }

    function update_author($id, $author, $bio, $image_path) {
        // 1. Sanitize all inputs to match your working post pattern exactly
        $sanitized_id = mysqli_real_escape_string($this->conn, trim($id));
        $sanitized_author = mysqli_real_escape_string($this->conn, trim($author));
        $sanitized_bio = mysqli_real_escape_string($this->conn, trim($bio));
        $sanitized_image = mysqli_real_escape_string($this->conn, trim($image_path));

        if (empty($sanitized_id) || empty($sanitized_author)) {
            return false;
        }

        // 🚀 MATCHES WORKING POST LOGIC: If a new image path exists, run full update
        if (!empty($sanitized_image)) {
            
            // Safe file system cleanup block
            $check_sql = "SELECT image FROM authors WHERE id = '$sanitized_id'";
            $check_result = mysqli_query($this->conn, $check_sql);
            
            if ($check_result && mysqli_num_rows($check_result) > 0) {
                $row = mysqli_fetch_assoc($check_result);
                $old_image_path = $row['image'];
                
                // 🚀 FIXED: Cleans string to ensure step-outs don't go outside the application root space
                $clean_old_path = str_replace('../../', '', $old_image_path);
                $full_file_path = __DIR__ . '/../../' . $clean_old_path; 
                
                if (!empty($clean_old_path) && file_exists($full_file_path)) {
                    @unlink($full_file_path);
                }
            }
            
            $sql = "UPDATE authors SET 
                        author = '$sanitized_author', 
                        bio = '$sanitized_bio', 
                        image = '$sanitized_image' 
                    WHERE id = '$sanitized_id'";
        } else {
            // 🚀 MATCHES WORKING POST LOGIC: If no image uploaded, preserve current profile path string
            $sql = "UPDATE authors SET 
                        author = '$sanitized_author', 
                        bio = '$sanitized_bio' 
                    WHERE id = '$sanitized_id'";
        }

        return mysqli_query($this->conn, $sql);
    }

    /**
     * Purges old images tied to an author record.
     * Fully encapsulated OOP method safe from server directory traps.
     */
    function purgeOldAuthorImage($id, $conn, $target_dir = "uploads/author_uploads/") {
        try {
            if (empty($id) || !isset($conn)) return false;
            $row = null;

            // Structured query targeting your exact table
            if ($conn instanceof mysqli) {
                $stmt = @$conn->prepare("SELECT * FROM `authors` WHERE `id` = ? LIMIT 1");
                if ($stmt) {
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $row = $res->fetch_assoc();
                    if ($res) $res->free();
                    $stmt->close();
                }
            } elseif ($conn instanceof PDO) {
                $stmt = @$conn->prepare("SELECT * FROM `authors` WHERE `id` = ? LIMIT 1");
                if ($stmt) {
                    $stmt->execute([$id]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                }
            }

            if ($row) {
                foreach ($row as $columnValue) {
                    if (is_string($columnValue) && !empty($columnValue)) {
                        $old_file_string = trim($columnValue);
                        $physical_file   = "";

                        // Pure OOP Path Resolution based on class file location
                        // Since Author.php is in admin/authors/, going up 1 level (__DIR__ . '/../') lands in admin/
                        $base_path = dirname(__DIR__) . '/';

                        if (strpos($old_file_string, 'uploads/') !== false) {
                            $physical_file = $base_path . $old_file_string;
                        } elseif (preg_match('/\.(webp|jpg|jpeg|png|gif)$/i', $old_file_string)) {
                            $physical_file = $base_path . $target_dir . $old_file_string;
                        }

                        if (!empty($physical_file) && file_exists($physical_file) && is_file($physical_file)) {
                            @unlink($physical_file);
                        }
                    }
                }
            }
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
    
    function delete_author($id) {
        // 1. Sanitize the incoming ID integer input to block SQL injection
        $sanitized_id = mysqli_real_escape_string($this->conn, trim($id));

        if (empty($sanitized_id)) {
            return false;
        }

        // 2. Execute the deletion query cleanly against your class connection link
        $sql = "DELETE FROM authors WHERE id = $sanitized_id";

        return mysqli_query($this->conn, $sql);
    }
}
?>