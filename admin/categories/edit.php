<?php
// Relative path to find your master database connection link
include_once "../../database/db.php"; 

// Capture and sanitize the ID from the URL query parameter (?id=X)
$cat_id = isset($_GET['id']) ? mysqli_real_escape_string($conn, trim($_GET['id'])) : '';

$current_category = null;
if (!empty($cat_id)) {
    $cat_query = "SELECT * FROM categories WHERE id = '$cat_id' LIMIT 1";
    $cat_result = mysqli_query($conn, $cat_query);
    if ($cat_result && mysqli_num_rows($cat_result) > 0) {
        $current_category = mysqli_fetch_assoc($cat_result);
    }
}

// Redirect fallback block if the specified category ID doesn't exist
if (!$current_category) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Error: Category record with ID ($cat_id) not found in the database.</div></div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Category</title>
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
                    <h2>Edit Category</h2>
                    <p class="text-muted">Modify the field below to update this category name.</p>
                </div>

                <!-- Container for AJAX message response -->
                <div id="response-msg" class="mt-3"></div>

                <form id="edit-category-form">
                    <!-- Routing flags and unique row key values -->
                    <input type="hidden" name="action_type" value="update_category_record">
                    <input type="hidden" name="id" value="<?php echo $current_category['id']; ?>">

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Category Name</label>
                        <input type="text" name="name" class="form-control"
                            value="<?php echo htmlspecialchars($current_category['name']); ?>" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Update Category</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('edit-category-form');

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Stop standard browser submission page reloads

                var formData = new FormData(this);
                var responseMsg = document.getElementById('response-msg');

                if (responseMsg) {
                    responseMsg.innerHTML =
                        '<div class="alert alert-info">Sending update requests...</div>';
                }

                // Fire the native browser fetch API network handler to communicate with the AJAX folder
                fetch('ajax/update_category.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        if (data.status === 'success') {
                            responseMsg.innerHTML = '<div class="alert alert-success">' + data
                                .message + '</div>';
                            // Smoothly refresh or redirect to your management list view after success
                            setTimeout(function() {
                                window.location.href = 'list.php';
                            }, 1200);
                        } else {
                            responseMsg.innerHTML = '<div class="alert alert-danger">' + data
                                .message + '</div>';
                        }
                    })
                    .catch(function(error) {
                        console.error("Critical Error Detail:", error);
                        responseMsg.innerHTML =
                            '<div class="alert alert-danger">Server connection error or invalid backend configuration.</div>';
                    });
            });
        }
    });
    </script>
</body>

</html>