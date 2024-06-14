<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}
include '../includes/header.php';
include '../includes/db.php';

$connection = db_connect();

$total_voters = $connection->query("SELECT COUNT(*) AS count FROM students")->fetch_assoc()['count'];
$total_candidates = $connection->query("SELECT COUNT(*) AS count FROM candidates")->fetch_assoc()['count'];
$total_events = $connection->query("SELECT COUNT(*) AS count FROM events")->fetch_assoc()['count'];
?>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 sidebar">
            <div class="sidebar-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" onclick="loadPage('admin_dashboard_content.php')">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="loadPage('candidate_verification.php')">Candidate Verification</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="loadPage('event_creation.php')">Event Creation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="loadPage('event_list.php')">Event List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="loadPage('candidate_list.php')">Candidate List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="loadPage('voter_list.php')">Voter List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="loadPage('settings_admin.php')">Settings</a>
                    </li>
                </ul>
            </div>
        </nav>

        <main role="main" class="col-md-10 ml-sm-auto col-lg-10 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Admin Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a class="btn btn-sm btn-outline-secondary" href="logout.php">Logout</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="dashboard-card green">
                        <h5>Total Voters</h5>
                        <p><?php echo $total_voters; ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dashboard-card yellow">
                        <h5>Total Candidates</h5>
                        <p><?php echo $total_candidates; ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dashboard-card purple">
                        <h5>Total Events</h5>
                        <p><?php echo $total_events; ?></p>
                    </div>
                </div>
            </div>
            <div id="content">
                <canvas id="voteChart" width="400" height="400"></canvas>
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
                if (page === 'admin_dashboard_content.php') {
                    fetchLiveResults();
                    setInterval(fetchLiveResults, 120000);
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
                chartsContainer.innerHTML = '';

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

    fetchLiveResults();
    setInterval(fetchLiveResults, 120000);
</script>

<?php include '../includes/footer.php'; ?>
