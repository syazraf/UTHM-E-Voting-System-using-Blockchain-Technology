<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';

$data = json_decode(file_get_contents('php://input'), true);

$studentAddress = $data['studentAddress'];
$eventId = $data['eventId'];
$candidateId = $data['candidateId'];

$connection = db_connect();

// Check if the student has already voted in this event
$stmt = $connection->prepare("SELECT * FROM votes WHERE student_address = ? AND event_id = ?");
$stmt->bind_param("si", $studentAddress, $eventId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already voted in this event.']);
    exit();
}

// Insert the vote
$stmt = $connection->prepare("INSERT INTO votes (student_address, event_id, candidate_id) VALUES (?, ?, ?)");
$stmt->bind_param("sii", $studentAddress, $eventId, $candidateId);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error casting vote.']);
}
?>
