<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit();
}
include '../includes/header.php';
include '../includes/db.php';
include '../includes/functions.php';

$username = $_SESSION['username'];
$connection = db_connect();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = validate_input($_POST['email']);
    $phone_number = validate_input($_POST['phone_number']);
    $errors = [];

    if (!validate_email($email)) {
        $errors[] = "Invalid email format.";
    }
    if (!validate_phone_number($phone_number)) {
        $errors[] = "Invalid phone number format.";
    }

    if (empty($errors)) {
        $stmt = $connection->prepare("UPDATE students SET email = ?, phone_number = ? WHERE matric_number = ?");
        $stmt->bind_param("sss", $email, $phone_number, $username);
        if ($stmt->execute()) {
            $success = "Details updated successfully.";
        } else {
            $errors[] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$stmt = $connection->prepare("SELECT email, phone_number FROM students WHERE matric_number = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$connection->close();
?>

<div class="form-container">
    <h2>Update Personal Details</h2>
    <form method="post">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
        </div>
        <div class="form-group">
            <label for="phone_number">Phone Number:</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo $user['phone_number']; ?>" required>
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
