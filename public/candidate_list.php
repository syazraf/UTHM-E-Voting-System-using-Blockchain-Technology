<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}
include '../includes/header.php';
include '../includes/db.php';
include '../includes/functions.php';

$connection = db_connect();

$candidates = $connection->query("SELECT * FROM candidates WHERE status = 'verified'");
if (!$candidates) {
    die("Query failed: " . $connection->error);
}
?>

<div class="container">
    <h2>Approved Candidates</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Matric Number</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Manifesto</th>
                <th>Event</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($candidates->num_rows > 0) {
                while ($candidate = $candidates->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $candidate['id']; ?></td>
                    <td><?php echo $candidate['matric_number']; ?></td>
                    <td><?php echo $candidate['name']; ?></td>
                    <td><?php echo $candidate['email']; ?></td>
                    <td><?php echo $candidate['phone_number']; ?></td>
                    <td><?php echo $candidate['manifesto']; ?></td>
                    <td>
                        <?php
                        $event_id = $candidate['event_id'];
                        $event_query = $connection->query("SELECT name FROM events WHERE id = $event_id");
                        if ($event_query) {
                            $event = $event_query->fetch_assoc();
                            echo $event['name'];
                        } else {
                            echo "Event not found";
                        }
                        ?>
                    </td>
                </tr>
                <?php endwhile;
            } else {
                echo "<tr><td colspan='7'>No approved candidates found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
