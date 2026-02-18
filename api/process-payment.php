<?php
require_once 'includes/config.php';
session_start();

// Security Guard
if (!isset($_SESSION['user_id'])) {
    header("Location: learning-dashboard.php?error=login_required");
    exit();
}

// Ensure Razorpay payment ID exists in URL
if (isset($_GET['payment_id'])) {
    $user_id = $_SESSION['user_id'];
    $cart = $_SESSION['cart'] ?? [];

    if (empty($cart)) {
        header("Location: courses.php?error=empty_cart");
        exit();
    }

    try {
        $conn->begin_transaction();

        // Loop through cart items and insert based on YOUR table columns
        foreach ($cart as $course_id => $item) {
            // MATCHED TO YOUR TABLE: id, user_id, course_id, payment_status, enrolled_at
            $stmt = $conn->prepare("INSERT INTO enrollments (user_id, course_id, payment_status, enrolled_at) VALUES (?, ?, 'completed', NOW())");
            $stmt->bind_param("ii", $user_id, $course_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Execution failed: " . $stmt->error);
            }
        }

        $conn->commit();
        unset($_SESSION['cart']); // Clear cart on success

        header("Location: learning-dashboard.php?view=enrolled&status=success");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        // Log the exact error to XAMPP's error log for debugging
        error_log("Payment DB Error: " . $e->getMessage());
        header("Location: cart.php?error=db_fail");
        exit();
    }
} else {
    header("Location: courses.php");
    exit();
}