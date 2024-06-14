<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}
include '../includes/db.php';
include '../includes/functions.php';

if (isset($_GET['id'])) {
    $event_id = validate_input($_GET['id']);
    $connection = db_connect();

    $stmt = $connection->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);

    if ($stmt->execute()) {
        header("Location: event_list.php");
        exit();
    } else {
        echo "Error deleting event: " . $stmt->error;
    }

    $stmt->close();
    $connection->close();
} else {
    header("Location: event_list.php");
    exit();
}
?>
