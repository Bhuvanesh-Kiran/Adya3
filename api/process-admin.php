<?php
require_once 'includes/config.php';
// session_start() is already called in config.php

// Security Guard: Only allow Admins
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

function uploadToImgBB($file_tmp) {
    if (!$file_tmp || !is_uploaded_file($file_tmp)) return null;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.imgbb.com/1/upload?key=' . IMGBB_API_KEY);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'image' => base64_encode(file_get_contents($file_tmp))
    ]);

    $response = curl_exec($ch);
    $result = json_decode($response, true);
    curl_close($ch);

    return (isset($result['data']['url'])) ? $result['data']['url'] : null;
}

// --- HANDLE ADD COURSE ---
if (isset($_POST['add_course'])) {
    $title    = sanitize($_POST['title']);
    $price    = sanitize($_POST['price']);
    $category = sanitize($_POST['category']);
    $duration = sanitize($_POST['duration']);
    
    // Default placeholder
    $db_image_path = "assets/images/placeholder.jpg"; 

    // Upload to ImgBB instead of local folder
    if (!empty($_FILES['course_image']['tmp_name'])) {
        $uploaded_url = uploadToImgBB($_FILES['course_image']['tmp_name']);
        if ($uploaded_url) {
            $db_image_path = $uploaded_url;
        }
    }

    $stmt = $conn->prepare("INSERT INTO courses (title, category, new_price, image_path, duration) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $title, $category, $price, $db_image_path, $duration);

    if ($stmt->execute()) {
        header("Location: admin-dashboard.php?view=manage_courses&status=success");
    } else {
        header("Location: admin-dashboard.php?view=manage_courses&status=error&msg=db_fail");
    }
    $stmt->close();
    exit();
}

// --- HANDLE UPDATE COURSE ---
if (isset($_POST['update_course'])) {
    $id       = sanitize($_POST['course_id']);
    $title    = sanitize($_POST['title']);
    $price    = sanitize($_POST['price']);
    $category = sanitize($_POST['category']);
    $duration = sanitize($_POST['duration']);

    // Check if a new image was uploaded
    if (!empty($_FILES['course_image']['tmp_name'])) {
        $uploaded_url = uploadToImgBB($_FILES['course_image']['tmp_name']);
        if ($uploaded_url) {
            // Update with NEW image URL
            $stmt = $conn->prepare("UPDATE courses SET title=?, new_price=?, category=?, image_path=?, duration=? WHERE id=?");
            $stmt->bind_param("sdsssi", $title, $price, $category, $uploaded_url, $duration, $id);
        } else {
            header("Location: admin-dashboard.php?view=manage_courses&status=error&msg=upload_fail");
            exit();
        }
    } else {
        // Update WITHOUT changing the existing image
        $stmt = $conn->prepare("UPDATE courses SET title=?, new_price=?, category=?, duration=? WHERE id=?");
        $stmt->bind_param("sdssi", $title, $price, $category, $duration, $id);
    }

    if ($stmt->execute()) {
        header("Location: admin-dashboard.php?view=manage_courses&status=updated");
    } else {
        header("Location: admin-dashboard.php?view=manage_courses&status=error");
    }
    $stmt->close();
    exit();
}
// Handle Delete
if (isset($_GET['delete_course'])) {
    $id = sanitize($_GET['delete_course']);
    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()) {
        header("Location: admin-dashboard.php?view=manage_courses&status=deleted");
    }
    exit();
}


// Handle Add Lesson
if (isset($_POST['add_lesson'])) {
    $course_id    = (int)$_POST['course_id'];
    $section      = sanitize($_POST['section_name']);
    $title        = sanitize($_POST['lesson_title']);
    $duration     = sanitize($_POST['duration']);
    $sort         = (int)$_POST['sort_order'];
    $is_preview   = isset($_POST['is_preview']) ? 1 : 0;

    // NEW: Clean the YouTube URL to get only the ID
    $raw_url      = $_POST['video_url'];
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $raw_url, $match);
    $video_id     = isset($match[1]) ? $match[1] : $raw_url;

    $stmt = $conn->prepare("INSERT INTO lessons (course_id, section_name, lesson_title, duration, is_preview, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssii", $course_id, $section, $title, $duration, $is_preview, $sort);

    if ($stmt->execute()) {
        header("Location: admin-dashboard.php?view=manage_content&course_id=$course_id&status=success");
    }
    exit();
}

// Handle Update Lesson
if (isset($_POST['update_lesson'])) {
    $lesson_id    = (int)$_POST['lesson_id'];
    $course_id    = (int)$_POST['course_id'];
    $section      = sanitize($_POST['section_name']);
    $title        = sanitize($_POST['lesson_title']);
    $duration     = sanitize($_POST['duration']);
    $sort         = (int)$_POST['sort_order'];
    $is_preview   = isset($_POST['is_preview']) ? 1 : 0;
    
    // Clean Video URL
    $raw_url      = $_POST['video_url'];
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $raw_url, $match);
    $video_id     = isset($match[1]) ? $match[1] : $raw_url;

    $stmt = $conn->prepare("UPDATE lessons SET section_name=?, lesson_title=?, video_url=?, duration=?, is_preview=?, sort_order=? WHERE id=?");
    $stmt->bind_param("ssssiii", $section, $title, $video_id, $duration, $is_preview, $sort, $lesson_id);

    if ($stmt->execute()) {
        header("Location: admin-dashboard.php?view=manage_content&course_id=$course_id&status=updated");
    } else {
        header("Location: admin-dashboard.php?view=manage_content&course_id=$course_id&status=error");
    }
    $stmt->close();
    exit();
}


// Handle Delete Lesson
if (isset($_GET['delete_lesson'])) {
    $id = (int)$_GET['delete_lesson'];
    $course_id = (int)$_GET['course_id'];
    $conn->query("DELETE FROM lessons WHERE id = $id");
    header("Location: admin-dashboard.php?view=manage_content&course_id=$course_id&status=deleted");
    exit();
}

// Handle Add Quiz Question
if (isset($_POST['add_quiz'])) {
    $course_id = (int)$_POST['course_id'];
    $question = sanitize($_POST['question']);
    $a = sanitize($_POST['opt_a']);
    $b = sanitize($_POST['opt_b']);
    $c = sanitize($_POST['opt_c']);
    $d = sanitize($_POST['opt_d']);
    $correct = sanitize($_POST['correct']);

    $stmt = $conn->prepare("INSERT INTO quizzes (course_id, question, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $course_id, $question, $a, $b, $c, $d, $correct);
    $stmt->execute();
    header("Location: admin-dashboard.php?view=manage_quizzes&course_id=$course_id&status=success");
    exit();
}
// Handle Delete Quiz Question
if (isset($_GET['delete_quiz'])) {
    $id = (int)$_GET['delete_quiz'];
    $course_id = (int)$_GET['course_id'];
    
    $stmt = $conn->prepare("DELETE FROM quizzes WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()) {
        header("Location: admin-dashboard.php?view=manage_quizzes&course_id=$course_id&status=deleted");
    }
    exit();
}

// Handle Submit Answer
if (isset($_POST['submit_answer'])) {
    $qa_id = (int)$_POST['qa_id'];
    $answer = sanitize($_POST['answer']);
    $stmt = $conn->prepare("UPDATE course_qa SET answer = ? WHERE id = ?");
    $stmt->bind_param("si", $answer, $qa_id);
    $stmt->execute();
    header("Location: admin-dashboard.php?view=qa_moderation&status=updated");
    exit();
}
// 1. Handle Submitting an Answer
if (isset($_POST['submit_answer'])) {
    $qa_id = (int)$_POST['qa_id'];
    $answer = $_POST['answer']; // Use your sanitize function if you have one

    $stmt = $conn->prepare("UPDATE course_qa SET answer = ? WHERE id = ?");
    $stmt->bind_param("si", $answer, $qa_id);
    
    if ($stmt->execute()) {
        header("Location: admin-dashboard.php?view=qa_moderation&status=success");
    } else {
        header("Location: admin-dashboard.php?view=qa_moderation&status=error");
    }
    exit();
}

// 2. Handle Deleting a Question
if (isset($_GET['delete_qa'])) {
    $qa_id = (int)$_GET['delete_qa'];
    
    $stmt = $conn->prepare("DELETE FROM course_qa WHERE id = ?");
    $stmt->bind_param("i", $qa_id);
    $stmt->execute();
    
    header("Location: admin-dashboard.php?view=qa_moderation&status=deleted");
    exit();
}

if (isset($_POST['mark_read'])) {
    $msg_id = (int)$_POST['msg_id'];
    $conn->query("UPDATE contact_requests SET status = 'read' WHERE id = $msg_id");
    header("Location: admin-dashboard.php?view=inbox&status=updated");
    exit();
}
?>

