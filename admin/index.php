<?php
    $conn = mysqli_connect("localhost", "root", "", "project-2");
    if (!$conn) { die("Database connection failed: " . mysqli_connect_error()); }
?>


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

    <!-- SIDEBAR INCLUDED FROM LAYOUT -->
    <?php include "../layout/sidebar.php"; ?>

    <div class="flex-grow-1 p-5">
        <h1>Admin Dashboard</h1>
        <p>Welcome to the project management area.</p>
        <div class="alert alert-info">Select 'Author' from the sidebar to add a new profile.</div>
    </div>

</div>
</body>