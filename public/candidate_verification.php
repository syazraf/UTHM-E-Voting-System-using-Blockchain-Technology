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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $candidate_id = $_POST['candidate_id'];
    $action = $_POST['action'];
    
    if ($action == 'approve') {
        $stmt = $connection->prepare("UPDATE candidates SET status = 'approved' WHERE id = ?");
        $stmt->bind_param("i", $candidate_id);
        if ($stmt->execute()) {
            echo "Candidate approved successfully";
        } else {
            echo "Error approving candidate: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $stmt = $connection->prepare("DELETE FROM candidates WHERE id = ?");
        $stmt->bind_param("i", $candidate_id);
        if ($stmt->execute()) {
            echo "Candidate rejected successfully";
        } else {
            echo "Error rejecting candidate: " . $stmt->error;
        }
        $stmt->close();
    }
}

$candidates = $connection->query("SELECT * FROM candidates WHERE status = 'pending'");

?>

<div class="container">
    <h2>Review Candidates</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Manifesto</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($candidate = $candidates->fetch_assoc()): ?>
            <tr>
                <td><?php echo $candidate['id']; ?></td>
                <td><?php echo $candidate['name']; ?></td>
                <td><?php echo $candidate['manifesto']; ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="candidate_id" value="<?php echo $candidate['id']; ?>">
                        <button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
                        <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
