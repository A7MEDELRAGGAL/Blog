<?php
session_start(); // بدء الجلسة
include '../include/db.php';

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'You must be logged in to like or dislike.']);
    exit;
}

$user_id = $_SESSION['user_id'];

if(isset($_POST['action']) && isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];

    // التحقق من وجود تفاعل مسبق
    $check_query = "SELECT * FROM post_interactions WHERE post_id = $post_id AND user_id = $user_id";
    $check_result = mysqli_query($conn, $check_query);

    if(mysqli_num_rows($check_result) > 0) {
        echo json_encode(['error' => 'You have already liked or disliked this post.']);
        exit;
    }

    if($_POST['action'] == 'like') {
        $query = "UPDATE posts SET likes = likes + 1 WHERE postID = $post_id";
        $insert_query = "INSERT INTO post_interactions (post_id, user_id, interaction_type) VALUES ($post_id, $user_id, 'like')";
    } elseif($_POST['action'] == 'dislike') {
        $query = "UPDATE posts SET dislikes = dislikes + 1 WHERE postID = $post_id";
        $insert_query = "INSERT INTO post_interactions (post_id, user_id, interaction_type) VALUES ($post_id, $user_id, 'dislike')";
    }

    if(mysqli_query($conn, $query) && mysqli_query($conn, $insert_query)) {
        $result = mysqli_query($conn, "SELECT likes, dislikes FROM posts WHERE postID = $post_id");
        $row = mysqli_fetch_assoc($result);
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Unable to update']);
    }
}
?>
