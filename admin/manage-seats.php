<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

require_admin();

$show_id = $_GET['show_id'] ?? null;
$shows_list = $conn->query("SELECT * FROM shows ORDER BY show_time DESC")->fetch_all(MYSQLI_ASSOC);
$selected_show = null;
$seats = [];

if ($show_id) {
    $selected_show = get_show_by_id($conn, $show_id);
    $seats = get_seats_by_show($conn, $show_id);
}

// Handle seat status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seat_id']) && isset($_POST['status'])) {
    $seat_id = $_POST['seat_id'];
    $status = $_POST['status'];
    
    $update_query = "UPDATE seats SET seat_status = ? WHERE seat_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $status, $seat_id);
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Seats - CineBook Admin</title>
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
                    <li><a href="manage-seats.php" class="active">🎫 Seats</a></li>
                    <li><a href="view-bookings.php">📋 Bookings</a></li>
                    <li><a href="reports.php">📈 Reports</a></li>
                </ul>
            </div>

            <div>
                <h1 style="color: #333; margin-bottom: 30px;">Manage Seats</h1>

                <div class="card" style="margin-bottom: 30px;">
                    <div class="card-header">
                        <h2>Select a Show</h2>
                    </div>

                    <form method="GET">
                        <div style="display: flex; gap: 10px;">
                            <select name="show_id" style="flex: 1;" onchange="this.form.submit();" required>
                                <option value="">-- Select Show --</option>
                                <?php foreach($shows_list as $show): ?>
                                    <option value="<?php echo $show['show_id']; ?>" 
                                            <?php echo ($show_id == $show['show_id']) ? 'selected' : ''; ?>>
                                        <?php 
                                        $movie = get_movie_by_id($conn, $show['movie_id']);
                                        echo htmlspecialchars($movie['title']) . ' - ' . format_datetime($show['show_time']);
                                        ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>

                <?php if($selected_show && !empty($seats)): ?>
                    <div class="card">
                        <div class="card-header">
                            <h2>Seat Layout for <?php echo htmlspecialchars($selected_show['title']); ?></h2>
                        </div>

                        <div style="padding: 20px;">
                            <div class="seat-legend" style="margin-bottom: 30px;">
                                <div class="legend-item">
                                    <div class="legend-seat available"></div>
                                    <span>Available</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-seat booked"></div>
                                    <span>Booked</span>
                                </div>
                            </div>

                            <div class="seats-grid">
                                <?php
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

                                foreach ($seat_layout as $row => $row_seats):
                                ?>
                                    <div class="seat-row">
                                        <div class="seat-label"><?php echo $row; ?></div>
                                        <?php foreach ($row_seats as $seat): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="show_id" value="<?php echo $show_id; ?>">
                                                <input type="hidden" name="seat_id" value="<?php echo $seat['seat_id']; ?>">
                                                <input type="hidden" name="status" value="<?php echo $seat['seat_status'] === 'available' ? 'booked' : 'available'; ?>">
                                                <button type="submit" class="seat <?php echo $seat['seat_status']; ?>" 
                                                        title="Click to toggle seat status">
                                                    <?php echo substr($seat['seat_number'], 1); ?>
                                                </button>
                                            </form>
                                        <?php endforeach; ?>
                                    </div>
                                <?php
                                endforeach;
                                ?>
                            </div>
                        </div>
                    </div>
                <?php elseif($show_id): ?>
                    <div class="alert alert-info">No seats found for this show</div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 CineBook - Admin Panel</p>
    </footer>

    <script src="../js/script.js"></script>
</body>
</html>
