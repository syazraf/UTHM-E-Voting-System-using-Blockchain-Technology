<?php
session_start();
if (!isset($_SESSION['username']) || ($_SESSION['role'] != 'student' && $_SESSION['role'] != 'candidate')) {
    header("Location: index.php");
    exit();
}
include '../includes/header.php';
include '../includes/db.php';

$connection = db_connect();
$candidates = $connection->query("SELECT candidates.id, candidates.name, candidates.gender, events.name AS event_name, candidates.manifesto, candidates.profile_picture 
                                  FROM candidates 
                                  JOIN events ON candidates.event_id = events.id 
                                  WHERE candidates.status = 'verified'");
?>

<div class="container mt-5">
    <h2 class="mb-4">View Candidates</h2>
    <div class="row">
        <?php while ($candidate = $candidates->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="<?php echo $candidate['profile_picture']; ?>" class="card-img-top" alt="<?php echo $candidate['name']; ?>" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $candidate['name']; ?></h5>
                        <p class="card-text"><strong>Gender:</strong> <?php echo ucfirst($candidate['gender']); ?></p>
                        <p class="card-text"><strong>Event:</strong> <?php echo $candidate['event_name']; ?></p>
                        <p class="card-text"><strong>Manifesto:</strong> <?php echo $candidate['manifesto']; ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
