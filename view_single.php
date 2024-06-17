<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include 'database.php';
include 'functions.php'; // Ensure functions.php is included where getVideoById() is defined

if (isset($_GET['id'])) {
    $video_id = $_GET['id'];
    $user_id = $_SESSION['user_id']; // Assuming $_SESSION['user_id'] is set correctly

    $video = getVideoById($video_id, $user_id); // Pass both $video_id and $user_id
    if ($video !== null) {
?>
<div class="card-primary">
    <div class="card-header">
        <h3 class="card-title">Video Details</h3>
    </div>
    <div class="card-body">
    <h5><strong>You are viewing video details.</strong></h5>
    <p></p>
        <p><strong>Title:</strong> <?php echo htmlspecialchars($video['title']); ?></p>
        <p><strong>Director:</strong> <?php echo htmlspecialchars($video['director']); ?></p>
        <p><strong>Release Year:</strong> <?php echo htmlspecialchars($video['release_year']); ?></p>
    </div>
    <div class="card-footer">
        <button type="button" class="btn btn-primary" onclick="history.back();">Back</button>
    </div>
</div>
<?php
    } else {
        echo '<div class="alert alert-warning">Video not found.</div>';
    }
} else {
    echo '<div class="alert alert-danger">No video ID specified.</div>';
}
?>
