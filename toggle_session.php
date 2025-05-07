<?php
require_once 'computer_control.php';
$cc = new computer_control();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pcId = $_POST['pc_id'];
    $action = $_POST['action'];
    $studentId = isset($_POST['student_id']) ? $_POST['student_id'] : null;

    if ($action === 'start') {
        // Start a session
        $cc->startSession($pcId, $studentId);
    } elseif ($action === 'end') {
        // End the session
        $cc->endSession($pcId);
    }
}
?>
