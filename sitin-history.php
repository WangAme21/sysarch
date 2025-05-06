<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/styles.css?v=<?php echo time(); ?>">
    <title>Sit-in History</title>
    <style>
        /* Basic Layout */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }


        .menu-icon {
            display: none;
        }



        .logout-btn {
            background-color: red;
        }

        .logout-btn:hover {
            background-color: darkred;
        }

        /* Success message */
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px 20px;
            margin: 20px;
            width: fit-content;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        /* Sit-in history table */
        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        td {
            font-size: 14px;
        }

        /* Status badge */
        .status-pending {
            padding: 5px 10px;
            background-color: #f39c12;
            color: white;
            border-radius: 5px;
        }

        /* Feedback form */
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
        }

        textarea {
            padding: 10px;
            width: 300px;
            height: 100px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: none;
        }

        .feedback-btn {
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 49%;
        }

        .feedback-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<nav>
    <h1>Dashboard</h1>
    <div class="menu-icon" id="menu-icon">☰</div>
    <div class="nav-links" id="nav-links">
        <a href=""> Notification</a>
        <a id="home-nav" href="dashboard.php"> Home</a>
        <a href="editprofile.php"> Edit Profile</a>
        <a href="sitin-history.php">Sit-in History</a>
        <a href="reservation.php"> Reservation</a>
        <a href="lab_schedule_student.php">Lab Schedules</a>
        <a href="lab_resources_view.php">Lab Resources</a>
        <a href="index.php" class="logout-btn" id="logoutbtn"> Log out </a>
    </div> 
</nav>

<?php if (isset($_GET['feedback']) && $_GET['feedback'] === 'success'): ?>
    <div class="success-message">Feedback submitted successfully!</div>
<?php endif; ?>

<!-- Sit-in history table -->
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Status</th>
            <th>Laboratory</th>
            <th>Feedback</th>
        </tr>
    </thead>
    <tbody>
        <?php
        session_start();
        include('db.php');

        $idno = $_SESSION['idno']; // Ensure user is logged in
        $query = "SELECT * FROM sit_in_history WHERE idno = '$idno' ORDER BY date DESC";
        $result = mysqli_query($connection, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($sitIn = mysqli_fetch_assoc($result)) {
                $formattedDate = date("F j, Y, g:i a", strtotime($sitIn['date']));
                echo "<tr>";
                echo "<td>$formattedDate</td>";
                echo "<td><span class='status-pending'>{$sitIn['status']}</span></td>";
                echo "<td>{$sitIn['laboratory']}</td>";
                echo '<td>
                    <form method="post" action="submit_feedback.php">
                        <textarea name="feedback" placeholder="Your feedback..."></textarea>
                        <input type="hidden" name="history_id" value="' . $sitIn['id'] . '">    
                        <button name="feedback-user" type="submit" class="feedback-btn">Submit Feedback</button>
                    </form>

                </td>';
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No sit-in history found.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>
