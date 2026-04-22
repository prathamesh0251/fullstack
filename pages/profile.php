<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

require_login();

$user = get_user_info($conn, $_SESSION['user_id']);
$bookings = get_user_bookings($conn, $_SESSION['user_id']);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (empty($first_name) || empty($last_name)) {
        $error = 'First name and last name are required';
    } else {
        $update_query = "UPDATE users SET first_name = ?, last_name = ?, phone = ? WHERE user_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sssi", $first_name, $last_name, $phone, $_SESSION['user_id']);

        if ($stmt->execute()) {
            $success = 'Profile updated successfully';
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $user['first_name'] = $first_name;
            $user['last_name'] = $last_name;
            $user['phone'] = $phone;
        } else {
            $error = 'Failed to update profile';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - CineBook</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">🎬 CineBook</div>
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="movies.php">Movies</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="bookings.php">My Bookings</a></li>
            </ul>
            <div class="auth-buttons">
                <span style="color: white; margin-right: 10px;">
                    Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>
                </span>
                <a href="logout.php">Logout</a>
            </div>
        </nav>
    </header>

    <main class="container">
        <div style="margin: 30px 0;">
            <h1 style="color: #333; margin-bottom: 30px;">My Profile</h1>

            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <div class="profile-container">
                <div class="profile-sidebar">
                    <div class="profile-avatar">👤</div>
                    <div class="profile-info">
                        <div class="profile-info-label">Name</div>
                        <div class="profile-info-value"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                    </div>
                    <div class="profile-info">
                        <div class="profile-info-label">Email</div>
                        <div class="profile-info-value" style="word-break: break-all;"><?php echo htmlspecialchars($user['email']); ?></div>
                    </div>
                    <div class="profile-info">
                        <div class="profile-info-label">Member Since</div>
                        <div class="profile-info-value"><?php echo format_date($user['created_at']); ?></div>
                    </div>
                </div>

                <div>
                    <div class="card">
                        <div class="card-header">
                            <h2>Edit Profile</h2>
                        </div>

                        <form method="POST">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="background-color: #f8f9fa; cursor: not-allowed;">
                                <small style="color: #999; display: block; margin-top: 5px;">Email cannot be changed</small>
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                            </div>

                            <button type="submit" class="btn btn-primary" style="width: 100%;">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>

            <div style="margin-top: 40px;">
                <h2 style="color: #333; margin-bottom: 20px;">Recent Bookings</h2>

                <?php if(!empty($bookings)): ?>
                    <div class="card">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Movie</th>
                                    <th>Show Time</th>
                                    <th>Seats</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($bookings as $booking): ?>
                                    <tr>
                                        <td><?php echo '#TKT' . str_pad($booking['booking_id'], 6, '0', STR_PAD_LEFT); ?></td>
                                        <td><?php echo htmlspecialchars($booking['title']); ?></td>
                                        <td><?php echo format_datetime($booking['show_time']); ?></td>
                                        <td><?php echo $booking['seat_count']; ?></td>
                                        <td>₹<?php echo $booking['total_amount']; ?></td>
                                        <td>
                                            <span style="padding: 4px 8px; border-radius: 4px; background-color: #d4edda; color: #155724; font-size: 12px;">
                                                <?php echo ucfirst($booking['booking_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="booking-details.php?booking_id=<?php echo $booking['booking_id']; ?>" class="btn btn-small btn-primary">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        You haven't booked any tickets yet. <a href="movies.php" style="color: #0c5460; text-decoration: underline;">Browse movies</a> and book your favorite movie now!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 CineBook - Movie Ticket Booking System</p>
    </footer>

    <script src="../js/script.js"></script>
</body>
</html>
