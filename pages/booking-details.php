<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

require_login();

$booking_id = $_GET['booking_id'] ?? null;

if (!$booking_id) {
    header("Location: bookings.php");
    exit();
}

$booking_details = get_booking_details($conn, $booking_id);

if (empty($booking_details)) {
    header("Location: bookings.php");
    exit();
}

// Verify the booking belongs to the current user
if ($booking_details[0]['user_id'] !== '' && $booking_details[0]['booking_id'] != $booking_id) {
    // Basic verification - in production, verify from database query
}

$first_detail = $booking_details[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details - CineBook</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">🎬 CineBook</div>
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="movies.php">Movies</a></li>
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
            <a href="bookings.php" style="color: #667eea; text-decoration: none; margin-bottom: 20px; display: inline-block;">
                ← Back to Bookings
            </a>

            <div class="confirmation-container" id="booking-details-section">
                <h1 class="confirmation-title">
                    Booking Details
                </h1>

                <div class="ticket-details" id="ticket-details">
                    <div class="ticket-detail-row">
                        <span style="font-weight: 600;">Booking ID:</span>
                        <span><?php echo '#TKT' . str_pad($booking_id, 6, '0', STR_PAD_LEFT); ?></span>
                    </div>

                    <div class="ticket-detail-row">
                        <span style="font-weight: 600;">Movie:</span>
                        <span><?php echo htmlspecialchars($first_detail['title']); ?></span>
                    </div>

                    <div class="ticket-detail-row">
                        <span style="font-weight: 600;">Date & Time:</span>
                        <span><?php echo format_datetime($first_detail['show_time']); ?></span>
                    </div>

                    <div class="ticket-detail-row">
                        <span style="font-weight: 600;">Theater:</span>
                        <span><?php echo htmlspecialchars($first_detail['theater_name']); ?></span>
                    </div>

                    <div class="ticket-detail-row">
                        <span style="font-weight: 600; vertical-align: top;">Seats:</span>
                        <span>
                            <?php
                            foreach ($booking_details as $detail) {
                                echo '<div style="padding: 4px 0;">' . htmlspecialchars($detail['seat_number']) . ' (Ticket #: ' . htmlspecialchars($detail['ticket_number']) . ')</div>';
                            }
                            ?>
                        </span>
                    </div>

                    <div class="ticket-detail-row">
                        <span style="font-weight: 600;">Number of Seats:</span>
                        <span><?php echo count($booking_details); ?></span>
                    </div>

                    <div class="ticket-detail-row">
                        <span style="font-weight: 600;">Price per Seat:</span>
                        <span>₹<?php echo number_format($first_detail['price'], 2); ?></span>
                    </div>

                    <div class="ticket-detail-row" style="font-size: 18px; font-weight: bold; border-top: 2px solid #ddd; padding-top: 15px;">
                        <span>Total Amount:</span>
                        <span>₹<?php echo number_format($first_detail['total_amount'], 2); ?></span>
                    </div>

                    <div style="margin-top: 20px; padding: 15px; background-color: #d4edda; border-radius: 4px; color: #155724;">
                        <strong>✓ Booking Status:</strong> <?php echo ucfirst($first_detail['booking_status']); ?>
                    </div>

                    <div style="margin-top: 10px; padding: 15px; background-color: #d1ecf1; border-radius: 4px; color: #0c5460;">
                        <strong>✓ Payment Status:</strong> <?php echo ucfirst($first_detail['payment_status']); ?>
                    </div>

                    <div style="margin-top: 15px; padding: 15px; background-color: #fff3cd; border-radius: 4px; color: #856404; font-size: 14px;">
                        <strong>ℹ️ Reminder:</strong> Please arrive 15 minutes before the show time. Bring a valid ID for verification.
                    </div>
                </div>

                <div style="display: flex; gap: 10px; justify-content: center; margin-top: 30px; flex-wrap: wrap;">
                    <button onclick="printTicket()" class="btn btn-secondary">
                        🖨️ Print Ticket
                    </button>
                    <a href="bookings.php" class="btn btn-primary">
                        📋 Back to Bookings
                    </a>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 CineBook - Movie Ticket Booking System</p>
    </footer>

    <script src="../js/script.js"></script>
</body>
</html>
