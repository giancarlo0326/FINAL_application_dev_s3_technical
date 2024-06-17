<?php
// Ensure the session is started only once
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'database.php';
include 'functions.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$videos = getVideos($user_id);
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Videos</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Director</th>
                    <th>Release Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($videos)): ?>
                    <?php foreach ($videos as $video): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($video['title']); ?></td>
                            <td><?php echo htmlspecialchars($video['director']); ?></td>
                            <td><?php echo htmlspecialchars($video['release_year']); ?></td>
                            <td>
                                <a href="index.php?page=edit&id=<?php echo $video['id']; ?>" class="btn btn-warning">Edit</a>
                                <a href="index.php?page=delete&id=<?php echo $video['id']; ?>" class="btn btn-danger">Delete</a>
                                <a href="index.php?page=view_single&id=<?php echo $video['id']; ?>" class="btn btn-primary">View Details</a>
                                <a href="index.php?page=rent&id=<?php echo $video['id']; ?>" class="btn btn-success">Rent Video</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No videos found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
