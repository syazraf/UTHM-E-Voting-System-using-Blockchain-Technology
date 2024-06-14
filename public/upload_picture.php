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
    $target_dir = "../uploads/";
    $upload_message = upload_profile_picture($_FILES["profile_picture"], $target_dir);
    if (strpos($upload_message, 'has been uploaded') !== false) {
        $profile_picture = basename($_FILES["profile_picture"]["name"]);
        $stmt = $connection->prepare("UPDATE students SET profile_picture = ? WHERE matric_number = ?");
        $stmt->bind_param("ss", $profile_picture, $username);
        if ($stmt->execute()) {
            $success = "Profile picture uploaded successfully.";
        } else {
            $errors[] = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errors[] = $upload_message;
    }
}
?>

<div class="form-container">
    <h2>Upload Profile Picture</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="profile_picture">Profile Picture:</label>
            <input type="file" class="form-control" id="profile_picture" name="profile_picture" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
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
