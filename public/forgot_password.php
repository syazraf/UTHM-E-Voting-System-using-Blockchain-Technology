<?php
include '../includes/header.php';
include '../includes/db.php';
include '../includes/functions.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $username = validate_input($_POST['username']);
    $phone_number = validate_input($_POST['phone_number']);

    $connection = db_connect();

    $table = $role == 'admin' ? 'admins' : ($role == 'candidate' ? 'candidates' : 'students');
    $identifier = $role == 'admin' ? 'username' : 'matric_number';

    $stmt = $connection->prepare("SELECT * FROM $table WHERE $identifier = ? AND phone_number = ?");
    $stmt->bind_param("ss", $username, $phone_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        $verification_code = rand(100000, 999999);
        send_sms_verification($phone_number, $verification_code, $connection);

        $stmt = $connection->prepare("UPDATE $table SET verification_code = ? WHERE id = ?");
        $stmt->bind_param("si", $verification_code, $user['id']);
        $stmt->execute();
        $stmt->close();

        $_SESSION['role'] = $role;
        $_SESSION['username'] = $username;
        header("Location: reset_password.php?role=$role");
        exit();
    } else {
        $error = "No matching user found.";
    }

    $connection->close();
}
?>

<div class="form-container">
    <h2>Forgot Password</h2>
    <form method="post">
        <input type="hidden" name="role" value="<?php echo $_GET['role']; ?>">
        <div class="form-group">
            <label for="username">Username / Matric Number:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="phone_number">Phone Number:</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="012-3456789" required>
        </div>
        <button type="submit" class="btn btn-primary">Send Verification Code</button>
    </form>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
