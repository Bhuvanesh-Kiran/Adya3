<?php
require_once 'includes/config.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Response array
$response = array(
    'success' => false,
    'message' => ''
);

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Get and sanitize form data
    $name = isset($_POST['name']) ? sanitize($_POST['name']) : '';
    $phone = isset($_POST['phone']) ? sanitize($_POST['phone']) : '';
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
    $service = isset($_POST['service']) ? sanitize($_POST['service']) : '';
    $message = isset($_POST['message']) ? sanitize($_POST['message']) : '';
    
    // Validate required fields
    if (empty($name) || empty($phone) || empty($email) || empty($service)) {
        throw new Exception('Please fill in all required fields');
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }
    
    // Service names mapping
    $service_names = array(
        'website' => 'Website Designing and Development',
        'digital-marketing' => 'Digital Marketing',
        'brochure' => 'Brochure Designing',
        'app-development' => 'App Development',
        'logo-design' => 'Logo Design',
        'seo' => 'SEO Services',
        'other' => 'Other Services'
    );
    
    $service_name = isset($service_names[$service]) ? $service_names[$service] : $service;

    // --- DUPLICATE CHECK LOGIC ---
    $check_sql = "SELECT id FROM contact_requests 
                  WHERE email = ? AND message = ? 
                  AND created_at > NOW() - INTERVAL 1 MINUTE 
                  LIMIT 1";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $email, $message);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    // If a match is found, stop and throw error to prevent double entry
    if ($check_result->num_rows > 0) {
        throw new Exception('We have already received your request. Please wait a moment.');
    }
    $check_stmt->close();

    // --- DATABASE INSERTION ---
    $sql = "INSERT INTO contact_requests (name, phone, email, service, message, status) VALUES (?, ?, ?, ?, ?, 'new')";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Database preparation failed: ' . $conn->error);
    }

    $stmt->bind_param("sssss", $name, $phone, $email, $service_name, $message);

    if (!$stmt->execute()) {
        throw new Exception('Database error: Failed to save your request.');
    }
    $stmt->close();

    // --- ADMIN NOTIFICATION EMAIL ---
    $email_content = "<h2>New Contact Request</h2>
                      <p><strong>Name:</strong> $name</p>
                      <p><strong>Phone:</strong> $phone</p>
                      <p><strong>Email:</strong> $email</p>
                      <p><strong>Service:</strong> $service_name</p>
                      <p><strong>Message:</strong> $message</p>";
    
    $headers = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\nFrom: " . SITE_NAME . " <noreply@adya3.com>";
    @mail(SITE_EMAIL, "New Inquiry: $service_name", $email_content, $headers);
    
    $response['success'] = true;
    $response['message'] = 'Thank you! Your request has been submitted successfully.';
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;