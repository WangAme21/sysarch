<?php
session_start();
include('db.php');

// Check if 'id' and 'status' are passed via POST
if (isset($_POST['id']) && isset($_POST['status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    if ($status === 'Accepted') {
        // Get the id_number, lab, and pc_number from reservations
        $queryUser = "SELECT id_number, lab, pc_number FROM reservations WHERE id = ?";
        $stmtUser = $connection->prepare($queryUser);
        $stmtUser->bind_param("i", $id);
        $stmtUser->execute();
        $resultUser = $stmtUser->get_result();

        if ($resultUser->num_rows > 0) {
            $user = $resultUser->fetch_assoc();
            $idNumber = $user['id_number'];
            $lab = $user['lab'];
            $pcNumber = $user['pc_number'];

            // Decrease the session count for the user
            $decrementQuery = "UPDATE userstbl SET sessions = GREATEST(sessions - 1, 0) WHERE idno = ?";
            $stmtDecrement = $connection->prepare($decrementQuery);
            $stmtDecrement->bind_param("s", $idNumber);
            $stmtDecrement->execute();

            // Get updated session count
            $querySessions = "SELECT sessions FROM userstbl WHERE idno = ?";
            $stmtSessions = $connection->prepare($querySessions);
            $stmtSessions->bind_param("s", $idNumber);
            $stmtSessions->execute();
            $resultSessions = $stmtSessions->get_result();
            $userSessions = $resultSessions->fetch_assoc();

            $_SESSION['sessions'] = $userSessions['sessions'];

            // Update remaining_session and reservation status
            $updateRemaining = "UPDATE reservations SET remaining_session = ?, status = 'Accepted' WHERE id = ?";
            $stmtRemaining = $connection->prepare($updateRemaining);
            $stmtRemaining->bind_param("ii", $userSessions['sessions'], $id);
            $stmtRemaining->execute();

            // Update PC status to 'in_use'
            $updatePCStatus = "UPDATE pcs SET status = 'in_use' WHERE lab = ? AND pc_number = ?";
            $stmtUpdatePC = $connection->prepare($updatePCStatus);
            $stmtUpdatePC->bind_param("si", $lab, $pcNumber);
            $stmtUpdatePC->execute();
        } else {
            die('No reservation found for this ID.');
        }
    } elseif ($status === 'Declined') {
        // Update status to Declined
        $updateStatus = "UPDATE reservations SET status = 'Declined' WHERE id = ?";
        $stmtDecline = $connection->prepare($updateStatus);
        $stmtDecline->bind_param("i", $id);
        $stmtDecline->execute();
    }

    // Redirect back after handling
    $_SESSION['message'] = "Reservation updated successfully.";
    header("Location: view-reservations.php");
    exit;
} else {
    echo "Error: Missing parameters.";
    exit;
}
?>
