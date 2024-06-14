<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'candidate') {
    header("Location: index.php");
    exit();
}
include '../includes/header.php';
include '../includes/db.php';
include '../includes/functions.php';

$username = $_SESSION['username'];
$connection = db_connect();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $manifesto = validate_input($_POST['manifesto']);
    $stmt = $connection->prepare("UPDATE candidates SET manifesto = ? WHERE username = ?");
    $stmt->bind_param("ss", $manifesto, $username);
    if ($stmt->execute()) {
        $success = "Manifesto updated successfully.";
    } else {
        $errors[] = "Error: " . $stmt->error;
    }
    $stmt->close();
}

$stmt = $connection->prepare("SELECT manifesto FROM candidates WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$connection->close();
?>

<div class="form-container">
    <h2>Update Manifesto</h2>
    <form method="post">
        <div class="form-group">
            <label for="manifesto">Manifesto:</label>
            <textarea class="form-control" id="manifesto" name="manifesto" rows="5" required><?php echo $user['manifesto']; ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
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
