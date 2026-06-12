<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Author Profiles</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <style>
    .profile-thumb {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 50%;
        border: 1px solid #ddd;
    }
    </style>
</head>

<body>
    <div class="d-flex">
        <?php include("../../layout/sidebar.php"); ?>

        <div class="flex-grow-1 p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Categories</h2>
                <a href="add.php" class="btn btn-success">Add New Category</a>
            </div>

            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Categories</th>
                        <th>Edit</th>
                    </tr> <!-- 🚀 FIXED: Added missing closing table row tag -->
                </thead>

                <!-- 🚀 FIXED: Added the required tbody container and the critical matching ID hook -->
                <tbody id="category-table-body">
                    <tr>
                        <td colspan="2" class="text-center text-muted">Loading categories...</td>
                    </tr>
                </tbody>
            </table>
        </div>


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script>
        <!-- Include jQuery library first 
        -->
        <script src="https://jquery.com"></script>

        <script>
        $(document).ready(function() {
            // Trigger the AJAX call immediately when the page loads
            $.ajax({
                url: 'ajax/show.php', // Target path to your handler file
                type: 'GET',
                data: {
                    action_type: 'fetch_catgories' // Matches the exact spelling in show.php
                },
                dataType: 'json',
                success: function(response) {
                    // Clear the "Loading..." row from the table body
                    var tableBody = $('#category-table-body');
                    tableBody.empty();

                    if (response.status === 'success') {
                        var categories = response.data;

                        // Check if the database table has records
                        if (categories.length > 0) {
                            // Loop through each category row from the database
                            $.each(categories, function(index, item) {
                                var row = '<tr>' +
                                    '<td>' + item.id + '</td>' + // Displays the 'id' column
                                    '<td>' + item.name + '</td>' +
                                    // Displays the 'name' column
                                    // 🚀 FIXED: Combined both Edit and Delete buttons into one cell with safety margins
                                    '<td>' +
                                    '<a href="edit.php?id=' + item.id +
                                    '" class="btn btn-outline-primary btn-sm" style="padding: 2px 8px; font-size: 0.75rem; margin-right: 4px;"><i class="fa fa-pencil"></i> Edit</a>' +
                                    '<button type="button" data-id="' + item.id +
                                    '" class="btn btn-outline-danger btn-sm delete-category-btn" style="padding: 2px 8px; font-size: 0.75rem;"><i class="fa fa-trash"></i> Delete</button>' +
                                    '</td>' +

                                    '</tr>';
                                tableBody.append(row);
                            });
                        } else {
                            tableBody.append('<tr><td colspan="2">No categories found.</td></tr>');
                        }
                    } else {
                        tableBody.append('<tr><td colspan="2">Error: ' + response.message +
                            '</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    // Handles server crashes or file location mapping errors
                    $('#category-table-body').html(
                        '<tr><td colspan="2">Failed to load data from server.</td></tr>');
                    console.error("AJAX Error: " + error);
                }
            });
            // 🚀 2. ADDED: The missing Delete click event code block to fire actions into your database
            $(document).on('click', '.delete-category-btn', function() {
                var categoryId = $(this).data('id');
                var rowElement = $(this).closest('tr');

                if (confirm("Are you absolutely sure you want to permanently delete this category?")) {
                    $.ajax({
                        url: 'ajax/delete_category.php',
                        type: 'POST',
                        data: {
                            action_type: 'delete_category_record',
                            id: categoryId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                // Visually slides and removes the table row automatically [1]
                                rowElement.fadeOut(400, function() {
                                    $(this).remove();
                                });
                            } else {
                                alert("Server Refusal Error: " + response.message);
                            }
                        },
                        error: function() {
                            alert(
                                "Critical Failure: Could not reach the delete_category.php endpoint."
                                );
                        }
                    });
                }
            });

        });
        </script>