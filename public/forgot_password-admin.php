<?php
include '../includes/header.php';
include '../includes/db.php';
include '../includes/functions.php';

session_start();

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = validate_input($_POST['username']);

    $connection = db_connect();

    $stmt = $connection->prepare("SELECT phone_number FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($phone_number);
    $stmt->fetch();
    $stmt->close();

    if ($phone_number) {
        $verification_code = rand(100000, 999999);
        send_sms_verification($phone_number, $verification_code, $connection);
        $_SESSION['verification_code'] = $verification_code;
        $_SESSION['reset_username'] = $username;
        header("Location: reset_password.php?role=admin");
        exit();
    } else {
        $errors[] = "Username not found.";
    }
}
?>

<div class="form-container">
    <h2>Forgot Password</h2>
    <form method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger mt-3">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
