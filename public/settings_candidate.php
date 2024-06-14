<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'candidate') {
    header("Location: index.php");
    exit();
}

include '../includes/header.php';
include '../includes/db.php';
include '../includes/functions.php';

$connection = db_connect();
$matric_number = $_SESSION['username'];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = validate_input($_POST['name']);
    $email = validate_input($_POST['email']);
    $phone_number = validate_input($_POST['phone_number']);
    $manifesto = validate_input($_POST['manifesto']);
    $new_password = validate_input($_POST['password']);
    $confirm_password = validate_input($_POST['confirm_password']);
    $profile_picture = '';

    if ($new_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (!empty($_FILES['profile_picture']['name'])) {
        $profile_picture = upload_profile_picture($_FILES['profile_picture'], '../uploads/');
        if (strpos($profile_picture, 'Sorry') !== false) {
            $errors[] = $profile_picture;
        }
    }

    if (empty($errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $connection->prepare("UPDATE candidates SET name = ?, email = ?, phone_number = ?, manifesto = ?, profile_picture = ?, password = ? WHERE matric_number = ?");
        $stmt->bind_param("sssssss", $name, $email, $phone_number, $manifesto, $profile_picture, $hashed_password, $matric_number);

        if ($stmt->execute()) {
            $success = "Profile updated successfully.";
        } else {
            $errors[] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$candidate = $connection->query("SELECT * FROM candidates WHERE matric_number = '$matric_number'")->fetch_assoc();
?>

<div class="form-container">
    <h2>Candidate Settings</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $candidate['name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $candidate['email']; ?>" required>
        </div>
        <div class="form-group">
            <label for="phone_number">Phone Number:</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo $candidate['phone_number']; ?>" required>
        </div>
        <div class="form-group">
            <label for="manifesto">Manifesto:</label>
            <textarea class="form-control" id="manifesto" name="manifesto" required><?php echo $candidate['manifesto']; ?></textarea>
        </div>
        <div class="form-group">
            <label for="profile_picture">Profile Picture:</label>
            <input type="file" class="form-control" id="profile_picture" name="profile_picture">
        </div>
        <div class="form-group">
            <label for="password">New Password:</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
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
