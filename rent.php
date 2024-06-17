<?php
// rent.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'database.php'; // Include database connection
include 'functions.php'; // Include functions file

// Function to set session alerts
function setAlert($message, $type = 'success') {
    $_SESSION['alert'] = ['message' => $message, 'type' => $type];
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if a valid video ID is passed and renting has not yet been confirmed
if (isset($_GET['id']) && !isset($_GET['confirm'])) {
    $videoId = htmlspecialchars($_GET['id']);
    $video = getVideoById($videoId, $user_id); // Retrieve video details

    if ($video) {
        // Display confirmation form
?>
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Rent Video</h3>
            </div>
            <div class="card-body">
                <h5><strong>You are about to rent the following video.</strong></h5>
                <p></p>
                <p><strong>Title:</strong> <?= htmlspecialchars($video['title']) ?></p>
                <p><strong>Director:</strong> <?= htmlspecialchars($video['director']) ?></p>
                <p><strong>Release Year:</strong> <?= htmlspecialchars($video['release_year']) ?></p>
                <form id="rentForm" action="rent.php?confirm=yes&id=<?= $videoId; ?>" method="post" onsubmit="return confirmRent()">
                    <div class="form-group">
                        <label for="days">Number of Days:</label>
                        <input type="number" class="form-control" name="days" id="days" required oninput="updateTotalCost()">
                    </div>
                    <p><strong>Total Cost: $<span id="totalCost">0</span></strong></p>
                    <button type="submit" name="rent" class="btn btn-success">Rent Video</button>
                    <a href="index.php?page=view" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>

        <script>
            const dailyRate = 10;
            function updateTotalCost() {
                const days = document.getElementById('days').value;
                const totalCost = days * dailyRate;
                document.getElementById('totalCost').textContent = totalCost.toFixed(2);
            }

            function confirmRent() {
                const days = document.getElementById('days').value;
                const totalCost = days * dailyRate;
                return confirm(`You will be charged $${totalCost.toFixed(2)} for renting this video for ${days} days. Do you want to proceed?`);
            }
        </script>
<?php
    } else {
        setAlert("Video not found.", "danger");
        header('Location: index.php?page=view');
        exit();
    }
} elseif (isset($_GET['confirm']) && $_GET['confirm'] == 'yes' && isset($_GET['id'])) {
    // Confirm renting
    $videoId = htmlspecialchars($_GET['id']);
    $days = htmlspecialchars($_POST['days']);
    $rented = rentVideo($videoId, $user_id, $days); // Rent video by ID

    if ($rented) {
        setAlert('Video rented successfully for ' . $days . ' days.', 'success');
    } else {
        setAlert('Failed to rent video.', 'danger');
    }
    header('Location: index.php?page=view_rentals'); // Redirect to the rented videos page
    exit();
} else {
    // No ID was provided
    setAlert('No video ID specified.', 'danger');
    header('Location: index.php?page=view');
    exit();
}
?>
