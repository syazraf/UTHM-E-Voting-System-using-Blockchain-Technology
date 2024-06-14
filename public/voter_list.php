<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}
include '../includes/header.php';
include '../includes/db.php';

$connection = db_connect();
$voters = $connection->query("SELECT * FROM students");

?>

<div class="container">
    <h2>Review Voters</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($voter = $voters->fetch_assoc()): ?>
            <tr>
                <td><?php echo $voter['id']; ?></td>
                <td><?php echo $voter['name']; ?></td>
                <td><?php echo $voter['email']; ?></td>
                <td><?php echo $voter['phone_number']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
