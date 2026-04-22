<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

require_login();

$show_id = $_POST['show_id'] ?? null;
$movie_id = $_POST['movie_id'] ?? null;

if (!$show_id || !$movie_id) {
    header("Location: movies.php");
    exit();
}

$show = get_show_by_id($conn, $show_id);
$seats = get_seats_by_show($conn, $show_id);

if (!$show) {
    header("Location: movies.php?error=Show not found");
    exit();
}

// Organize seats by row
$seat_layout = [];
foreach ($seats as $seat) {
    $row = substr($seat['seat_number'], 0, 1);
    if (!isset($seat_layout[$row])) {
        $seat_layout[$row] = [];
    }
    $seat_layout[$row][] = $seat;
}

ksort($seat_layout);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seats - CineBook</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">🎬 CineBook</div>
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="movies.php">Movies</a></li>
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
            <a href="select-show.php?movie_id=<?php echo $movie_id; ?>" style="color: #667eea; text-decoration: none; margin-bottom: 20px; display: inline-block;">
                ← Back to Shows
            </a>

            <div class="grid-2">
                <div>
                    <h1 style="color: #333; margin-bottom: 10px;"><?php echo htmlspecialchars($show['title']); ?></h1>
                    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <p style="margin-bottom: 10px;">
                            <strong>Date & Time:</strong> <?php echo format_datetime($show['show_time']); ?>
                        </p>
                        <p style="margin-bottom: 10px;">
                            <strong>Theater:</strong> <?php echo htmlspecialchars($show['theater_name']); ?>
                        </p>
                        <p style="margin-bottom: 0;">
                            <strong>Price per Seat:</strong> ₹300
                        </p>
                    </div>

                    <form method="POST" action="booking-confirmation.php" id="booking-form">
                        <input type="hidden" name="show_id" id="show-id" value="<?php echo $show_id; ?>">
                        <input type="hidden" name="selected_seats" id="selected-seats" value="">

                        <div class="seat-selection">
                            <div class="seat-legend">
                                <div class="legend-item">
                                    <div class="legend-seat available"></div>
                                    <span>Available</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-seat booked"></div>
                                    <span>Booked</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-seat selected"></div>
                                    <span>Selected</span>
                                </div>
                            </div>

                            <div class="screen">🎬 SCREEN 🎬</div>

                            <div class="seats-grid">
                                <?php foreach ($seat_layout as $row => $row_seats): ?>
                                    <div class="seat-row">
                                        <div class="seat-label"><?php echo $row; ?></div>
                                        <?php foreach ($row_seats as $seat): ?>
                                            <div class="seat <?php echo $seat['seat_status']; ?>" 
                                                 <?php if ($seat['seat_status'] !== 'booked'): ?> 
                                                     onclick="toggleSeat(this, '<?php echo htmlspecialchars($seat['seat_number']); ?>')" 
                                                 <?php endif; ?>>
                                                <?php echo substr($seat['seat_number'], 1); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </form>
                </div>

                <div>
                    <div class="booking-summary" id="booking-summary">
                        <!-- Updated by JavaScript -->
                    </div>
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
