<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
include '../includes/header.php';
include '../includes/db.php';
include '../includes/functions.php';

$connection = db_connect();

$query = "SELECT * FROM events";
$events = $connection->query($query);
?>

<div class="container">
    <h2>Event List</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Date</th>
                <th>Status</th>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                <th>Action</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($event = $events->fetch_assoc()): ?>
            <tr>
                <td><?php echo $event['id']; ?></td>
                <td><?php echo $event['name']; ?></td>
                <td><?php echo $event['date']; ?></td>
                <td><?php echo (new DateTime($event['date']) < new DateTime()) ? 'Ended' : 'Upcoming'; ?></td>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                <td>
                    <?php if (new DateTime($event['date']) < new DateTime()): ?>
                    <a href="event_destroy.php?id=<?php echo $event['id']; ?>" class="btn btn-danger">Delete</a>
                    <?php endif; ?>
                </td>
                <?php endif; ?>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
