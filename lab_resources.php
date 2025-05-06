<?php
// Include database connection (assuming you have a db.php file)
include 'db.php';

// Configuration
$upload_dir = 'uploads/'; // Directory to store uploaded files
$max_file_size = 10 * 1024 * 1024; // 10MB limit
$allowed_types = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'zip', 'rar', 'jpg', 'jpeg', 'png'];

// Helper function to get file extension
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

// Function to handle file uploads
function handleFileUpload($file, $upload_dir, $max_file_size, $allowed_types) {
    if ($file['error'] == UPLOAD_ERR_OK) {
        if ($file['size'] > $max_file_size) {
            return ['error' => 'File size exceeds the limit.'];
        }

        $file_ext = getFileExtension($file['name']);
        if (!in_array($file_ext, $allowed_types)) {
            return ['error' => 'Invalid file type. Allowed types: ' . implode(', ', $allowed_types)];
        }

        $new_file_name = uniqid() . '.' . $file_ext;
        $file_path = $upload_dir . $new_file_name;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            return ['success' => true, 'file_path' => $file_path, 'file_name' => $new_file_name, 'original_name' => $file['name']];
        } else {
            return ['error' => 'Failed to upload file.'];
        }
    } elseif ($file['error'] != UPLOAD_ERR_NO_FILE) { //handle other errors except when no file is uploaded
        return ['error' => 'Upload error: ' . $file['error']];
    }
    return ['success' => true]; // Return success even if no file, handle the rest in main code
}
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $link = $_POST['link']; // Get the link from the form

    // Handle file upload
    $upload_result = handleFileUpload($_FILES['file'], $upload_dir, $max_file_size, $allowed_types);

    if (isset($upload_result['error'])) {
        echo "<script>alert('" . $upload_result['error'] . "');</script>";
        //redirect back to the form
        echo "<script>window.location.href='lab_resources.php';</script>";
        exit;
    }

    if ($upload_result['success']) {
        $file_name = $upload_result['file_name'] ?? null; //important to set null if no file
        $original_name = $upload_result['original_name'] ?? null;
        $file_path = $upload_result['file_path'] ?? null;

        // Insert data into the database
        $stmt = $connection->prepare("INSERT INTO lab_resources (title, description, file_name, original_name, file_path, status, link) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $title, $description, $file_name, $original_name, $file_path, $status, $link);  // Include $link

        if ($stmt->execute()) {
            echo "<script>alert('Resource uploaded successfully!');</script>";
            //redirect back to the form
            echo "<script>window.location.href='lab_resources.php';</script>";
            exit;

        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Retrieve file path for deletion
    $stmt = $connection->prepare("SELECT file_path FROM lab_resources WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $file_path = $row['file_path'];
    $stmt->close();

    // Delete the record from the database
    $stmt = $connection->prepare("DELETE FROM lab_resources WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Delete the file if it exists
        if ($file_path && file_exists($file_path)) {
            unlink($file_path);
        }
        echo "<script>alert('Resource deleted successfully!');</script>";
        //redirect back to the form
        echo "<script>window.location.href='lab_resources.php';</script>";
        exit;
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
    $stmt->close();
}

// Handle edit
if (isset($_POST['edit'])) {
    $id = $_POST['edit_id'];
    $title = $_POST['edit_title'];
    $description = $_POST['edit_description'];
    $status = $_POST['edit_status'];
    $link = $_POST['edit_link']; // Get the edited link

    $stmt = $connection->prepare("UPDATE lab_resources SET title = ?, description = ?, status = ?, link = ? WHERE id = ?"); // Include link in update
    $stmt->bind_param("ssssi", $title, $description, $status, $link, $id); //bind link

    if ($stmt->execute()) {
        echo "<script>alert('Resource updated successfully!');</script>";
        //redirect back to the form
        echo "<script>window.location.href='lab_resources.php';</script>";
        exit;
    } else {
        echo "Error updating record: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all resources from the database
$result = $connection->query("SELECT * FROM lab_resources ORDER BY id DESC");
$resources = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/styles.css?v=<?php echo time(); ?>">
    <title>Lab Resources</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>

      nav{
        display:flex;
        align-items: center;
        justify-content: space-between;
        height: 105px;
      }
        .container {
            margin-top: 20px;
        }
        .resource-item {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .action-buttons {
            display: flex;
        }
        .edit-btn, .delete-btn, .download-btn {
            margin-right: 5px;
            cursor: pointer;
        }
        .edit-btn { color: #28a745; }
        .delete-btn { color: #dc3545; }
        .download-btn { color: #17a2b8; }

        /* Style for the form */
        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 8px 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button[type="submit"] {
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<nav>
    <h1>CCS Admin</h1>
    <div class="menu-icon" id="menu-icon">☰</div>
    <div class="nav-links-admin" id="nav-links">
        <a id="home-nav" href="admindashboard.php"> Home</a>
        <a href="#" onclick="searchFunction()" id="search-btn"> Search Students</a>
        <a href="current-sit-in.php"> Sit-in</a>
        <a href="view-sit-in-records.php">Sit-in Records</a>
        <a href="sit-in-reports.php"> Sit-in Reports</a>
        <a href="feedback-reports.php"> Feedback Reports</a>   
        <a href="view-reservations.php"> Reservation</a>
        <a href="student_management.php">Student Info</a>
        <a href="lab_schedule.php">Lab Schedule</a>
        <a href="lab_resources.php">Lab Resources</a>
        <a href="admin.php" class="logout-btn" id="logoutbtn"> Log out </a>
    </div> 
</nav>
    <div class="container">
        <h2>Upload New Resource</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="link">Link (Optional)</label>
                <input type="text" class="form-control" id="link" name="link">
            </div>
            <div class="form-group">
                <label for="file">Select File</label>
                <input type="file" class="form-control-file" id="file" name="file">
                <small class="text-muted">Max file size: 10MB. Allowed types: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, TXT, ZIP, RAR, JPG, JPEG, PNG</small>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status">
                    <option value="enabled">Enabled</option>
                    <option value="disabled">Disabled</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>

        <h2>Lab Resources</h2>
        <?php if (empty($resources)): ?>
            <p>No resources available.</p>
        <?php else: ?>
            <?php foreach ($resources as $resource): ?>
                <div class="resource-item">
                    <div>
                        <h4><?php echo htmlspecialchars($resource['title']); ?></h4>
                        <p><?php echo htmlspecialchars($resource['description']); ?></p>
                        <?php if ($resource['link']): ?>
                            <p><strong>Link:</strong> <a href="<?php echo htmlspecialchars($resource['link']); ?>" target="_blank"><?php echo htmlspecialchars($resource['link']); ?></a></p>
                        <?php endif; ?>
                        <p>
                            <strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($resource['status'])); ?>
                            <?php if ($resource['file_name']): ?>
                                 | <strong>File:</strong> <?php echo htmlspecialchars($resource['original_name']); ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="action-buttons">
                        <button class="btn btn-sm edit-btn" data-toggle="modal" data-target="#editModal<?php echo $resource['id']; ?>">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm delete-btn" onclick="if(confirm('Are you sure you want to delete this resource?')) { window.location.href='lab_resources.php?delete=<?php echo $resource['id']; ?>'; }">
                            <i class="fas fa-trash"></i>
                        </button>
                        <?php if ($resource['file_name']): ?>
                            <a href="<?php echo $upload_dir . $resource['file_name']; ?>" download="<?php echo $resource['original_name']; ?>" class="btn btn-sm download-btn">
                                <i class="fas fa-download"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="modal fade" id="editModal<?php echo $resource['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Resource</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="edit_id" value="<?php echo $resource['id']; ?>">
                                    <div class="form-group">
                                        <label for="edit_title">Title</label>
                                        <input type="text" class="form-control" id="edit_title" name="edit_title" value="<?php echo htmlspecialchars($resource['title']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_description">Description</label>
                                        <textarea class="form-control" id="edit_description" name="edit_description" required><?php echo htmlspecialchars($resource['description']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_link">Link</label>
                                        <input type="text" class="form-control" id="edit_link" name="edit_link" value="<?php echo htmlspecialchars($resource['link']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_status">Status</label>
                                        <select class="form-control" id="edit_status" name="edit_status">
                                            <option value="enabled" <?php echo $resource['status'] === 'enabled' ? 'selected' : ''; ?>>Enabled</option>
                                            <option value="disabled" <?php echo $resource['status'] === 'disabled' ? 'selected' : ''; ?>>Disabled</option>
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary" name="edit">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
