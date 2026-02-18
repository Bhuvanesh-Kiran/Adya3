<?php
// 1. Start Output Buffering immediately to prevent "Headers already sent" errors
ob_start();

// 2. Database Credentials (Pull from Environment Variables for Vercel/CleverCloud)
define('DB_HOST', getenv('MYSQL_ADDON_HOST') ?: 'bdfmslrjyj2ghxmufzkl-mysql.services.clever-cloud.com');
define('DB_USER', getenv('MYSQL_ADDON_USER') ?: 'urwekbajkg1uduch');
define('DB_PASS', getenv('MYSQL_ADDON_PASSWORD') ?: '5YGFhxJdzEyQXX8L8NQd');
define('DB_NAME', getenv('MYSQL_ADDON_DB') ?: 'bdfmslrjyj2ghxmufzkl');
define('DB_PORT', getenv('MYSQL_ADDON_PORT') ?: 3306);
// Add this near your other defines
define('IMGBB_API_KEY', 'e7dc348c496b36a71e1c4a87e499472f');
// 3. Create Database Connection (CRITICAL: Must happen before session_start)
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 4. Define the Database Session Handler Class
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

// 5. Configure and Start the Session
$handler = new DatabaseSessionHandler();
session_set_save_handler($handler, true);

// Session Security & Persistence Settings
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_path', '/');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 6. Site Configuration Constants
define('SITE_NAME', 'Adya3 Solutions');
define('SITE_URL', 'https://adya3.com');
define('SITE_EMAIL', 'contact@adya3.com');
define('SITE_PHONE', '+91 9030761831');
define('SITE_PHONE_2', '+91 9381389350');
define('SITE_ADDRESS', 'Flat No: 304, Carmel City Squares, Lotus Diagnostics, Chaitanya Nagar, Chinna Gantyada, Gajuwaka, Visakhapatnam-530026');

// 7. Social Media Links
define('FACEBOOK_URL', 'https://www.facebook.com/profile.php?id=100071789641443');
define('INSTAGRAM_URL', 'https://www.instagram.com/adya3_solutions/?hl=en');

// 8. Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');

// 9. Folder Paths
define('BASE_PATH', dirname(__DIR__));
define('INCLUDES_PATH', BASE_PATH . '/includes/');
define('ASSETS_PATH', dirname(BASE_PATH) . '/assets/');

// 10. Helper Functions & Variables
$current_page = basename($_SERVER['PHP_SELF'], '.php');

function isActive($page) {
    global $current_page;
    return ($current_page === $page) ? 'active' : '';
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>