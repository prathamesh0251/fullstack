<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

require_admin();

$error = '';
$success = '';

// Handle Add/Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $del_id = $_POST['delete_id'];
        $delete_query = "DELETE FROM shows WHERE show_id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $del_id);
        if ($stmt->execute()) {
            $success = 'Show deleted successfully';
        } else {
            $error = 'Failed to delete show';
        }
    } else {
        $movie_id = $_POST['movie_id'] ?? '';
        $show_time = $_POST['show_time'] ?? '';
        $theater_name = $_POST['theater_name'] ?? '';
        $total_seats = $_POST['total_seats'] ?? '';

        if (empty($movie_id) || empty($show_time) || empty($theater_name) || empty($total_seats)) {
            $error = 'All fields are required';
        } else {
            $insert_query = "INSERT INTO shows (movie_id, show_time, theater_name, total_seats, available_seats) 
                           VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("isiii", $movie_id, $show_time, $theater_name, $total_seats, $total_seats);
            if ($stmt->execute()) {
                $success = 'Show added successfully';
                $_POST = [];
                
                // Create seats for this show
                $show_id = $stmt->insert_id;
                $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
                $cols = 10;
                
                for ($r = 0; $r < count($rows); $r++) {
                    for ($c = 1; $c <= $cols; $c++) {
                        $seat_num = $rows[$r] . $c;
                        $seat_insert = "INSERT INTO seats (show_id, seat_number, seat_status) VALUES (?, ?, 'available')";
                        $s_stmt = $conn->prepare($seat_insert);
                        $s_stmt->bind_param("is", $show_id, $seat_num);
                        $s_stmt->execute();
                    }
                }
            } else {
                $error = 'Failed to add show';
            }
        }
    }
}

$movies = get_all_movies($conn);
$shows_query = "SELECT s.*, m.title FROM shows s JOIN movies m ON s.movie_id = m.movie_id ORDER BY s.show_time DESC";
$shows = $conn->query($shows_query)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Shows - CineBook Admin</title>
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
                    <li><a href="manage-shows.php" class="active">⏰ Shows</a></li>
                    <li><a href="manage-seats.php">🎫 Seats</a></li>
                    <li><a href="view-bookings.php">📋 Bookings</a></li>
                    <li><a href="reports.php">📈 Reports</a></li>
                </ul>
            </div>

            <div>
                <h1 style="color: #333; margin-bottom: 30px;">Manage Shows</h1>

                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <div class="card" style="margin-bottom: 30px;">
                    <div class="card-header">
                        <h2>Add New Show</h2>
                    </div>

                    <form method="POST">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label for="movie_id">Movie</label>
                                <select id="movie_id" name="movie_id" required>
                                    <option value="">-- Select Movie --</option>
                                    <?php foreach($movies as $movie): ?>
                                        <option value="<?php echo $movie['movie_id']; ?>">
                                            <?php echo htmlspecialchars($movie['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="show_time">Date & Time</label>
                                <input type="datetime-local" id="show_time" name="show_time" required>
                            </div>

                            <div class="form-group">
                                <label for="theater_name">Theater Name</label>
                                <input type="text" id="theater_name" name="theater_name" placeholder="e.g., Theater A" required>
                            </div>

                            <div class="form-group">
                                <label for="total_seats">Total Seats</label>
                                <input type="number" id="total_seats" name="total_seats" value="100" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Add Show</button>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>All Shows</h2>
                    </div>

                    <?php if(!empty($shows)): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Movie</th>
                                    <th>Date & Time</th>
                                    <th>Theater</th>
                                    <th>Total Seats</th>
                                    <th>Available</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($shows as $show): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($show['title']); ?></td>
                                        <td><?php echo format_datetime($show['show_time']); ?></td>
                                        <td><?php echo htmlspecialchars($show['theater_name']); ?></td>
                                        <td><?php echo $show['total_seats']; ?></td>
                                        <td><?php echo $show['available_seats']; ?></td>
                                        <td>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                                <input type="hidden" name="delete_id" value="<?php echo $show['show_id']; ?>">
                                                <button type="submit" class="btn btn-small btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="padding: 20px; text-align: center; color: #999;">No shows found</p>
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
