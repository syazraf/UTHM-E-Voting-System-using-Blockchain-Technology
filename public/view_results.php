<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit();
}
include '../includes/header.php';
include '../includes/db.php';

$connection = db_connect();
$results = $connection->query("SELECT candidates.name, COUNT(votes.candidate_id) as vote_count FROM candidates LEFT JOIN votes ON candidates.id = votes.candidate_id GROUP BY candidates.id");

?>

<div class="container">
    <h2>Election Results</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Candidate Name</th>
                <th>Votes</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($result = $results->fetch_assoc()): ?>
            <tr>
                <td><?php echo $result['name']; ?></td>
                <td><?php echo $result['vote_count']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
