<?php 
include_once __DIR__ . "/../../database/db.php";

class Post {
    public $author_id;
    public $title;
    public $description;
    public $category_id;
    public $image_path;
     public $conn; // 🚀 ADDED: Public property to hold your database link

    // 🚀 ADDED: Constructor to automatically catch and bind your global connection link
    function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    function set_details($conn, $author_id, $title, $description, $category_id) {
        // FIXED: Sanitized all data elements to block database breaks caused by quotes or symbols
        $this->author_id   = mysqli_real_escape_string($conn, $author_id);
        $this->title       = mysqli_real_escape_string($conn, $title);
        $this->description = mysqli_real_escape_string($conn, $description);
        $this->category_id = mysqli_real_escape_string($conn, $category_id);

        $target_dir = "uploads/post_uploads/"; // Points into admin/uploads/posts/
        $this->image_path = ""; 
        
        if(isset($_FILES['post_image']) && $_FILES['post_image']['error'] == 0) {
            $filename = time() . "_" . basename($_FILES['post_image']['name']);
            $tmp_name = $_FILES['post_image']['tmp_name'];
            
            $full_destination = "../" . $target_dir . $filename;
            
            if(move_uploaded_file($tmp_name, $full_destination)){
                $this->image_path = $target_dir . $filename;
            }
        }

        // FIXED: Re-added the complete slug generator variable assignment string without the cutoff
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->title))) . '-' . time();
        
        // FIXED: Stripped out raw string single quotes surrounding foreign numerical integer data columns
        $sql = "INSERT INTO posts (author_id, title, description, category_id, image, slug) 
                VALUES ($this->author_id, '$this->title', '$this->description', $this->category_id, '$this->image_path', '$slug')";
        
        return mysqli_query($conn, $sql);
    }
function update_post($id, $author_id, $title, $description, $category_id, $image_path) {
    // 1. Sanitize all inputs to match your coding pattern
    $sanitized_id = mysqli_real_escape_string($this->conn, trim($id));
    $sanitized_author_id = mysqli_real_escape_string($this->conn, trim($author_id));
    $sanitized_title = mysqli_real_escape_string($this->conn, trim($title));
    $sanitized_description = mysqli_real_escape_string($this->conn, trim($description));
    $sanitized_category_id = mysqli_real_escape_string($this->conn, trim($category_id));
    $sanitized_image = mysqli_real_escape_string($this->conn, trim($image_path));

    // Generate a web-safe URL slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $sanitized_title)));

    if (empty($sanitized_id) || empty($sanitized_title)) {
        return false;
    }

    // 🚀 THE FIX: If a new image path exists, update everything including the image
    if (!empty($sanitized_image)) {
        // Optional cleanup: fetch old image from DB and use @unlink() here if desired
        
        $sql = "UPDATE posts SET 
                    author_id = $sanitized_author_id, 
                    title = '$sanitized_title', 
                    description = '$sanitized_description', 
                    category_id = $sanitized_category_id, 
                    image = '$sanitized_image', 
                    slug = '$slug' 
                WHERE id = '$sanitized_id'";
    } else {
        // 🚀 THE FIX: If no new image was uploaded, update all fields EXCEPT the image column
        $sql = "UPDATE posts SET 
                    author_id = $sanitized_author_id, 
                    title = '$sanitized_title', 
                    description = '$sanitized_description', 
                    category_id = $sanitized_category_id, 
                    slug = '$slug' 
                WHERE id = '$sanitized_id'";
    }

    return mysqli_query($this->conn, $sql);
}
/**
 * Purges old images tied to a post record.
 * Encapsulated OOP method safe from server directory traps.
 */
public function purgeOldPostImage($id, $conn, $target_dir = "uploads/post_uploads/") {
    try {
        if (empty($id) || !isset($conn)) return false;
        $row = null;

        foreach (['posts', 'post'] as $tableName) {
            if ($conn instanceof mysqli) {
                $stmt = @$conn->prepare("SELECT * FROM `$tableName` WHERE `id` = ? LIMIT 1");
                if ($stmt) {
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $row = $res->fetch_assoc();
                    if ($res) $res->free();
                    $stmt->close();
                }
            } elseif ($conn instanceof PDO) {
                $stmt = @$conn->prepare("SELECT * FROM `$tableName` WHERE `id` = ? LIMIT 1");
                if ($stmt) {
                    $stmt->execute([$id]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                }
            }
            if ($row) break; 
        }

        if ($row) {
            foreach ($row as $columnValue) {
                if (is_string($columnValue) && !empty($columnValue)) {
                    $old_file_string = trim($columnValue);
                    $physical_file   = "";

                    // Resolves path based on your class file location structure safely
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
    function delete_post($id) {
        // 1. Sanitize the incoming ID integer input to block SQL injection
        $sanitized_id = mysqli_real_escape_string($this->conn, trim($id));

        if (empty($sanitized_id)) {
            return false;
        }

        // 2. Execute the deletion query cleanly against your class connection link
        $sql = "DELETE FROM posts WHERE id = '$sanitized_id'";
        return mysqli_query($this->conn, $sql);
    }


}
?>
