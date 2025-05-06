<?php
session_start();
include('db.php');

// Check if 'id' and 'status' are passed via POST
if (isset($_POST['id']) && isset($_POST['status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    if ($status === 'Accepted') {
        // Get the id_number from reservations
        $queryUser = "SELECT id_number FROM reservations WHERE id = ?";
        $stmtUser = $connection->prepare($queryUser);

        if (!$stmtUser) {
            die('Prepare failed: ' . $connection->error);
        }

        $stmtUser->bind_param("i", $id);
        $stmtUser->execute();
        $resultUser = $stmtUser->get_result();

        if ($resultUser->num_rows > 0) {
            $user = $resultUser->fetch_assoc();
            $idNumber = $user['id_number'];

            // Use idno instead of id_number here
            $decrementQuery = "UPDATE userstbl SET sessions = GREATEST(sessions - 1, 0) WHERE idno = ?";
            $stmtDecrement = $connection->prepare($decrementQuery);

            if (!$stmtDecrement) {
                die('Prepare failed: ' . $connection->error);
            }

            $stmtDecrement->bind_param("s", $idNumber);
            $stmtDecrement->execute();

            // Get updated session count
            $querySessions = "SELECT sessions FROM userstbl WHERE idno = ?";
            $stmtSessions = $connection->prepare($querySessions);

            if (!$stmtSessions) {
                die('Prepare failed: ' . $connection->error);
            }

            $stmtSessions->bind_param("s", $idNumber);
            $stmtSessions->execute();
            $resultSessions = $stmtSessions->get_result();
            $userSessions = $resultSessions->fetch_assoc();

            $_SESSION['sessions'] = $userSessions['sessions'];

            // ✅ Update remaining_session in reservations
            $updateRemaining = "UPDATE reservations SET remaining_session = ?, status = 'Accepted' WHERE id = ?";
            $stmtRemaining = $connection->prepare($updateRemaining);

            if (!$stmtRemaining) {
                die('Prepare failed (remaining_session update): ' . $connection->error);
            }

            $stmtRemaining->bind_param("ii", $userSessions['sessions'], $id);
            $stmtRemaining->execute();

            // Store the student in sit_in_students session
            if (!isset($_SESSION['sit_in_students'])) {
                $_SESSION['sit_in_students'] = [];
            }

            // Add the student ID to the session if it's not already there
            if (!in_array($idNumber, $_SESSION['sit_in_students'])) {
                $_SESSION['sit_in_students'][] = $idNumber;
            }

            // Update the status to "Active" in the userstbl table
            $updateStatus = "UPDATE userstbl SET status = 'Active' WHERE idno = ?";
            $stmtStatus = $connection->prepare($updateStatus);

            if (!$stmtStatus) {
                die('Prepare failed (status update): ' . $connection->error);
            }

            $stmtStatus->bind_param("s", $idNumber);
            $stmtStatus->execute();
        } else {
            die('No reservation found for this ID.');
        }
    } elseif ($status === 'Declined') {
        // Update status to Declined
        $updateStatus = "UPDATE reservations SET status = 'Declined' WHERE id = ?";
        $stmtDecline = $connection->prepare($updateStatus);

        if (!$stmtDecline) {
            die('Prepare failed (decline): ' . $connection->error);
        }

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
