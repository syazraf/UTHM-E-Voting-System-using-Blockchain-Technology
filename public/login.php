<?php
include '../includes/header.php';
include '../includes/db.php';
include '../includes/functions.php';

session_start();

$max_attempts = 3;
$lockout_time = 20;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $username = validate_input($_POST['username']);
    $password = validate_input($_POST['password']);
    $verification_code = isset($_POST['verification_code']) ? validate_input($_POST['verification_code']) : '';

    $allowed_roles = ['admin', 'candidate', 'student'];
    if (!in_array($role, $allowed_roles)) {
        die("Invalid role specified");
    }
    $connection = db_connect();

    if ($role == 'admin') {
        $stmt = $connection->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
    } else {
        $table = $role == 'candidate' ? 'candidates' : 'students';
        $stmt = $connection->prepare("SELECT * FROM $table WHERE matric_number = ?");
        $stmt->bind_param("s", $username);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        $current_time = time();
        $lockout_expires = strtotime($user['last_login_attempt']) + $lockout_time;

        if ($user['login_attempts'] >= $max_attempts && $current_time < $lockout_expires) {
            $error = "Too many failed attempts. Try again after " . ($lockout_expires - $current_time) . " seconds.";
        } else {
            if (password_verify($password, $user['password'])) {
                $stmt = $connection->prepare("UPDATE $table SET login_attempts = 0, last_login_attempt = NULL WHERE id = ?");
                $stmt->bind_param("i", $user['id']);
                $stmt->execute();
                $stmt->close();

                $_SESSION['role'] = $role;
                $_SESSION['username'] = $username;
                $_SESSION['student_id'] = $role == 'student' ? $user['id'] : null;

                if ($role == 'admin') {
                    header("Location: admin_dashboard.php");
                    exit();
                } elseif ($role == 'candidate' && $user['status'] == 'pending') {
                    if ($verification_code == $user['verification_code']) {
                        $stmt = $connection->prepare("UPDATE candidates SET status = 'verified' WHERE id = ?");
                        $stmt->bind_param("i", $user['id']);
                        $stmt->execute();
                        $stmt->close();

                        header("Location: candidate_dashboard.php");
                        exit();
                    } else {
                        $error = "Invalid verification code.";
                    }
                } elseif ($user['status'] == 'verified') {
                    header("Location: {$role}_dashboard.php");
                    exit();
                } else {
                    $error = "Account not verified.";
                }
            } else {
                $stmt = $connection->prepare("UPDATE $table SET login_attempts = login_attempts + 1, last_login_attempt = NOW() WHERE id = ?");
                $stmt->bind_param("i", $user['id']);
                $stmt->execute();
                $stmt->close();

                $error = "Invalid credentials.";
            }
        }
    } else {
        $error = "Invalid credentials.";
    }

    $connection->close();
}
?>

<div class="form-container">
    <h2>Login</h2>
    <form method="post">
        <input type="hidden" name="role" value="<?php echo $_GET['role']; ?>">
        <?php if ($_GET['role'] == 'admin'): ?>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
        <?php else: ?>
            <div class="form-group">
                <label for="username">Matric Number:</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="ai210156" required>
            </div>
        <?php endif; ?>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <?php if ($_GET['role'] == 'candidate'): ?>
            <div class="form-group">
                <label for="verification_code">Verification Code (if pending):</label>
                <input type="text" class="form-control" id="verification_code" name="verification_code">
            </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($_GET['role'] != 'admin') : ?>
        <p>Don't have an account? <a href="signup.php?role=<?php echo $_GET['role']; ?>">Sign up</a></p>
        <p>Forgot your password? <a href="forgot_password.php?role=<?php echo $_GET['role']; ?>">Reset Password</a></p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
