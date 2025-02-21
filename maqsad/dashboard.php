<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaQsaD Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .welcome-text {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .nav-link {
            color: #333;
        }
        .nav-link:hover {
            color: #007bff;
        }
        .logout-btn {
            cursor: pointer;
        }
        #providerContent, #beneficiaryContent {
            display: none;
        }
        .action-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">MaQsaD</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="logoutBtn">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="profile-section">
            <h2 class="welcome-text">Welcome, <span id="displayName"><?php echo htmlspecialchars($_SESSION['display_name'] ?? $_SESSION['username']); ?></span>!</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="action-card">
                        <h3>Quick Actions</h3>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" id="editProfileBtn">Edit Profile</button>
                            <button class="btn btn-info" id="viewServicesBtn">View Services</button>
                            <button class="btn btn-secondary" id="messagesBtn">Messages</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Provider-specific content -->
        <div id="providerContent" style="display: <?php echo $_SESSION['role'] === 'provider' ? 'block' : 'none'; ?>">
            <div class="row">
                <div class="col-12">
                    <div class="action-card">
                        <h3>Your Services</h3>
                        <p>Manage your service offerings and view requests from beneficiaries.</p>
                        <div id="providerServices">Loading services...</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Beneficiary-specific content -->
        <div id="beneficiaryContent" style="display: <?php echo $_SESSION['role'] === 'beneficiary' ? 'block' : 'none'; ?>">
            <div class="row">
                <div class="col-12">
                    <div class="action-card">
                        <h3>Available Services</h3>
                        <p>Browse and request services from our trusted providers.</p>
                        <div id="availableServices">Loading services...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle logout
        document.getElementById('logoutBtn').addEventListener('click', function(e) {
            e.preventDefault();
            fetch('auth/logout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Always redirect to index page on logout
                window.location.href = 'index.html';
            })
            .catch(error => {
                console.error('Logout failed:', error);
                // Still redirect to index page even if there's an error
                window.location.href = 'index.html';
            });
        });

        // Quick action buttons
        document.getElementById('editProfileBtn').addEventListener('click', function(e) {
            e.preventDefault();
            // TODO: Implement edit profile functionality
            alert('Edit profile functionality coming soon!');
        });

        document.getElementById('viewServicesBtn').addEventListener('click', function(e) {
            e.preventDefault();
            // TODO: Implement view services functionality
            alert('View services functionality coming soon!');
        });

        document.getElementById('messagesBtn').addEventListener('click', function(e) {
            e.preventDefault();
            // TODO: Implement messages functionality
            alert('Messages functionality coming soon!');
        });
    </script>
</body>
</html>
