<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

require_admin();

$bookings_query = "
    SELECT b.*, u.first_name, u.last_name, u.email, m.title, sh.show_time, sh.theater_name, COUNT(bd.booking_detail_id) as seat_count
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN shows sh ON b.show_id = sh.show_id
    JOIN movies m ON sh.movie_id = m.movie_id
    JOIN booking_details bd ON b.booking_id = bd.booking_id
    GROUP BY b.booking_id
    ORDER BY b.booking_date DESC
";

$bookings = $conn->query($bookings_query)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings - CineBook Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">🎬 CineBook - Admin</div>
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
                    <li><a href="dashboard.php">📊 Dashboard</a></li>
                    <li><a href="manage-movies.php">🎬 Movies</a></li>
                    <li><a href="manage-shows.php">⏰ Shows</a></li>
                    <li><a href="manage-seats.php">🎫 Seats</a></li>
                    <li><a href="view-bookings.php" class="active">📋 Bookings</a></li>
                    <li><a href="reports.php">📈 Reports</a></li>
                </ul>
            </div>

            <div>
                <h1 style="color: #333; margin-bottom: 30px;">All Bookings</h1>

                <div class="card">
                    <?php if(!empty($bookings)): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Movie</th>
                                    <th>Show Time</th>
                                    <th>Seats</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Booked On</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($bookings as $booking): ?>
                                    <tr>
                                        <td><strong><?php echo '#TKT' . str_pad($booking['booking_id'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                                        <td><?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['email']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['title']); ?></td>
                                        <td><?php echo format_datetime($booking['show_time']); ?></td>
                                        <td><?php echo $booking['seat_count']; ?></td>
                                        <td>₹<?php echo number_format($booking['total_amount'], 2); ?></td>
                                        <td>
                                            <span style="padding: 4px 8px; border-radius: 4px; 
                                                         background-color: <?php echo $booking['booking_status'] === 'confirmed' ? '#d4edda' : '#f8d7da'; ?>;
                                                         color: <?php echo $booking['booking_status'] === 'confirmed' ? '#155724' : '#721c24'; ?>;
                                                         font-size: 12px; font-weight: 600;">
                                                <?php echo ucfirst($booking['booking_status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo format_date($booking['booking_date']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="padding: 20px; text-align: center; color: #999;">No bookings found</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 CineBook - Admin Panel</p>
    </footer>
</body>
</html>
