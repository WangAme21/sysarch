<?php
session_start();
echo "Session ID: " . session_id();  // This ensures session is started
echo "<br>";
echo "Session 'idno': " . (isset($_SESSION['idno']) ? $_SESSION['idno'] : 'Not set');

include('db.php');

if (isset($_POST['feedback-user'])) {
    $history_id = $_POST['history_id'];
    $feedback = mysqli_real_escape_string($connection, $_POST['feedback']);

    // Debugging output
    echo "History ID: $history_id<br>";
    echo "Feedback: $feedback<br>";

    if (!empty($history_id) && !empty($feedback)) {
        $query = "UPDATE sit_in_history SET feedback = ? WHERE id = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "si", $feedback, $history_id); // "si" = string, integer
        if (mysqli_stmt_execute($stmt)) {
           
             header("Location: sitin-history.php?feedback=success");  // Comment out the redirect for debugging
            exit;
        } else {
            echo "Error updating feedback: " . mysqli_error($connection);
        }
    } else {
        echo "Feedback cannot be empty.";
    }
} else {
    echo "Invalid request: feedback-user not set.";
}
?>
