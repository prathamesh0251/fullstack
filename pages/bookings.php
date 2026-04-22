<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

require_login();

$bookings = get_user_bookings($conn, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - CineBook</title>
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
            <h1 style="color: #333; margin-bottom: 30px;">My Bookings</h1>

            <?php if(!empty($bookings)): ?>
                <div class="card">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Movie</th>
                                <th>Show Time</th>
                                <th>Theater</th>
                                <th>Seats</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Booked On</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($bookings as $booking): ?>
                                <tr>
                                    <td><strong><?php echo '#TKT' . str_pad($booking['booking_id'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                                    <td><?php echo htmlspecialchars($booking['title']); ?></td>
                                    <td><?php echo format_datetime($booking['show_time']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['theater_name']); ?></td>
                                    <td><?php echo $booking['seat_count']; ?></td>
                                    <td><strong>₹<?php echo $booking['total_amount']; ?></strong></td>
                                    <td>
                                        <span style="padding: 4px 8px; border-radius: 4px; background-color: #d4edda; color: #155724; font-size: 12px; font-weight: 600;">
                                            <?php echo ucfirst($booking['booking_status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo format_date($booking['booking_date']); ?></td>
                                    <td>
                                        <a href="booking-details.php?booking_id=<?php echo $booking['booking_id']; ?>" class="btn btn-small btn-primary">
                                            Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <h3>No Bookings Yet</h3>
                    <p>You haven't made any bookings yet. <a href="movies.php" style="color: #0c5460; text-decoration: underline; font-weight: 600;">Browse our movie collection</a> and book your favorite movie!</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 CineBook - Movie Ticket Booking System</p>
    </footer>

    <script src="../js/script.js"></script>
</body>
</html>
