<?php
include '../includes/header.php';
include '../includes/db.php';
include '../includes/functions.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $username = $_SESSION['username'];
    $verification_code = validate_input($_POST['verification_code']);
    $new_password = validate_input($_POST['new_password']);
    $confirm_password = validate_input($_POST['confirm_password']);

    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $connection = db_connect();
        $table = $role == 'admin' ? 'admins' : ($role == 'candidate' ? 'candidates' : 'students');
        $identifier = $role == 'admin' ? 'username' : 'matric_number';

        $stmt = $connection->prepare("SELECT * FROM $table WHERE $identifier = ? AND verification_code = ?");
        $stmt->bind_param("ss", $username, $verification_code);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $connection->prepare("UPDATE $table SET password = ?, verification_code = NULL WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $user['id']);
            $stmt->execute();
            $stmt->close();

            $success = "Password has been reset successfully.";
        } else {
            $error = "Invalid verification code.";
        }

        $connection->close();
    }
}
?>

<div class="form-container">
    <h2>Reset Password</h2>
    <form method="post">
        <input type="hidden" name="role" value="<?php echo $_GET['role']; ?>">
        <div class="form-group">
            <label for="verification_code">Verification Code:</label>
            <input type="text" class="form-control" id="verification_code" name="verification_code" required>
        </div>
        <div class="form-group">
            <label for="new_password">New Password:</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Reset Password</button>
    </form>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div class="alert alert-success mt-3"><?php echo $success; ?></div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
