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
                <h2>Author Profiles</h2>
                <a href="add.php" class="btn btn-success">Add New Author</a>
            </div>

            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Author</th>
                        <th>Bio</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Edit/Delete</th>
                    </tr>
                </thead>
                <!-- FIXED: Added the critical targeted tbody id container for jQuery row injections -->
                <tbody id="authors-table-body">
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Loading active profiles from database...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://googleapis.com"></script>
<script src="https://googleapis.com"></script>
<script>
$(document).ready(function() {
    loadAuthorsRealtime();

    // Global delegated click event listener to process asynchronous profile deletion row unlinks
    $(document).on('click', '.delete-author-btn', function() {
        var authorId = $(this).data('id'); 
        var rowElement = $(this).closest('tr'); 

        if (confirm("Are you absolutely sure you want to permanently delete this author profile?")) {
            $.ajax({
                url: 'ajax/delete.php', 
                type: 'POST',
                data: {
                    action_type: 'delete_author_record',
                    id: authorId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        rowElement.fadeOut(400, function() {
                            $(this).remove();
                        });
                    } else {
                        alert("Backend Refusal: " + response.message);
                    }
                },
                error: function(xhr, status, error) {
                
                    console.log(error);
                }
            });
        }
    });
});

function loadAuthorsRealtime() {
    $.ajax({
        url: 'ajax/show.php',
        type: 'GET',
        data: {
            action_type: 'fetch_authors'
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                var authors = response.data;
                var tableRows = '';

                if (authors.length > 0) {
                    $.each(authors, function(index, row) {
                        var imgHtml = '';

                        if (row.image && row.image.trim() !== "") {
                            var dbImage = row.image.trim();
                            var finalImgSrc = "";

                            // 🚀 THE CRITICAL ALIGNMENT FIX (Matches your working Post strategy)
                            // If it's an old entry containing path prefixes, clean it or match it
                            if (dbImage.indexOf('../../') === 0) {
                                // Strip old embedded prefix pathing characters to unify render streams
                                finalImgSrc = dbImage.replace('../../', '../../');
                            } else if (dbImage.indexOf('uploads/') === 0) {
                                // Steps out one level into admin space, exactly like your functional post list
                                finalImgSrc = '../' + dbImage;
                            } else {
                                // Default fallback to step up one directory layer
                                finalImgSrc = '../' + dbImage;
                            }

                            imgHtml = '<img src="' + finalImgSrc + '" width="50" height="50" style="object-fit: cover;" class="rounded-circle" alt="Author">';
                        } else {
                            imgHtml = '<div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 10px;">No Image</div>';
                        }

                        var bioText = row.bio ? row.bio : 'No biography provided.';
                        if (bioText.length > 50) {
                            bioText = bioText.substring(0, 50) + '...';
                        }

                        tableRows += '<tr>';
                        tableRows += '<td>' + row.id + '</td>';
                        tableRows += '<td>' + imgHtml + '</td>';
                        tableRows += '<td class="fw-bold">' + row.author + '</td>';
                        tableRows += '<td>' + bioText + '</td>';
                        tableRows += '<td>' + (row.created_at ? row.created_at : 'N/A') + '</td>';
                        tableRows += '<td>' + (row.updated_at ? row.updated_at : 'N/A') + '</td>';
                        tableRows += '<td>' +
                            '<a href="edit.php?id=' + row.id + '" class="btn btn-outline-primary btn-sm" style="padding: 2px 8px; font-size: 0.75rem; margin-right: 4px;"><i class="fa fa-pencil"></i> Edit</a>' +
                            '<button type="button" data-id="' + row.id + '" class="btn btn-outline-danger btn-sm delete-author-btn" style="padding: 2px 8px; font-size: 0.75rem;"><i class="fa fa-trash"></i> Delete</button>' +
                            '</td>';
                        tableRows += '</tr>';
                    });
                } else {
                    tableRows = '<tr><td colspan="7" class="text-center text-muted py-4">No authors found inside the system database.</td></tr>';
                }

                $('#authors-table-body').html(tableRows);
            }
        },
        error: function(xhr, status, error) {
            console.error("Failed to communicate with your PHP backend:", error);
            $('#authors-table-body').html(
                '<tr><td colspan="7" class="text-center text-danger py-4">Error loading data components from server connection.</td></tr>'
            );
        }
    });
}


</script>

</body>

</html>