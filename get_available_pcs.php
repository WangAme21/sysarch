<?php
header('Content-Type: application/json');
include('db.php');

$labId = $_GET['lab'] ?? null;

if (!$labId) {
    http_response_code(400);
    echo json_encode(['error' => 'Lab ID is required']);
    exit;
}

// Sanity check: ensure column values are consistently lowercase in DB
$query = "SELECT * FROM pcs WHERE lab = ? AND status != 'in_use'";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $labId);
$stmt->execute();
$result = $stmt->get_result();

$availablePcs = [];
while ($row = $result->fetch_assoc()) { // ✅ correct method
    $availablePcs[] = $row;
}

// Optional: sort PCs by number if needed
usort($availablePcs, function($a, $b) {
    return (int)$a['pc_number'] - (int)$b['pc_number'];
});

echo json_encode($availablePcs);
?>
