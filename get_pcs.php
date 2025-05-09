<?php
header('Content-Type: application/json');
include('db.php');

$labId = $_GET['lab'] ?? null;

if (!$labId) {
    http_response_code(400);
    echo json_encode(['error' => 'Lab ID is required']);
    exit;
}

$query = "SELECT pc_number, id, status FROM pcs WHERE lab = ?";
$stmt = $connection->prepare($query);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: Could not prepare statement']);
    exit;
}

$stmt->bind_param("s", $labId);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: Could not execute query']);
    exit;
}


// Get existing PC data from the DB
$query = "SELECT pc_number, id, status FROM pcs WHERE lab = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $labId);
$stmt->execute();
$result = $stmt->get_result();

$existingPcs = [];
while ($row = $result->fetch_assoc()) {
    $existingPcs[$row['pc_number']] = [
        'id' => $row['id'],
        'pc_number' => $row['pc_number'],
        'status' => $row['status']
    ];
}

// Create a list of 30 PCs, use DB values if they exist, otherwise set default
$pcs = [];
for ($i = 1; $i <= 30; $i++) {
    if (isset($existingPcs[$i])) {
        $pcs[] = $existingPcs[$i];
    } else {
        $pcs[] = [
            'id' => null, // No ID because not in DB
            'pc_number' => $i,
            'status' => 'available'
        ];
    }
}

echo json_encode($pcs);
?>
