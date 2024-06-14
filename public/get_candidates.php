<?php
include '../includes/db.php';

$event_id = $_GET['event_id'];
$connection = db_connect();

$candidates = $connection->query("SELECT id, name FROM candidates WHERE event_id = $event_id AND status = 'verified'");

$candidate_list = [];
while ($candidate = $candidates->fetch_assoc()) {
    $candidate_list[] = $candidate;
}

echo json_encode($candidate_list);
?>
