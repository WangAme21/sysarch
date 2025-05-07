<?php
// Include database connection
include 'db.php';

// Fetch only enabled resources
$stmt = $connection->prepare("SELECT * FROM lab_resources WHERE status = 'enabled' ORDER BY id DESC");
$stmt->execute();
$result = $stmt->get_result();
$resources = $result->fetch_all(MYSQLI_ASSOC);
$upload_dir = 'uploads/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab Resources</title>
    <link rel="stylesheet" href="assets/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>

        nav{
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 105px;
        }
        .container {
            margin-top: 40px;
        }
        .resource-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
        }
        .resource-title {
            font-size: 1.2rem;
            font-weight: bold;
        }
        .resource-description {
            margin-top: 10px;
        }
        .resource-link a {
            word-break: break-all;
        }
    </style>
</head>
<body>

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
        <a href="#" class="logout-btn" id="logoutbtn"> Log out </a>
    </div> 
</nav>

<div class="container">
    <h2 class="mb-4">Lab Resources</h2>

    <?php if (empty($resources)): ?>
        <div class="alert alert-info">No lab resources available at the moment.</div>
    <?php else: ?>
        <?php foreach ($resources as $resource): ?>
            <div class="resource-card">
                <div class="resource-title"><?php echo htmlspecialchars($resource['title']); ?></div>
                <div class="resource-description"><?php echo htmlspecialchars($resource['description']); ?></div>
                <?php if (!empty($resource['link'])): ?>
                    <div class="resource-link mt-2">
                        <strong>Link:</strong>
                        <a href="<?php echo htmlspecialchars($resource['link']); ?>" target="_blank">
                            <?php echo htmlspecialchars($resource['link']); ?>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if (!empty($resource['file_name'])): ?>
                    <div class="resource-file mt-2">
                        <strong>File:</strong>
                        <a href="<?php echo $upload_dir . $resource['file_name']; ?>" download="<?php echo htmlspecialchars($resource['original_name']); ?>">
                            <?php echo htmlspecialchars($resource['original_name']); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

</body>
</html>
