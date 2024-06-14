<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UTHM Vote System</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand mx-auto" href="#">
                <img src="../public/uthm_logo.png" alt="UTHM Logo" width="80" height="80">
                <span class="navbar-title">UTHM Vote System</span>
            </a>
        </div>
    </nav>

    <div class="container text-center mt-5">
        <h1>Welcome to UTHM Vote System</h1>
        <p>Select your role to proceed</p>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card role-card">
                    <div class="card-body">
                        <h5 class="card-title">Admin</h5>
                        <p class="card-text">Login as Admin to manage the voting system.</p>
                        <a href="../public/login.php?role=admin" class="btn btn-primary">Login as Admin</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card role-card">
                    <div class="card-body">
                        <h5 class="card-title">Candidate</h5>
                        <p class="card-text">Login or Sign up as Candidate to participate in the election.</p>
                        <a href="../public/login.php?role=candidate" class="btn btn-primary">Login as Candidate</a>
                        <a href="../public/signup.php?role=candidate" class="btn btn-secondary candidate-btn mt-2">Sign up as Candidate</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card role-card">
                    <div class="card-body">
                        <h5 class="card-title">Student</h5>
                        <p class="card-text">Login or Sign up as Student to vote.</p>
                        <a href="../public/login.php?role=student" class="btn btn-primary">Login as Student</a>
                        <a href="../public/signup.php?role=student" class="btn btn-secondary student-btn mt-2">Sign up as Student</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5">
        <p>UTHM Vote System &copy; 2024</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
