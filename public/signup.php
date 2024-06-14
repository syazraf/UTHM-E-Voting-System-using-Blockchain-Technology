<?php
include '../includes/header.php';
include '../includes/db.php';
include '../includes/functions.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $matric_number = validate_input($_POST['matric_number']);
    $name = validate_input($_POST['name']);
    $email = validate_input($_POST['email']);
    $password = validate_input($_POST['password']);
    $confirm_password = validate_input($_POST['confirm_password']);
    $gender = validate_input($_POST['gender']);
    $phone_number = validate_input($_POST['phone_number']);
    $event_id = isset($_POST['event_id']) ? validate_input($_POST['event_id']) : null;

    $errors = [];

    if (!validate_matric_number($matric_number)) {
        $errors[] = "Invalid matric number format.";
    }
    if (!validate_email($email)) {
        $errors[] = "Invalid email format.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    if (!validate_phone_number($phone_number)) {
        $errors[] = "Invalid phone number format.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $connection = db_connect();
        $table = $role == 'candidate' ? 'candidates' : 'students';
        
        if ($role == 'candidate') {
            $stmt = $connection->prepare("INSERT INTO $table (matric_number, name, email, password, gender, phone_number, event_id, status, verification_code) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?)");
            $verification_code = rand(100000, 999999);
            $stmt->bind_param("ssssssii", $matric_number, $name, $email, $hashed_password, $gender, $phone_number, $event_id, $verification_code);
        } else {
            $stmt = $connection->prepare("INSERT INTO $table (matric_number, name, email, password, gender, phone_number, status, verification_code) VALUES (?, ?, ?, ?, ?, ?, 'pending', ?)");
            $verification_code = rand(100000, 999999);
            $stmt->bind_param("ssssssi", $matric_number, $name, $email, $hashed_password, $gender, $phone_number, $verification_code);
        }
        
        if ($stmt->execute()) {
            send_email_verification($email, $verification_code, $connection);
            header("Location: verify.php?role=$role&phone_number=$phone_number");
            exit();
        } else {
            $errors[] = "Error: " . $stmt->error;
            error_log("Error executing statement: " . $stmt->error);
        }

        $stmt->close();
        $connection->close();
    }
}

$connection = db_connect();
$events = $connection->query("SELECT * FROM events");
?>

<div class="form-container">
    <h2>Signup</h2>
    <form method="post">
        <input type="hidden" name="role" value="<?php echo $_GET['role']; ?>">
        <div class="form-group">
            <label for="matric_number">Matric Number:</label>
            <input type="text" class="form-control" id="matric_number" name="matric_number" placeholder="ai210156" required>
        </div>
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="email@example.com" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <div class="form-group">
            <label for="gender">Gender:</label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>
        <div class="form-group">
            <label for="phone_number">Phone Number:</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="012-3334444" required>
        </div>
        <?php if ($_GET['role'] == 'candidate'): ?>
            <div class="form-group">
                <label for="event_id">Event:</label>
                <select class="form-control" id="event_id" name="event_id" required>
                    <?php while ($event = $events->fetch_assoc()): ?>
                        <option value="<?php echo $event['id']; ?>"><?php echo $event['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Signup</button>
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
