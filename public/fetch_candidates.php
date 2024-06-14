<?php
include '../includes/db.php';
include '../includes/functions.php';

$event_id = validate_input($_GET['event_id']);
$connection = db_connect();

$stmt = $connection->prepare("SELECT id, name, gender, event_name, manifesto, profile_picture FROM candidates WHERE event_id = ? AND status = 'verified'");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

$candidates = [];
while ($candidate = $result->fetch_assoc()) {
    $candidates[] = $candidate;
}

$stmt->close();
$connection->close();

header('Content-Type: application/json');
echo json_encode($candidates);
?>


