<?php
// includes/auth.php
// Authentication functions

session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
}

function checkRole($allowed_roles) {
    $user_role = getUserRole();
    if (!in_array($user_role, $allowed_roles)) {
        header('Location: /pages/error.php?code=403');
        exit;
    }
}

function getUserById($conn, $user_id) {
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function login($conn, $email, $password) {
    $query = "SELECT id, username, email, password, role, status FROM users WHERE email = ? AND status = 'active'";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        return ['success' => false, 'message' => 'Database error'];
    }
    
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['success' => false, 'message' => 'Invalid email or password'];
    }
    
    $user = $result->fetch_assoc();
    
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid email or password'];
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    
    return ['success' => true, 'message' => 'Login successful'];
}

function register($conn, $username, $email, $password, $first_name = '', $last_name = '') {
    // Check if user exists
    $query = "SELECT id FROM users WHERE email = ? OR username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return ['success' => false, 'message' => 'Email or username already exists'];
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    // Insert user
    $query = "INSERT INTO users (username, email, password, first_name, last_name, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssss', $username, $email, $hashed_password, $first_name, $last_name);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Registration successful'];
    } else {
        return ['success' => false, 'message' => 'Registration failed'];
    }
}

function logout() {
    session_destroy();
    header('Location: /pages/index.php');
    exit;
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function sanitizeOutput($output) {
    return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
}
?>
