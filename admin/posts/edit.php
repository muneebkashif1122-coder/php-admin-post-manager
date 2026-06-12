<?php
// 3 levels up to the root database folder
include_once "../../database/db.php"; ; 

$post_id = isset($_GET['id']) ? mysqli_real_escape_string($conn, trim($_GET['id'])) : '';

$current_post = null;
if (!empty($post_id)) {
    $post_query = "SELECT * FROM posts WHERE id = '$post_id' LIMIT 1";
    $post_result = mysqli_query($conn, $post_query);
    if ($post_result && mysqli_num_rows($post_result) > 0) {
        $current_post = mysqli_fetch_assoc($post_result);
    }
}

if (!$current_post) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Error: Post record not found.</div></div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Post</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="d-flex">
        <?php include("../../layout/sidebar.php"); ?>
        <div class="flex-grow-1 p-5">
            <div class="form-panel">
                <div class="form-header">
                    <h2>Edit Post</h2>
                    <p>Modify the fields below to update this post record.</p>
                </div>
                
                <div id="response-msg" class="mt-3"></div>

                <form id="edit-post-form" enctype="multipart/form-data">
                    <input type="hidden" name="action_type" value="update_post_record">
                    <input type="hidden" name="id" value="<?php echo $current_post['id']; ?>">
                    
                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($current_post['title']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($current_post['description']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="category_select" class="form-label">Categories</label>
                        <select name="category_id" id="category_select" class="form-select" required>
                            <option value="">Select a Category</option>
                            <?php
                            $query = "SELECT id, name FROM categories ORDER BY name ASC";
                            $result = mysqli_query($conn, $query);
                            if ($result) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $cat_id = $row['id'];
                                    $cat_name = htmlspecialchars($row['name']);
                                    $selected = ($cat_id == $current_post['category_id']) ? "selected" : "";
                                    echo "<option value='$cat_id' $selected>$cat_name</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="formFile" class="form-label">Cover Image</label>
                        <input class="form-control" type="file" id="formFile" name="post_image">
                <?php if (!empty($current_post['image'])): ?>
    <div class="mt-2">
        <small class="text-muted d-block">Current Cover:</small>
        <!-- 🚀 THE FIX: Dynamically check if '../../' already exists in the database path string -->
        <img src="<?php 
            $db_image = $current_post['image'];
            if (strpos($db_image, '../../') === 0) {
                echo htmlspecialchars($db_image);
            } else {
                echo '../../' . htmlspecialchars($db_image);
            }
        ?>" style="max-height: 100px;" class="rounded border p-1 mt-1" alt="Current Cover">
    </div>
<?php endif; ?>
</div>
<div class="mb-3">
<label for="author_select" class="form-label">Author</label>
<select name="author_id" id="author_select" class="form-select" required>
    <option value="">Select an Author</option>
    <?php
    $query = "SELECT id, author FROM authors ORDER BY author ASC";
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $author_id = $row['id'];
            $author_name = htmlspecialchars($row['author']);
            $selected = ($author_id == $current_post['author_id']) ? "selected" : "";
            echo "<option value='$author_id' $selected>$author_name</option>";
        }
    }
    ?>

                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Update Post</button>
                </form>
            </div>
        </div>
    </div>

<script src="https://googleapis.com"></script>
<script src="https://googleapis.com"></script>
<!-- Remove any jQuery script tags and replace with this native script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('edit-post-form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Stop standard browser submission page reloads

            var formData = new FormData(this);
            var responseMsg = document.getElementById('response-msg');
            
            if (responseMsg) {
                responseMsg.innerHTML = '<div class="alert alert-info">Sending update requests...</div>';
            }

            // Native browser fetch API (No library required)
            fetch('ajax/update_post.php', {
                method: 'POST',
                body: formData
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                console.log("Server Response:", data);
                if (data.status === 'success') {
                    responseMsg.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                    setTimeout(function() {
                        location.reload();
                    }, 1200);
                } else {
                    responseMsg.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                }
            })
            .catch(function(error) {
                console.error("Critical Error:", error);
                responseMsg.innerHTML = '<div class="alert alert-danger">Server connection error or invalid JSON response.</div>';
            });
        });
    }
});
</script>

</body>
</html>

