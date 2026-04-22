<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

require_admin();

$error = '';
$success = '';
$action = $_GET['action'] ?? 'list';
$movie_id = $_GET['id'] ?? null;

// Handle Add/Edit/Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $del_id = $_POST['delete_id'];
        $delete_query = "DELETE FROM movies WHERE movie_id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $del_id);
        if ($stmt->execute()) {
            $success = 'Movie deleted successfully';
        } else {
            $error = 'Failed to delete movie';
        }
    } else {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $genre = trim($_POST['genre'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $rating = trim($_POST['rating'] ?? '');
        $release_date = trim($_POST['release_date'] ?? '');

        if (empty($title) || empty($genre) || empty($duration) || empty($rating)) {
            $error = 'All fields are required';
        } else {
            if ($_POST['action'] === 'add') {
                $insert_query = "INSERT INTO movies (title, description, genre, duration, rating, release_date, status) 
                               VALUES (?, ?, ?, ?, ?, ?, 'active')";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("sssids", $title, $description, $genre, $duration, $rating, $release_date);
                if ($stmt->execute()) {
                    $success = 'Movie added successfully';
                    $_POST = [];
                } else {
                    $error = 'Failed to add movie';
                }
            } else {
                $movie_id = $_POST['movie_id'];
                $update_query = "UPDATE movies SET title = ?, description = ?, genre = ?, duration = ?, rating = ?, release_date = ? WHERE movie_id = ?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param("sssidsi", $title, $description, $genre, $duration, $rating, $release_date, $movie_id);
                if ($stmt->execute()) {
                    $success = 'Movie updated successfully';
                    unset($_GET['action']);
                    unset($_GET['id']);
                } else {
                    $error = 'Failed to update movie';
                }
            }
        }
    }
}

$movies = get_all_movies($conn);
$edit_movie = null;
if ($action === 'edit' && $movie_id) {
    $edit_movie = get_movie_by_id($conn, $movie_id);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Movies - CineBook Admin</title>
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
                    <li><a href="manage-movies.php" class="active">🎬 Movies</a></li>
                    <li><a href="manage-shows.php">⏰ Shows</a></li>
                    <li><a href="manage-seats.php">🎫 Seats</a></li>
                    <li><a href="view-bookings.php">📋 Bookings</a></li>
                    <li><a href="reports.php">📈 Reports</a></li>
                </ul>
            </div>

            <div>
                <h1 style="color: #333; margin-bottom: 30px;">Manage Movies</h1>

                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <?php if($action === 'add' || $action === 'edit'): ?>
                    <div class="card" style="margin-bottom: 30px;">
                        <div class="card-header">
                            <h2><?php echo $action === 'add' ? 'Add New Movie' : 'Edit Movie'; ?></h2>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="action" value="<?php echo $action; ?>">
                            <?php if($action === 'edit' && $edit_movie): ?>
                                <input type="hidden" name="movie_id" value="<?php echo $edit_movie['movie_id']; ?>">
                            <?php endif; ?>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label for="title">Movie Title</label>
                                    <input type="text" id="title" name="title" required 
                                           value="<?php echo htmlspecialchars($edit_movie['title'] ?? $_POST['title'] ?? ''); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="genre">Genre</label>
                                    <input type="text" id="genre" name="genre" required
                                           value="<?php echo htmlspecialchars($edit_movie['genre'] ?? $_POST['genre'] ?? ''); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="duration">Duration (minutes)</label>
                                    <input type="number" id="duration" name="duration" required
                                           value="<?php echo $edit_movie['duration'] ?? $_POST['duration'] ?? ''; ?>">
                                </div>

                                <div class="form-group">
                                    <label for="rating">Rating (out of 10)</label>
                                    <input type="number" id="rating" name="rating" step="0.1" required
                                           value="<?php echo $edit_movie['rating'] ?? $_POST['rating'] ?? ''; ?>">
                                </div>

                                <div class="form-group">
                                    <label for="release_date">Release Date</label>
                                    <input type="date" id="release_date" name="release_date"
                                           value="<?php echo $edit_movie['release_date'] ?? $_POST['release_date'] ?? ''; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($edit_movie['description'] ?? $_POST['description'] ?? ''); ?></textarea>
                            </div>

                            <div style="display: flex; gap: 10px;">
                                <button type="submit" class="btn btn-primary">
                                    <?php echo $action === 'add' ? 'Add Movie' : 'Update Movie'; ?>
                                </button>
                                <a href="manage-movies.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h2>All Movies</h2>
                        <a href="manage-movies.php?action=add" class="btn btn-primary" style="float: right;">+ Add New Movie</a>
                    </div>

                    <?php if(!empty($movies)): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Genre</th>
                                    <th>Duration</th>
                                    <th>Rating</th>
                                    <th>Release Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($movies as $movie): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($movie['title']); ?></td>
                                        <td><?php echo htmlspecialchars($movie['genre']); ?></td>
                                        <td><?php echo $movie['duration']; ?> min</td>
                                        <td>⭐ <?php echo $movie['rating']; ?>/10</td>
                                        <td><?php echo format_date($movie['release_date']); ?></td>
                                        <td>
                                            <a href="manage-movies.php?action=edit&id=<?php echo $movie['movie_id']; ?>" class="btn btn-small btn-secondary">Edit</a>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                                <input type="hidden" name="delete_id" value="<?php echo $movie['movie_id']; ?>">
                                                <button type="submit" class="btn btn-small btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="padding: 20px; text-align: center; color: #999;">No movies found</p>
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
