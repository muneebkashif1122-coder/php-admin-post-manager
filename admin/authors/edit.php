<?php
include_once "../../database/db.php"; 

$author_id = isset($_GET['id']) ? mysqli_real_escape_string($conn, trim($_GET['id'])) : '';

$current_author = null;
if (!empty($author_id)) {
    $author_query = "SELECT * FROM authors WHERE id = '$author_id' LIMIT 1";
    $author_result = mysqli_query($conn, $author_query);
    if ($author_result && mysqli_num_rows($author_result) > 0) {
        $current_author = mysqli_fetch_assoc($author_result);
    }
}

if (!$current_author) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Error: Author record with ID ($author_id) not found.</div></div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Author</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>
    <div class="d-flex">
        <?php include("../../layout/sidebar.php"); ?>

        <div class="flex-grow-1 p-5">
            <div class="form-panel" style="max-width: 600px; margin: 0 auto;">
                <div class="form-header mb-4">
                    <h2>Edit Author</h2>
                    <p class="text-muted">Modify the fields below to update this author's profile details.</p>
                </div>

                <div id="response-msg" class="mt-3"></div>

                <form id="edit-author-form" enctype="multipart/form-data">
                    <input type="hidden" name="action_type" value="update_author_record">
                    <input type="hidden" name="id" value="<?php echo $current_author['id']; ?>">

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Author Name</label>
                        <input type="text" name="author" class="form-control"
                            value="<?php echo htmlspecialchars($current_author['author']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Biography (Bio)</label>
                        <textarea name="bio" class="form-control"
                            rows="4"><?php echo htmlspecialchars($current_author['bio'] ?? ''); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Profile Photo (Leave blank to keep current)</label>
                        <input class="form-control" type="file" name="author_image" accept="image/*">
                        <?php if (!empty($current_author['image'])): ?>
                        <div class="mt-2">
                            <small class="text-muted d-block">Current Profile Photo:</small>
                            <img src="../../<?php echo htmlspecialchars($current_author['image']); ?>"
                                style="max-height: 100px; object-fit: contain;" class="rounded border p-1 mt-1">

                        </div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Update Author</button>
                </form>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('edit-author-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                var responseMsg = document.getElementById('response-msg');

                if (responseMsg) {
                    responseMsg.innerHTML =
                        '<div class="alert alert-info">Processing author updates...</div>';
                }

                // 🚀 THE EXACT INTERACTION FIX: 
                // If the form used 'author_image', copy its reference to 'post_image' inside the AJAX stream
                var authorImageFile = form.querySelector('input[name="author_image"]');
                if (authorImageFile && authorImageFile.files.length > 0) {
                    formData.append('post_image', authorImageFile.files[0]);
                }

                fetch('ajax/update_author.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        console.log("Server Response:", data);
                        if (data.status === 'success') {
                            responseMsg.innerHTML = '<div class="alert alert-success">' + data
                                .message + '</div>';
                            setTimeout(function() {
                                window.location.href = 'list.php';
                            }, 1200);
                        } else {
                            responseMsg.innerHTML = '<div class="alert alert-danger">' + data
                                .message + '</div>';
                        }
                    })
                    .catch(function(error) {
                        console.error("Error Details:", error);
                        responseMsg.innerHTML =
                            '<div class="alert alert-danger">Server communication failure. Check backend configuration.</div>';
                    });
            });
        }
    });
    </script>

</body>

</html>