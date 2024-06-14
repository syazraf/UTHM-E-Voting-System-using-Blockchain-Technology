<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'candidate') {
    header("Location: index.php");
    exit();
}
include '../includes/header.php';
include '../includes/db.php';

$connection = db_connect();

// Fetch totals
$candidate_count = $connection->query("SELECT COUNT(*) as count FROM candidates WHERE status = 'verified'")->fetch_assoc()['count'];
$event_count = $connection->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
?>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 sidebar">
            <div class="sidebar-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" onclick="loadPage('candidate_dashboard_content.php')">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="loadPage('view_candidates.php')">View Candidates</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="loadPage('settings_candidate.php')">Settings</a>
                    </li>
                </ul>
            </div>
        </nav>

        <main role="main" class="col-md-10 ml-sm-auto col-lg-10 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Candidate Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a class="btn btn-sm btn-outline-secondary" href="logout.php">Logout</a>
                </div>
            </div>
            <div id="content">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-header">Total Verified Candidates</div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $candidate_count; ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-header">Total Events</div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $event_count; ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="charts-container"></div>
            </div>
        </main>
    </div>
</div>

<script>
    function loadPage(page) {
        const contentDiv = document.getElementById('content');
        fetch(page)
            .then(response => response.text())
            .then(html => {
                contentDiv.innerHTML = html;
                if (page === 'candidate_dashboard_content.php') {
                    fetchLiveResults();
                    setInterval(fetchLiveResults, 120000); // Refresh every 2 minutes
                }
            })
            .catch(error => console.warn('Error loading page:', error));
        event.preventDefault();
    }

    function fetchLiveResults() {
        fetch('fetch_live_results.php')
            .then(response => response.json())
            .then(data => {
                const chartsContainer = document.getElementById('charts-container');
                chartsContainer.innerHTML = ''; // Clear previous charts

                data.forEach(event => {
                    const chartId = `chart-${event.event_name.replace(/\s+/g, '-')}`;
                    const chartContainer = document.createElement('div');
                    chartContainer.innerHTML = `
                        <h3>${event.event_name}</h3>
                        <canvas id="${chartId}" width="400" height="400"></canvas>
                    `;
                    chartsContainer.appendChild(chartContainer);

                    const ctx = document.getElementById(chartId).getContext('2d');
                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: event.candidates.map(candidate => candidate.name),
                            datasets: [{
                                data: event.candidates.map(candidate => candidate.vote_count),
                                backgroundColor: event.candidates.map(() => `hsl(${Math.random() * 360}, 100%, 75%)`)
                            }]
                        }
                    });
                });
            });
    }

    fetchLiveResults(); // Initial fetch
    setInterval(fetchLiveResults, 120000); // Refresh every 2 minutes
</script>

<?php include '../includes/footer.php'; ?>
