<?php
// Authentication functions

function register_user($conn, $email, $password, $first_name, $last_name, $phone = '') {
    // Check if email already exists
    $check_query = "SELECT user_id FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    // Insert user
    $insert_query = "INSERT INTO users (email, password, first_name, last_name, phone) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("sssss", $email, $hashed_password, $first_name, $last_name, $phone);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Registration successful', 'user_id' => $stmt->insert_id];
    } else {
        return ['success' => false, 'message' => 'Registration failed'];
    }
}

function login_user($conn, $email, $password) {
    $query = "SELECT user_id, email, first_name, last_name, user_type, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return ['success' => false, 'message' => 'Invalid email or password'];
    }
    
    $user = $result->fetch_assoc();
    
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['user_type'] = $user['user_type'];
        
        return ['success' => true, 'message' => 'Login successful', 'user_type' => $user['user_type']];
    } else {
        return ['success' => false, 'message' => 'Invalid email or password'];
    }
}

function logout_user() {
    session_destroy();
    return ['success' => true, 'message' => 'Logged out successfully'];
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
        exit();
    }
}

function require_admin() {
    if (!is_admin()) {
        header("Location: ../index.php?error=Access denied");
        exit();
    }
}

function get_user_info($conn, $user_id) {
    $query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
?>
