<?php
include_once "../../database/db.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Write a Post</title>
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
                    <h2>Write a Post</h2>
                    <p>Fill out the fields below to publish a post.</p>
                </div>
                
                <!-- FIXED: Container added so your green/red alert text can physically render -->
                <div id="response-msg" class="mt-3"></div>

                <!-- FIXED: Added id="post-form" to map with jQuery hook -->
                <form id="post-form" enctype="multipart/form-data">
                    <!-- FIXED: Value changed to 'create_post' to match your handler logic -->
                    <input type="hidden" name="action_type" value="create_post">
                    
                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4"></textarea>
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
                                    echo "<option value='$cat_id'>$cat_name</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="formFile" class="form-label">Cover Image</label>
                        <input class="form-control" type="file" id="formFile" name="post_image">
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
                                    echo "<option value='$author_id'>$author_name</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
        </div>
    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
$(document).ready(function() {
    $('#post-form').submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: 'ajax/insert.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            success: function (response) {
                console.log(response);
                if(response.status === 'success') {
                    $('#response-msg').html('<div class="alert alert-success">' + response.message + '</div>');
                    $('#post-form')[0].reset();
                } else {
                    $('#response-msg').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr.responseText);
                $('#response-msg').html('<div class="alert alert-danger">Server connection error.</div>');
            }
        });
    });
});
</script>
</body>
</html>


