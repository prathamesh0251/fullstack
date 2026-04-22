<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

require_admin();

// Get statistics
$total_movies = $conn->query("SELECT COUNT(*) as count FROM movies")->fetch_assoc()['count'];
$total_shows = $conn->query("SELECT COUNT(*) as count FROM shows")->fetch_assoc()['count'];
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total_amount) as total FROM bookings WHERE payment_status = 'completed'")->fetch_assoc()['total'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'user'")->fetch_assoc()['count'];

// Get recent bookings
$recent_bookings = $conn->query("
    SELECT b.booking_id, b.booking_date, b.total_amount, m.title, u.first_name, u.last_name
    FROM bookings b
    JOIN shows sh ON b.show_id = sh.show_id
    JOIN movies m ON sh.movie_id = m.movie_id
    JOIN users u ON b.user_id = u.user_id
    ORDER BY b.booking_date DESC
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CineBook</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">🎬 CineBook - Admin Panel</div>
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
            </ul>
            <div class="auth-buttons">
                <span style="color: white; margin-right: 10px;">
                    Admin: <?php echo htmlspecialchars($_SESSION['first_name']); ?>
                </span>
                <a href="../pages/logout.php">Logout</a>
            </div>
        </nav>
    </header>

    <main class="container">
        <div class="admin-container">
            <div class="admin-sidebar">
                <ul class="admin-menu">
                    <li><a href="dashboard.php" class="active">📊 Dashboard</a></li>
                    <li><a href="manage-movies.php">🎬 Movies</a></li>
                    <li><a href="manage-shows.php">⏰ Shows</a></li>
                    <li><a href="manage-seats.php">🎫 Seats</a></li>
                    <li><a href="view-bookings.php">📋 Bookings</a></li>
                    <li><a href="reports.php">📈 Reports</a></li>
                    <li><a href="manage-users.php">👥 Users</a></li>
                </ul>
            </div>

            <div>
                <h1 style="color: #333; margin-bottom: 30px;">Dashboard</h1>

                <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 40px;">
                    <div class="card" style="text-align: center;">
                        <div style="font-size: 32px; margin-bottom: 10px;">🎬</div>
                        <div style="font-size: 24px; font-weight: bold; color: #667eea;"><?php echo $total_movies; ?></div>
                        <div style="color: #666;">Total Movies</div>
                    </div>

                    <div class="card" style="text-align: center;">
                        <div style="font-size: 32px; margin-bottom: 10px;">⏰</div>
                        <div style="font-size: 24px; font-weight: bold; color: #667eea;"><?php echo $total_shows; ?></div>
                        <div style="color: #666;">Total Shows</div>
                    </div>

                    <div class="card" style="text-align: center;">
                        <div style="font-size: 32px; margin-bottom: 10px;">🎫</div>
                        <div style="font-size: 24px; font-weight: bold; color: #667eea;"><?php echo $total_bookings; ?></div>
                        <div style="color: #666;">Total Bookings</div>
                    </div>

                    <div class="card" style="text-align: center;">
                        <div style="font-size: 32px; margin-bottom: 10px;">💰</div>
                        <div style="font-size: 24px; font-weight: bold; color: #28a745;">₹<?php echo number_format($total_revenue ?? 0, 0); ?></div>
                        <div style="color: #666;">Total Revenue</div>
                    </div>

                    <div class="card" style="text-align: center;">
                        <div style="font-size: 32px; margin-bottom: 10px;">👥</div>
                        <div style="font-size: 24px; font-weight: bold; color: #667eea;"><?php echo $total_users; ?></div>
                        <div style="color: #666;">Total Users</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>Recent Bookings</h2>
                    </div>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>User</th>
                                <th>Movie</th>
                                <th>Amount</th>
                                <th>Booked On</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_bookings as $booking): ?>
                                <tr>
                                    <td><strong><?php echo '#TKT' . str_pad($booking['booking_id'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                                    <td><?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['title']); ?></td>
                                    <td>₹<?php echo number_format($booking['total_amount'], 2); ?></td>
                                    <td><?php echo format_date($booking['booking_date']); ?></td>
                                    <td>
                                        <a href="view-bookings.php?id=<?php echo $booking['booking_id']; ?>" class="btn btn-small btn-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div style="margin-top: 15px; text-align: center;">
                        <a href="view-bookings.php" class="btn btn-secondary">View All Bookings</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 CineBook - Admin Panel</p>
    </footer>
</body>
</html>
