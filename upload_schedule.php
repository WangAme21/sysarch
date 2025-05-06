<?php
$conn = new mysqli('localhost', 'root', '', 'usersdb');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Upload logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Check if a file is selected and uploaded
    if (isset($_FILES['schedule_file']) && $_FILES['schedule_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';

        // Ensure the upload directory exists, create if not
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Get the file details
        $file_name = basename($_FILES['schedule_file']['name']);
        $target_file = $upload_dir . $file_name;

        // Optional: Validate the file type (if needed)
        $allowed_file_types = ['pdf', 'docx', 'jpg', 'png', 'jpeg'];
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (!in_array($file_type, $allowed_file_types)) {
            $message = "Invalid file type. Only PDF, DOCX, JPG, PNG, JPEG files are allowed.";
        } else {
            // Attempt to move the uploaded file to the desired directory
            if (move_uploaded_file($_FILES['schedule_file']['tmp_name'], $target_file)) {
                // Prepare and execute the SQL query
                $stmt = $conn->prepare("INSERT INTO lab_schedules (title, description, start_date, end_date, file_path) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $title, $description, $start_date, $end_date, $file_name);
                if ($stmt->execute()) {
                    $message = "Schedule uploaded successfully!";
                } else {
                    $message = "Database error: " . $conn->error;
                }
            } else {
                $message = "Failed to upload file.";
            }
        }
    } else {
        $message = "No file uploaded or there was an error uploading the file.";
    }
}
?>
