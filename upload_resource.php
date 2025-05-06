<?php
require 'db.php';

$title = $_POST['title'];
$description = $_POST['description'];
$status = $_POST['status'];

$allowed_types = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'zip', 'rar', 'jpg', 'png'];
$upload_dir = "uploads/";

if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $filename = basename($_FILES['file']['name']);
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed_types)) {
        die("File type not allowed.");
    }

    $new_name = uniqid() . "." . $ext;
    $target_path = $upload_dir . $new_name;
    
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    
    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
        $filesize = round(filesize($target_path) / 1024, 2) . ' KB';
        
        $stmt = $conn->prepare("INSERT INTO resources (title, description, filename, filepath, filesize, filetype, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $title, $description, $filename, $target_path, $filesize, $ext, $status);
        $stmt->execute();
        header("Location: lab_resources.php");
        exit();
    } else {
        echo "Failed to upload file.";
    }
} else {
    echo "File upload error.";
}
?>
