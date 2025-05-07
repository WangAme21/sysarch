<?php
require_once 'computer_control.php';
$cc = new computer_control();

if (isset($_GET['lab_id'])) {
    $labId = $_GET['lab_id'];
    $computers = $cc->getLabComputers($labId);

    echo json_encode($computers);
}
?>
