<?php
include '../includes/header.php';
include '../includes/db.php';
include '../includes/functions.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $phone_number = validate_input($_POST['phone_number']);
    $verification_code = validate_input($_POST['verification_code']);

    $connection = db_connect();
    $table = $role == 'candidate' ? 'candidates' : 'students';

    $stmt = $connection->prepare("SELECT * FROM $table WHERE phone_number = ? AND verification_code = ?");
    $stmt->bind_param("ss", $phone_number, $verification_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        $stmt = $connection->prepare("UPDATE $table SET status = 'verified' WHERE phone_number = ?");
        $stmt->bind_param("s", $phone_number);
        $stmt->execute();
        $stmt->close();

        $_SESSION['role'] = $role;
        $_SESSION['username'] = $user['matric_number'];
        header("Location: {$role}_dashboard.php");
        exit();
    } else {
        $error = "Invalid verification code.";
    }

    $connection->close();
}
?>

<div class="form-container">
    <h2>Verify Account</h2>
    <form method="post">
        <input type="hidden" name="role" value="<?php echo $_GET['role']; ?>">
        <input type="hidden" name="phone_number" value="<?php echo $_GET['phone_number']; ?>">
        <div class="form-group">
            <label for="verification_code">Verification Code:</label>
            <input type="text" class="form-control" id="verification_code" name="verification_code" required>
        </div>
        <button type="submit" class="btn btn-primary">Verify</button>
    </form>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
