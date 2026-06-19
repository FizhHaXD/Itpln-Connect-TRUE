<?php
/**
 * Security Helper Functions
 * Digunakan untuk XSS protection, CSRF protection, dll
 */

/**
 * XSS Protection - Escape HTML output
 */
function safe($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF Token
 */
function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function csrf_verify() {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

/**
 * CSRF Token HTML Input
 */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Check Session Timeout (1 hour = 3600 seconds)
 */
function check_session_timeout($timeout = 3600) {
    if (isset($_SESSION['last_activity'])) {
        $elapsed = time() - $_SESSION['last_activity'];
        if ($elapsed > $timeout) {
            session_destroy();
            header("Location: " . BASE_URL . "auth/login.php?error=session_expired");
            exit();
        }
    }
    $_SESSION['last_activity'] = time();
}

/**
 * Validate File Upload
 */
function validate_image_upload($file, $maxSize = 2097152) { // 2MB default
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
    
    // Check if file exists
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'No file uploaded or upload error'];
    }
    
    // Check file size
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'File too large. Max ' . ($maxSize / 1024 / 1024) . 'MB'];
    }
    
    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime, $allowed_types)) {
        return ['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, GIF allowed'];
    }
    
    // Check extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext)) {
        return ['success' => false, 'error' => 'Invalid file extension'];
    }
    
    // Generate safe filename
    $newFilename = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    
    return [
        'success' => true,
        'filename' => $newFilename,
        'extension' => $ext,
        'mime' => $mime
    ];
}

/**
 * Sanitize filename
 */
function safe_filename($filename) {
    // Remove special characters
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    return $filename;
}

/**
 * Check if user is logged in
 */
function require_login() {
    if (!isset($_SESSION['id_user'])) {
        header("Location: " . BASE_URL . "auth/login.php?need_login=1");
        exit();
    }
}

/**
 * Get current user data
 */
function get_logged_user($conn) {
    if (!isset($_SESSION['id_user'])) {
        return null;
    }
    
    $stmt = $conn->prepare("SELECT id_user, nama, email, foto FROM users WHERE id_user = ?");
    $stmt->bind_param("i", $_SESSION['id_user']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    return $user;
}

/**
 * Check if current user is admin
 */
function is_admin($conn) {
    if (!isset($_SESSION['id_user'])) {
        return false;
    }
    
    $stmt = $conn->prepare("SELECT role FROM users WHERE id_user = ?");
    $stmt->bind_param("i", $_SESSION['id_user']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    return ($user && $user['role'] === 'admin');
}

/**
 * Require admin access (redirect if not admin)
 */
function require_admin($conn) {
    if (!is_admin($conn)) {
        header("Location: " . BASE_URL . "dashboard/index.php?error=admin_only");
        exit();
    }
}
