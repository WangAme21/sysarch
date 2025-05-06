<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $result = $conn->query("SELECT filepath FROM resources WHERE id = $id");
    if ($row = $result->fetch_assoc()) {
        $filepath = $row['filepath'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }

    $conn->query("DELETE FROM resources WHERE id = $id");
}

header("Location: lab_resources.php");
exit();
?>
