<?php
include '../config/config.php';

function hashMatricNumber($matricNumber) {
    return hash('sha256', $matricNumber);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $voterId = $_POST['voter_id'];
    $candidateId = $_POST['candidate_id'];
    $eventId = $_POST['event_id'];
    $voterIdHash = hashMatricNumber($voterId);

    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM votes WHERE voter_id_hash = ? AND event_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $voterIdHash, $eventId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "You have already voted.";
    } else {
        $sql = "INSERT INTO votes (voter_id_hash, candidate_id, event_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $voterIdHash, $candidateId, $eventId);
        $stmt->execute();
        echo "Vote recorded successfully.";


        echo "<script>
            if (typeof window.ethereum !== 'undefined') {
                ethereum.request({ method: 'eth_requestAccounts' }).then(accounts => {
                    const account = accounts[0];
                    const web3 = new Web3(ethereum);
                    const contractAddress = 5777
                    const contractABI = [];
                    const contract = new web3.eth.Contract(contractABI, contractAddress);
                    
                    contract.methods.recordVote('$voterIdHash', '$candidateId', '$eventId').send({ from: account })
                    .on('transactionHash', function(hash) {
                        console.log('Transaction Hash:', hash);
                    })
                    .on('receipt', function(receipt) {
                        console.log('Receipt:', receipt);
                    })
                    .on('error', function(error) {
                        console.error('Error:', error);
                    });
                });
            }
        </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.jsdelivr.net/gh/ethereum/web3.js/dist/web3.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>Cast Your Vote</h1>
        <form action="vote.php" method="POST" id="voteForm">
            <div class="form-group">
                <label for="voter_id">Voter ID</label>
                <input type="text" class="form-control" id="voter_id" name="voter_id" required>
            </div>
            <div class="form-group">
                <label for="event_id">Event</label>
                <select class="form-control" id="event_id" name="event_id" onchange="fetchCandidates(this.value)" required>
                    <option value="">Select Event</option>
                    <!-- Add event options dynamically from the database -->
                </select>
            </div>
            <div class="form-group" id="candidate_list">
                <!-- Candidate list will be populated dynamically -->
            </div>
            <button type="submit" class="btn btn-primary">Vote</button>
        </form>
    </div>

    <script>
        function fetchCandidates(eventId) {
            if (eventId === "") {
                document.getElementById("candidate_list").innerHTML = '';
                return;
            }
            fetch('fetch_candidates.php?event_id=' + eventId)
                .then(response => response.json())
                .then(data => {
                    let candidateList = document.getElementById("candidate_list");
                    candidateList.innerHTML = '';
                    data.forEach(candidate => {
                        let card = document.createElement("div");
                        card.className = "card m-2";
                        card.style.width = "18rem";
                        card.innerHTML = `
                            <img src="${candidate.profile_picture}" class="card-img-top" alt="${candidate.name}" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">${candidate.name}</h5>
                                <p class="card-text"><strong>Gender:</strong> ${candidate.gender}</p>
                                <p class="card-text"><strong>Event:</strong> ${candidate.event_name}</p>
                                <p class="card-text"><strong>Manifesto:</strong> ${candidate.manifesto}</p>
                                <input type="radio" name="candidate_id" value="${candidate.id}" required>
                            </div>
                        `;
                        candidateList.appendChild(card);
                    });
                })
                .catch(error => console.error('Error fetching candidates:', error));
        }
    </script>
</body>
</html>
