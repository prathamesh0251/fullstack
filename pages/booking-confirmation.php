<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

require_login();

$show_id = $_POST['show_id'] ?? null;
$seats = $_POST['selected_seats'] ?? '';

if (!$show_id || empty($seats)) {
    header("Location: movies.php");
    exit();
}

$seat_list = explode(',', $seats);
$show = get_show_by_id($conn, $show_id);

if (!$show) {
    header("Location: movies.php?error=Show not found");
    exit();
}

// Verify all seats are available
$seats_unavailable = false;
foreach ($seat_list as $seat_number) {
    if (!check_seat_availability($conn, $show_id, trim($seat_number))) {
        $seats_unavailable = true;
        break;
    }
}

if ($seats_unavailable) {
    header("Location: seat-selection.php?error=One or more seats are no longer available");
    exit();
}

// Create the booking
$total_amount = count($seat_list) * 300;
$booking_result = create_booking($conn, $_SESSION['user_id'], $show_id, array_map('trim', $seat_list), $total_amount);

if (!$booking_result['success']) {
    header("Location: seat-selection.php?error=Booking failed");
    exit();
}

$booking_id = $booking_result['booking_id'];
$booking_details = get_booking_details($conn, $booking_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - CineBook</title>
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
        <div class="confirmation-container">
            <div class="confirmation-icon">✅</div>
            <h1 class="confirmation-title">Booking Confirmed!</h1>
            <p class="confirmation-message">Your movie tickets have been successfully booked.</p>

            <div class="ticket-details" id="ticket-details">
                <div class="ticket-detail-row">
                    <span style="font-weight: 600;">Booking ID:</span>
                    <span><?php echo '#TKT' . str_pad($booking_id, 6, '0', STR_PAD_LEFT); ?></span>
                </div>

                <div class="ticket-detail-row">
                    <span style="font-weight: 600;">Movie:</span>
                    <span><?php echo htmlspecialchars($show['title']); ?></span>
                </div>

                <div class="ticket-detail-row">
                    <span style="font-weight: 600;">Date & Time:</span>
                    <span><?php echo format_datetime($show['show_time']); ?></span>
                </div>

                <div class="ticket-detail-row">
                    <span style="font-weight: 600;">Theater:</span>
                    <span><?php echo htmlspecialchars($show['theater_name']); ?></span>
                </div>

                <div class="ticket-detail-row">
                    <span style="font-weight: 600;">Seats:</span>
                    <span>
                        <?php 
                        $seat_numbers = array_unique(array_column($booking_details, 'seat_number'));
                        echo htmlspecialchars(implode(', ', $seat_numbers));
                        ?>
                    </span>
                </div>

                <div class="ticket-detail-row">
                    <span style="font-weight: 600;">Total Seats:</span>
                    <span><?php echo count($booking_details); ?></span>
                </div>

                <div class="ticket-detail-row">
                    <span style="font-weight: 600;">Price per Seat:</span>
                    <span>₹300</span>
                </div>

                <div class="ticket-detail-row" style="font-size: 18px; font-weight: bold; border-top: 2px solid #ddd; padding-top: 15px;">
                    <span>Total Amount:</span>
                    <span>₹<?php echo $total_amount; ?></span>
                </div>

                <div style="margin-top: 20px; padding: 15px; background-color: #d4edda; border-radius: 4px; color: #155724;">
                    <strong>✓ Payment Status:</strong> Completed
                </div>

                <div style="margin-top: 15px; padding: 15px; background-color: #d1ecf1; border-radius: 4px; color: #0c5460; font-size: 14px;">
                    <strong>Note:</strong> You will receive a confirmation email shortly with your e-tickets. Please arrive 15 minutes before the show time.
                </div>
            </div>

            <div style="display: flex; gap: 10px; justify-content: center; margin-top: 30px; flex-wrap: wrap;">
                <button onclick="printTicket()" class="btn btn-secondary">
                    🖨️ Print Ticket
                </button>
                <button onclick="downloadTicket()" class="btn btn-secondary">
                    📥 Download Ticket
                </button>
                <a href="bookings.php" class="btn btn-primary">
                    📋 View All Bookings
                </a>
            </div>

            <div style="margin-top: 30px; text-align: center;">
                <a href="../index.php" class="btn btn-secondary">
                    ← Back to Home
                </a>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 CineBook - Movie Ticket Booking System</p>
    </footer>

    <script src="../js/script.js"></script>
</body>
</html>
