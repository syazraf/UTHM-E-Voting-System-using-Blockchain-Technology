<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

include '../includes/header.php';
include '../includes/db.php';
include '../includes/functions.php';

$connection = db_connect();
$username = $_SESSION['username'];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = validate_input($_POST['username']);
    $new_password = validate_input($_POST['password']);
    $confirm_password = validate_input($_POST['confirm_password']);

    if ($new_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $connection->prepare("UPDATE admins SET username = ?, password = ? WHERE username = ?");
        $stmt->bind_param("sss", $new_username, $hashed_password, $username);

        if ($stmt->execute()) {
            $_SESSION['username'] = $new_username;
            $success = "Profile updated successfully.";
        } else {
            $errors[] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$admin = $connection->query("SELECT * FROM admins WHERE username = '$username'")->fetch_assoc();
?>

<div class="form-container">
    <h2>Admin Settings</h2>
    <form method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo $admin['username']; ?>" required>
        </div>
        <div class="form-group">
            <label for="password">New Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
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
