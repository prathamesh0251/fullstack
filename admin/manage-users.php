<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

require_admin();

$users_query = "SELECT u.* FROM users u WHERE u.user_type = 'user' ORDER BY u.created_at DESC";
$users = $conn->query($users_query)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - CineBook Admin</title>
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
                    <li><a href="reports.php">📈 Reports</a></li>
                    <li><a href="manage-users.php" class="active">👥 Users</a></li>
                </ul>
            </div>

            <div>
                <h1 style="color: #333; margin-bottom: 30px;">Manage Users</h1>

                <div class="card">
                    <div class="card-header">
                        <h2>All Users</h2>
                    </div>

                    <?php if(!empty($users)): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Member Since</th>
                                    <th>Total Bookings</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($users as $user): 
                                    $booking_count = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE user_id = " . $user['user_id'])->fetch_assoc()['count'];
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                                        <td><?php echo format_date($user['created_at']); ?></td>
                                        <td>
                                            <span style="background-color: #e7f3ff; color: #0066cc; padding: 4px 8px; border-radius: 4px;">
                                                <?php echo $booking_count; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="padding: 20px; text-align: center; color: #999;">No users found</p>
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
