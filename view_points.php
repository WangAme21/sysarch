<?php
session_start();
include('db.php');

if (!isset($_SESSION['idno'])) {
    echo "<div style='margin: 2rem; font-family: sans-serif; color: red;'>⚠️ Error: You are not logged in. Please log in to view your points.</div>";
    exit();
}

$user_id = $_SESSION['idno'];

// Get total points, claimed rewards, and current sessions
$totalPoints = $claimedRewards = $sessions = 0;
$stmt = $connection->prepare("SELECT points, claimed_rewards, sessions FROM userstbl WHERE idno = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($totalPoints, $claimedRewards, $sessions);
$stmt->fetch();
$stmt->close();

// Calculate newly earned rewards
$newRewards = floor($totalPoints / 3);
$newlyEarned = $newRewards - $claimedRewards;

if ($newlyEarned > 0) {
    $sessions += $newlyEarned;
    $claimedRewards = $newRewards;

    $stmt = $connection->prepare("UPDATE userstbl SET sessions = ?, claimed_rewards = ? WHERE idno = ?");
    $stmt->bind_param("iii", $sessions, $claimedRewards, $user_id);
    $stmt->execute();
    $stmt->close();
}

$pointsToNextReward = 3 - ($totalPoints % 3);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Points</title>
    <link rel="stylesheet" href="assets/styles.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style> 
    nav {
        height: 105px;
    }
</style>
<body class="bg-light">

<nav>
    <h1>Dashboard</h1>
    <div class="menu-icon" id="menu-icon">☰</div>
    <div class="nav-links" id="nav-links">
        <a href=""> Notification</a>
        <a id="home-nav" href="#"> Home</a>
        <a href="editprofile.php"> Edit Profile</a>
        <a href="sitin-history.php">Sit-in History</a>
        <a href="reservation.php"> Reservation</a>
        <a href="lab_schedule_student.php">Lab Schedules</a>
        <a href="lab_resources_view.php">Lab Resources</a>
        <a href="view_points.php">View Points</a>
        <a href="index.php" class="logout-btn" id="logoutbtn"> Log out </a>
    </div> 
</nav>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-success shadow">
                <div class="card-body">
                    <h5 class="card-title">⭐ Total Points</h5>
                    <h2><?= $totalPoints ?></h2>
                    <p>Points earned from lab sessions</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-warning shadow">
                <div class="card-body">
                    <h5 class="card-title">🎁 Rewards Earned</h5>
                    <h2><?= $claimedRewards ?></h2>
                    <p>Free sessions earned (3 points = 1 free session)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">🔄 Progress</h5>
            <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: <?= ($totalPoints % 3) * 33.33 ?>%" aria-valuenow="<?= $pointsToNextReward ?>" aria-valuemin="0" aria-valuemax="3"></div>
            </div>
            <p class="mt-2"><?= $pointsToNextReward ?> points left to next reward</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Session Details</h5>
            <p>Active sessions: <?= $sessions ?></p>
        </div>
    </div>
</div>

</body>
</html>
