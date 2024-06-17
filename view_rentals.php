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
$rentedVideos = getRentedVideos($user_id);

// Process form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['return_single'])) {
        $video_id = $_POST['video_id'];
        if (returnVideo($video_id, $user_id)) {
            // Remove the returned video from the $rentedVideos array
            foreach ($rentedVideos as $key => $video) {
                if ($video['video_id'] == $video_id) {
                    unset($rentedVideos[$key]);
                    break;
                }
            }
        }
    } elseif (isset($_POST['return_all'])) {
        if (returnAllVideos($user_id)) {
            // Clear the $rentedVideos array since all videos are returned
            $rentedVideos = [];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rented Videos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Rented Videos</h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Director</th>
                        <th>Release Year</th>
                        <th>Rent Date</th>
                        <th>Days Rented</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($rentedVideos)): ?>
                        <?php foreach ($rentedVideos as $video): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($video['title']); ?></td>
                                <td><?php echo htmlspecialchars($video['director']); ?></td>
                                <td><?php echo htmlspecialchars($video['release_year']); ?></td>
                                <td><?php echo htmlspecialchars($video['rent_date']); ?></td>
                                <td><?php echo htmlspecialchars($video['days']); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="video_id" value="<?php echo $video['video_id']; ?>">
                                        <button type="submit" class="btn btn-success" name="return_single">Return</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No rented videos found</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                <?php if (!empty($rentedVideos)): ?>
                    <button type="submit" class="btn btn-success" name="return_all">Return all Videos</button>
                <?php endif; ?>
            </form>
        </div>
    </div>

</body>
</html>
