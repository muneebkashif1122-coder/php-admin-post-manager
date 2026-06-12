<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Posts</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <style>
    /* Updated class name to match post images */
    .post-thumb {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #ddd;
    }
    </style>
</head>

<body>
    <div class="d-flex">
        <?php include("../../layout/sidebar.php"); ?>

        <div class="flex-grow-1 p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Posts Management</h2>
                <a href="add.php" class="btn btn-success">Add New Post</a>
            </div>

            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Author ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Category ID</th>
                        <th>Image</th>
                        <th>Created At</th>
                        <th>Edit/Delete</th>
                    </tr>
                </thead>

                <!-- 🚀 FIXED: Renamed target ID hook to posts-table-body -->
                <tbody id="posts-table-body">
                    <tr>
                        <!-- 🚀 FIXED: Changed colspan to 7 to match your exact column count -->
                        <td colspan="7" class="text-center text-muted">Loading posts...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
        $.ajax({
            url: 'ajax/show.php',
            type: 'GET',
            data: {
                action_type: 'fetch_posts' // 🚀 Matches the exact condition inside show.php
            },
            dataType: 'json',
            success: function(response) {
                var tableBody = $('#posts-table-body');
                tableBody.empty(); // Safely clears out loading messages

                if (response.status === 'success') {
                    var posts = response.data;

                    if (posts.length > 0) {
                        $.each(posts, function(index, item) {

                            // 🚀 FIXED: Step up ONE level out of 'posts/' to reach 'admin/uploads/'
                            var imageSource = item.image ? '../' + item.image :
                                '../../assets/no-image.png';

                            // Safe description truncation handler
                            var shortDesc = '';
                            if (item.description) {
                                shortDesc = item.description.length > 50 ?
                                    item.description.substr(0, 50) + '...' :
                                    item.description;
                            } else {
                                shortDesc =
                                    '<span class="text-muted">No description</span>';
                            }



                            // var imageSource = '../../' + (item.image ||
                            //     'assets/images/default.jpg');

                            var row = '<tr>' +
                                '<td>' + (item.id || '') + '</td>' +
                                '<td>' + (item.author_name ||
                                    '<span class="text-muted">Unknown Author</span>') +
                                '</td>' +
                                '<td><strong>' + (item.title || '') + '</strong></td>' +
                                '<td>' + shortDesc + '</td>' +
                                '<td>' + (item.category_name ||
                                    '<span class="text-muted">Uncategorized</span>') +
                                '</td>' +
                                '<td><img src="' + imageSource +
                                '" class="post-thumb" alt="Post Img" width="50" height="50" style="object-fit: cover;"></td>' +
                                '<td>' + (item.created_at || '') + '</td>' +
                                '<td>' +
                                // 🚀 FIXED: Removed the d-flex utility container to prevent layout hidden collapses
                                '<a href="edit.php?id=' + (item.id || '') +
                                '" class="btn btn-outline-primary btn-sm" style="padding: 2px 8px; font-size: 0.75rem; margin-right: 4px;"><i class="fa fa-pencil"></i> Edit</a>' +
                                '<button type="button" data-id="' + (item.id || '') +
                                '" class="btn btn-outline-danger btn-sm delete-post-btn" style="padding: 2px 8px; font-size: 0.75rem;"><i class="fa fa-trash"></i> Delete</button>' +
                                '</td>' +
                                '</tr>';

                            tableBody.append(row);

                        });

                    } else {
                        tableBody.append(
                            '<tr><td colspan="7" class="text-center text-warning">No posts found inside the database.</td></tr>'
                        );
                    }
                } else {
                    tableBody.append('<tr><td colspan="7" class="text-center text-danger">Error: ' +
                        response.message + '</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                $('#posts-table-body').html(
                    '<tr><td colspan="7" class="text-center text-danger">Failed to connect to show.php endpoint.</td></tr>'
                );
                console.error("AJAX Processing Error Log:", error);
            }

        });
    });

    $(document).on('click', '.delete-post-btn', function() {
        var postId = $(this).data('id'); // Captures data-id="X" from the button
        var rowElement = $(this).closest('tr'); // Tracks the current row HTML element to animate removal

        if (confirm("Are you absolutely sure you want to permanently delete this post?")) {
            $.ajax({
                url: 'ajax/delete_post.php', // Targets your script file path exactly
                type: 'POST',
                data: {
                    action_type: 'delete_post_record',
                    id: postId // 🚀 FIXED: Changed from authorId to postId to match your variable tracking assignment key name
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Smoothly fades out and deletes the row from your HTML dashboard table grid
                        rowElement.fadeOut(400, function() {
                            $(this).remove();
                        });
                    } else {
                        // Triggers if it hits 'Missing ID' or 'Database query failed'
                        alert("Backend Refusal: " + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    // 🚀 FIXED: Updated error string description to match the actual post script module path
                    alert("Critical Error: Could not communicate with delete_post.php handler.");
                    console.error("AJAX Error details:", error);
                }
            });
        }
    });
    </script>
</body>

</html>