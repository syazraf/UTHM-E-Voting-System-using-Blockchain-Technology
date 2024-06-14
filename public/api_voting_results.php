<?php
include '../includes/db.php';

function get_live_voting_results() {
    // Example logic to get live voting results from the database
    $results = [
        ['candidate' => 'Candidate 1', 'votes' => 120],
        ['candidate' => 'Candidate 2', 'votes' => 150],
        ['candidate' => 'Candidate 3', 'votes' => 90],
    ];
    return $results;
}

header('Content-Type: application/json');
echo json_encode(get_live_voting_results());
?>
