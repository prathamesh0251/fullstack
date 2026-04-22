<?php
require_once '../config/config.php';
require_once '../includes/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required';
    } else {
        $result = login_user($conn, $email, $password);
        if ($result['success']) {
            $redirect = $_GET['redirect'] ?? 'index.php';
            if (strpos($redirect, 'admin') !== false) {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../pages/movies.php");
            }
            exit();
        } else {
            $error = $result['message'];
        }
    }
}

// If already logged in, redirect
if (is_logged_in()) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CineBook</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">🎬 CineBook</div>
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <div style="max-width: 400px; margin: 50px auto;">
            <div class="card">
                <div class="card-header">
                    <h2>Login</h2>
                </div>

                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" class="form-group">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
                </form>

                <hr style="margin: 20px 0;">

                <p style="text-align: center; color: #666;">
                    Don't have an account? <a href="register.php" style="color: #667eea; text-decoration: none; font-weight: 600;">Register here</a>
                </p>

                <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 4px;">
                    <p style="font-size: 12px; color: #666; margin: 0;">
                        <strong>Demo Credentials:</strong><br>
                        Email: user1@example.com<br>
                        Password: password
                    </p>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 CineBook - Movie Ticket Booking System</p>
    </footer>
</body>
</html>
