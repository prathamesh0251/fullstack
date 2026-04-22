<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

require_admin();

// Get report data
$movies_report = $conn->query("
    SELECT m.movie_id, m.title, COUNT(b.booking_id) as bookings, SUM(b.total_amount) as revenue, COUNT(DISTINCT bd.seat_id) as tickets_sold
    FROM movies m
    LEFT JOIN shows sh ON m.movie_id = sh.movie_id
    LEFT JOIN bookings b ON sh.show_id = b.show_id
    LEFT JOIN booking_details bd ON b.booking_id = bd.booking_id
    GROUP BY m.movie_id, m.title
    ORDER BY tickets_sold DESC
")->fetch_all(MYSQLI_ASSOC);

// Monthly revenue
$monthly_revenue = $conn->query("
    SELECT DATE_FORMAT(b.booking_date, '%Y-%m') as month, COUNT(*) as bookings, SUM(b.total_amount) as revenue
    FROM bookings b
    WHERE b.payment_status = 'completed'
    GROUP BY DATE_FORMAT(b.booking_date, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12
")->fetch_all(MYSQLI_ASSOC);

// Top shows
$top_shows = $conn->query("
    SELECT sh.show_id, m.title, sh.show_time, sh.theater_name, COUNT(bd.seat_id) as tickets_sold, SUM(b.total_amount) as revenue
    FROM shows sh
    JOIN movies m ON sh.movie_id = m.movie_id
    JOIN bookings b ON sh.show_id = b.show_id
    JOIN booking_details bd ON b.booking_id = bd.booking_id
    GROUP BY sh.show_id
    ORDER BY tickets_sold DESC
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - CineBook Admin</title>
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
                    <li><a href="view-bookings.php">📋 Bookings</a></li>
                    <li><a href="reports.php" class="active">📈 Reports</a></li>
                </ul>
            </div>

            <div>
                <h1 style="color: #333; margin-bottom: 30px;">Reports & Analytics</h1>

                <!-- Movies Report -->
                <div class="card" style="margin-bottom: 30px;">
                    <div class="card-header">
                        <h2>Tickets Sold Per Movie</h2>
                    </div>

                    <?php if(!empty($movies_report)): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Movie</th>
                                    <th>Tickets Sold</th>
                                    <th>Total Bookings</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($movies_report as $movie): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($movie['title']); ?></td>
                                        <td><strong><?php echo $movie['tickets_sold'] ?? 0; ?></strong></td>
                                        <td><?php echo $movie['bookings'] ?? 0; ?></td>
                                        <td>₹<?php echo number_format($movie['revenue'] ?? 0, 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="padding: 20px; text-align: center; color: #999;">No data available</p>
                    <?php endif; ?>
                </div>

                <!-- Top Shows Report -->
                <div class="card" style="margin-bottom: 30px;">
                    <div class="card-header">
                        <h2>Top Performing Shows</h2>
                    </div>

                    <?php if(!empty($top_shows)): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Movie</th>
                                    <th>Date & Time</th>
                                    <th>Theater</th>
                                    <th>Tickets Sold</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($top_shows as $show): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($show['title']); ?></td>
                                        <td><?php echo format_datetime($show['show_time']); ?></td>
                                        <td><?php echo htmlspecialchars($show['theater_name']); ?></td>
                                        <td><strong><?php echo $show['tickets_sold']; ?></strong></td>
                                        <td>₹<?php echo number_format($show['revenue'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="padding: 20px; text-align: center; color: #999;">No data available</p>
                    <?php endif; ?>
                </div>

                <!-- Monthly Revenue Report -->
                <div class="card">
                    <div class="card-header">
                        <h2>Monthly Revenue</h2>
                    </div>

                    <?php if(!empty($monthly_revenue)): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Bookings</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($monthly_revenue as $month): ?>
                                    <tr>
                                        <td><?php echo date('M Y', strtotime($month['month'] . '-01')); ?></td>
                                        <td><?php echo $month['bookings']; ?></td>
                                        <td><strong>₹<?php echo number_format($month['revenue'], 2); ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="padding: 20px; text-align: center; color: #999;">No data available</p>
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
