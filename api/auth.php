<?php
require_once 'includes/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: /learning-dashboard.php");
    exit();
}

// Handle Registration
if (isset($_POST['register'])) {
    $first_name = sanitize($_POST['first_name']);
    $last_name  = sanitize($_POST['last_name']);
    $username   = sanitize($_POST['username']);
    $email      = sanitize($_POST['email']);
    $password   = $_POST['password'];
    $confirm    = $_POST['confirm_password'];

    // 1. Password Strength Check
    $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/";
    if (!preg_match($pattern, $password)) {
        header("Location: /learning-dashboard.php?error=weak_password");
        exit();
    }

    // 2. Check if passwords match
    if ($password !== $confirm) {
        header("Location: /learning-dashboard.php?error=password_mismatch");
        exit();
    }

    // 3. PRE-CHECK: Check if Email or Username already exists
    // Fetching role as well to handle auto-login for existing users correctly
    $check_stmt = $conn->prepare("SELECT id, username, first_name, last_name, role FROM users WHERE username = ? OR email = ?");
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($user = $check_result->fetch_assoc()) {
        // User already exists - Log them in directly
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['role'] = $user['role']; // Assign role from DB
        
        // Dynamic Redirect
        if ($_SESSION['role'] === 'admin') {
            header("Location: /admin-dashboard.php");
        } else {
            header("Location: /learning-dashboard.php");
        }
        exit();
    }
    $check_stmt->close();

    // 4. Hash and Insert (Role defaults to 'student' in DB schema)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $first_name, $last_name, $username, $email, $hashed_password);

    if ($stmt->execute()) {
        $new_user_id = $conn->insert_id;
        
        $_SESSION['user_id'] = $new_user_id;
        $_SESSION['username'] = $username;
        $_SESSION['full_name'] = $first_name . ' ' . $last_name;
        $_SESSION['role'] = 'student'; // New registrations are students by default

        header("Location: /learning-dashboard.php?signup=success");
        exit();
    } else {
        header("Location: /learning-dashboard.php?error=db_error");
        exit();
    }
}

// Handle Login
if (isset($_POST['login'])) {
    $identifier = sanitize($_POST['identifier']); 
    $password   = $_POST['password'];

    // Fetching 'role' to differentiate Admin vs Student
    $stmt = $conn->prepare("SELECT id, username, first_name, last_name, password, role FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['role'] = $user['role']; // Save role to session
            
            // Handle Cart Loading
            $_SESSION['cart'] = []; 
            $cart_stmt = $conn->prepare("SELECT course_id, course_name, course_price FROM cart WHERE user_id = ?");
            $cart_stmt->bind_param("i", $user['id']);
            $cart_stmt->execute();
            $cart_res = $cart_stmt->get_result();
            while ($row = $cart_res->fetch_assoc()) {
                $_SESSION['cart'][$row['course_id']] = [
                    'name' => $row['course_name'],
                    'price' => $row['course_price'],
                    'quantity' => 1
                ];
            }

            // Role-Based Redirection
            if ($_SESSION['role'] === 'admin') {
                header("Location: /admin-dashboard.php");
            } else {
                header("Location: /learning-dashboard.php");
            }
            exit();
        } else {
            header("Location: /learning-dashboard.php?error=invalid_password");
            exit();
        }
    } else {
        header("Location: /learning-dashboard.php?error=user_not_found");
        exit();
    }
}

// Handle Profile Update
if (isset($_POST['update_profile'])) {
    $user_id = $_SESSION['user_id'];
    $first_name = sanitize($_POST['first_name']);
    $last_name  = sanitize($_POST['last_name']);
    $phone      = sanitize($_POST['phone']);
    $occupation = sanitize($_POST['occupation']);
    $bio        = sanitize($_POST['bio']);

    $update_stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, phone=?, occupation=?, bio=? WHERE id=?");
    $update_stmt->bind_param("sssssi", $first_name, $last_name, $phone, $occupation, $bio, $user_id);

    if ($update_stmt->execute()) {
        $_SESSION['full_name'] = $first_name . ' ' . $last_name;
        header("Location: /learning-dashboard.php?view=settings&success=1");
    } else {
        header("Location: /learning-dashboard.php?view=settings&error=update_failed");
    }
    $update_stmt->close();
    exit();
}
if (isset($_POST['submit_quiz'])) {
    $course_id = (int)$_POST['course_id'];
    $user_id = $_SESSION['user_id'];
    $score = 0;

    $questions = $conn->query("SELECT id, correct_option FROM quizzes WHERE course_id = $course_id");
    $total = $questions->num_rows;

    while ($q = $questions->fetch_assoc()) {
        $ans_key = "answer_" . $q['id'];
        if (isset($_POST[$ans_key]) && $_POST[$ans_key] === $q['correct_option']) {
            $score++;
        }
    }

    $stmt = $conn->prepare("INSERT INTO quiz_attempts (user_id, course_id, score, total_questions) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $user_id, $course_id, $score, $total);
    $stmt->execute();

    // Redirect to Learning Dashboard to show the new attempt
    header("Location: /learning-dashboard.php?status=quiz_completed&score=$score");
    exit();
}

if (isset($_POST['submit_question']) && isset($_SESSION['user_id'])) {
    $user_id = (int)$_SESSION['user_id'];
    $course_id = (int)$_POST['course_id'];
    $question = trim($_POST['question']);

    if (!empty($question) && $course_id > 0) {
        $stmt = $conn->prepare("INSERT INTO course_qa (user_id, course_id, question) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $course_id, $question);
        
        if ($stmt->execute()) {
            header("Location: /learning-dashboard.php?view=qa&status=submitted");
        } else {
            header("Location: /learning-dashboard.php?view=qa&status=error");
        }
    } else {
        header("Location: /learning-dashboard.php?view=qa&status=invalid");
    }
    exit();
}
?>