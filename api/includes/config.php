<?php
// =====================================================
// CONFIGURATION FILE
// =====================================================
// Add this BEFORE session_start()
class DatabaseSessionHandler implements SessionHandlerInterface {
    private $db;
    public function open($path, $name): bool { global $conn; $this->db = $conn; return true; }
    public function close(): bool { return true; }
    public function read($id): string {
        $stmt = $this->db->prepare("SELECT data FROM sessions WHERE id = ?");
        $stmt->bind_param("s", $id); $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res['data'] ?? '';
    }
    public function write($id, $data): bool {
        $now = time();
        $stmt = $this->db->prepare("REPLACE INTO sessions (id, data, last_access) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $id, $data, $now);
        return $stmt->execute();
    }
    public function destroy($id): bool {
        $stmt = $this->db->prepare("DELETE FROM sessions WHERE id = ?");
        $stmt->bind_param("s", $id); return $stmt->execute();
    }
    public function gc($max): int|false {
        $old = time() - $max;
        $stmt = $this->db->prepare("DELETE FROM sessions WHERE last_access < ?");
        $stmt->bind_param("i", $old); return $stmt->execute() ? 1 : false;
    }
}

$handler = new DatabaseSessionHandler();
session_set_save_handler($handler, true);
ob_start(); // Start output buffering

// Session Security & Persistence Settings
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_path', '/');

// Site Configuration
define('SITE_NAME', 'Adya3 Solutions');
define('SITE_URL', 'https://adya3.com');
define('SITE_EMAIL', 'contact@adya3.com');
define('SITE_PHONE', '+91 9030761831');
define('SITE_PHONE_2', '+91 9381389350');
define('SITE_ADDRESS', 'Flat No: 304, Carmel City Squares, Lotus Diagnostics, Chaitanya Nagar, Chinna Gantyada, Gajuwaka, Visakhapatnam-530026');

// Social Media Links
define('FACEBOOK_URL', 'https://www.facebook.com/profile.php?id=100071789641443');
define('INSTAGRAM_URL', 'https://www.instagram.com/adya3_solutions/?hl=en');

// Database Configuration (if needed)
define('DB_HOST', 'bdfmslrjyj2ghxmufzkl-mysql.services.clever-cloud.com');
define('DB_USER', 'urwekbajkg1uduch');
define('DB_PASS', '5YGFhxJdzEyQXX8L8NQd');
define('DB_NAME', 'bdfmslrjyj2ghxmufzkl');

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');

// Paths
define('BASE_PATH', dirname(__DIR__));
define('INCLUDES_PATH', BASE_PATH . '/includes/');
// Assets are in the ROOT, not inside api/
define('ASSETS_PATH', dirname(BASE_PATH) . '/assets/');

// Get current page
$current_page = basename($_SERVER['PHP_SELF'], '.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to get active class for navigation
function isActive($page) {
    global $current_page;
    return ($current_page === $page) ? 'active' : '';
}

// Function to sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
// Create Database Connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
