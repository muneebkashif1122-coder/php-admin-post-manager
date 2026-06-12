<!DOCTYPE html>
<html lang="en">
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Bootstrap Example</title>
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
                    <h2>Create Author Profile</h2>
                    <p>Fill out the database fields below to register.</p>
                </div>
                <form id="author-form" enctype="multipart/form-data">

                    <input type="hidden" name="action_type" value="register">
                    <div class="mb-3">
                        <label>Author Name</label>
                        <input type="text" name="author" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Biography</label>
                        <textarea name="bio" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="formFile" class="form-label">Default file input example</label>
                        <input class="form-control" type="file" id="formFile" name="post_image">
                    </div>
                    <button type="submit" class="btn btn-primary">Complete Registration</button>
                </form>
                <div id="response-msg" class="mt-3"></div>
            </div>
        </div>
</body>
<!-- 1. Load jQuery first -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<!-- 2. Your AJAX code -->
<script>
$(document).ready(function() {
    // 1. Trigger this on a button click or form submit
      // FORM SUBMIT
    $('#author-form').submit(function (e) {

        e.preventDefault();

        // FORM DATA
        var formData = new FormData(this);

        $.ajax({
            // FIXED: Points straight to your new insert script file path mapping
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
                    $('#author-form')[0].reset();
                } else {
                    $('#response-msg').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function (xhr, status, error) {
                console.log(error);
                $('#response-msg').html('<div class="alert alert-danger">Connection Error.</div>');
            }
        });


    });
});
</script>
</body>