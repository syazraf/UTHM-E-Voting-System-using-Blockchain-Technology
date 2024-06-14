<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}
include '../includes/header.php';
include '../includes/db.php';
include '../includes/functions.php';

$connection = db_connect();
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = validate_input($_POST['event_name']);
    $event_date = validate_input($_POST['event_date']);

    echo "Event Name: " . $event_name . "<br>";
    echo "Event Date: " . $event_date . "<br>";

    if (empty($event_name) || empty($event_date)) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        $stmt = $connection->prepare("INSERT INTO events (name, date) VALUES (?, ?)");
        if ($stmt === false) {
            $errors[] = "Error preparing statement: " . $connection->error;
        } else {
            $stmt->bind_param("ss", $event_name, $event_date);
            if ($stmt->execute()) {
                $success = "Event created successfully.";
            } else {
                $errors[] = "Error executing statement: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<div class="form-container">
    <h2>Create an Event</h2>
    <form method="post">
        <div class="form-group">
            <label for="event_name">Event Name:</label>
            <input type="text" class="form-control" id="event_name" name="event_name" required>
        </div>
        <div class="form-group">
            <label for="event_date">Event Date:</label>
            <input type="date" class="form-control" id="event_date" name="event_date" required>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger mt-3">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div class="alert alert-success mt-3"><?php echo $success; ?></div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
