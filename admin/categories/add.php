<!DOCTYPE html>
<html lang="en">

<head>
    <title>Author Profiles</title>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="d-flex">
        <?php include("../../layout/sidebar.php"); ?>

        <div class="flex-grow-1 p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Add Category</h2>
            </div>
            <div id="response-msg" class="mt-3"></div>

            <!-- FIXED: Cleaned form attributes and added the critical action_type identifier -->
            <form id="category-form">
                <input type="hidden" name="action_type" value="add_category">

                <div class="mb-3">
                    <label class="form-label">Category Name</label>
                    <input type="text" name="category_name" class="form-control" placeholder="e.g. Technology, Health"
                        required>
                </div>
                <button type="submit" class="btn btn-primary">Save Category</button>
                <a href="../categories/category.php" class="btn btn-light">Back</a>
            </form>
        </div>
    </div>


    <!-- 1. Load jQuery first -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- 2. Category AJAX script -->
    <script>
    $(document).ready(function() {
        // Intercept category form submission
        $('#category-form').submit(function(e) {
            e.preventDefault();

            // Pack the category form fields
            var formData = new FormData(this);

            $.ajax({
                url: 'ajax/insert.php', // Submits to categories/ajax_handler.php
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                success: function(response) {
                    console.log(response);
                    // Display green success alert
                    $('#response-msg').html('<div class="alert alert-success">' + response
                        .message + '</div>');

                    // Clear the category form
                    $('#category-form')[0].reset();
                },
                error: function(xhr, status, error) {
                    console.log(error);
                    // Display red error alert
                    $('#response-msg').html('<div class="alert alert-danger">' + error +
                        '</div>');
                }
            });
        });
    });
    </script>
</body>

</html>