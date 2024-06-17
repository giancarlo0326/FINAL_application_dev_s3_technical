<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'functions.php'; 

// Function to set session alerts
function setAlert($message, $type = 'success') {
    $_SESSION['alert'] = ['message' => $message, 'type' => $type];
}

// Check if a valid video ID is passed and deletion has not yet been confirmed
if (isset($_GET['id']) && !isset($_GET['confirm'])) {
    $videoId = htmlspecialchars($_GET['id']);
    $user_id = $_SESSION['user_id']; // Assuming you have user_id stored in session
    $video = getVideoById($videoId, $user_id); // Retrieve video details

    if ($video) {
        // Display confirmation form
?>

<div class="card card-danger">
    <div class="card-header">
        <h3 class="card-title">Delete Video</h3>
    </div>
    <div class="card-body">
        <h5><strong>Are you sure you want to delete the following video?</strong></h5>
        <p></p>
        <p><strong>Title:</strong> <?= htmlspecialchars($video['title']) ?></p>
        <p><strong>Director:</strong> <?= htmlspecialchars($video['director']) ?></p>
        <p><strong>Release Year:</strong> <?= htmlspecialchars($video['release_year']) ?></p>
    </div>
    <div class="card-footer">
        <a href="delete.php?confirm=yes&id=<?= $videoId; ?>" class="btn btn-danger">Delete</a>
        <a href="index.php?page=view" class="btn btn-secondary">Cancel</a>
    </div>
</div>

<?php
    } else {
        setAlert("Video not found.", "danger");
        header('Location: index.php?page=view');
        exit();
    }
} elseif (isset($_GET['confirm']) && $_GET['confirm'] == 'yes' && isset($_GET['id'])) {
    // Confirm deletion
    $videoId = htmlspecialchars($_GET['id']);
    $deleted = deleteVideo($videoId); // Delete video by ID

    if ($deleted) {
        setAlert('Video deleted successfully.', 'success');
    } else {
        setAlert('Failed to delete video. Video not found.', 'danger');
    }
    header('Location: index.php?page=view'); // Redirect to the video list page
    exit();
} else {
    // No ID was provided
    setAlert('No video ID specified.', 'danger');
    header('Location: index.php?page=view');
    exit();
}
?>
