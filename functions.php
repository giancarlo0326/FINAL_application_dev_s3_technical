<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'database.php'; // Ensure this line is already included

// Add video function
if (!function_exists('addVideo')) {
    function addVideo($title, $director, $release_year, $user_id) {
        global $conn;
        $sql = "INSERT INTO videos (title, director, release_year, user_id) VALUES (?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssi", $title, $director, $release_year, $user_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Get videos function
if (!function_exists('getVideos')) {
    function getVideos($user_id) {
        global $conn;
        $sql = "SELECT id, title, director, release_year FROM videos WHERE user_id = ?";
        $videos = [];

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $videos[] = $row;
            }

            $stmt->close();
        }

        return $videos;
    }
}

// Get a single video by ID and user ID
if (!function_exists('getVideoById')) {
    function getVideoById($id, $user_id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM videos WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $video = $result->fetch_assoc();
        $stmt->close();
        return $video;
    }
}

// Update a video function
if (!function_exists('editVideo')) {
    function editVideo($id, $title, $director, $release_year, $user_id) {
        global $conn;
        $stmt = $conn->prepare("UPDATE videos SET title = ?, director = ?, release_year = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssiii", $title, $director, $release_year, $id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Delete a video function
if (!function_exists('deleteVideo')) {
    function deleteVideo($id) {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM videos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Rent video function
if (!function_exists('rentVideo')) {
    function rentVideo($video_id, $user_id, $days) {
        global $conn;
        $sql = "INSERT INTO rentals (video_id, user_id, days) VALUES (?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("iii", $video_id, $user_id, $days);
            $stmt->execute();
            $stmt->close();
            return true; // Return true on success
        } else {
            return false; // Return false on failure
        }
    }
}

// Get rented videos function
if (!function_exists('getRentedVideos')) {
    function getRentedVideos($user_id) {
        global $conn;
        $sql = "SELECT v.id as video_id, v.title, v.director, v.release_year, r.rent_date, r.return_date, r.days
                FROM rentals r
                INNER JOIN videos v ON r.video_id = v.id
                WHERE r.user_id = ?";
        $rentedVideos = [];

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $rentedVideos[] = $row;
            }

            $stmt->close();
        }

        return $rentedVideos;
    }
}

// Return a single video function
if (!function_exists('returnVideo')) {
    function returnVideo($video_id, $user_id) {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM rentals WHERE video_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $video_id, $user_id);
        $stmt->execute();
        $stmt->close();
        return true; // Return true on success
    }
}

// Return all rented videos function
if (!function_exists('returnAllVideos')) {
    function returnAllVideos($user_id) {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM rentals WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        return true; // Return true on success
    }
}
?>
