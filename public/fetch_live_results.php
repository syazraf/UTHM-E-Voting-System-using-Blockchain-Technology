<?php
include '../includes/db.php';
include '../includes/functions.php';

$connection = db_connect();

$events = $connection->query("SELECT * FROM events");
$results = [];

while ($event = $events->fetch_assoc()) {
    $event_id = $event['id'];
    $event_name = $event['name'];

    $stmt = $connection->prepare("
        SELECT candidates.name, COUNT(votes.id) as vote_count 
        FROM votes 
        JOIN candidates ON votes.candidate_id = candidates.id 
        WHERE candidates.event_id = ? 
        GROUP BY candidates.name
    ");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $candidates = [];
    while ($row = $result->fetch_assoc()) {
        $candidates[] = $row;
    }

    $results[] = [
        'event_name' => $event_name,
        'candidates' => $candidates
    ];

    $stmt->close();
}

$connection->close();

header('Content-Type: application/json');
echo json_encode($results);
?>


