<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$movies = get_all_movies($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movies - CineBook</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">🎬 CineBook</div>
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="movies.php">Movies</a></li>
                <?php if(is_logged_in()): ?>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="bookings.php">My Bookings</a></li>
                <?php endif; ?>
            </ul>
            <div class="auth-buttons">
                <?php if(is_logged_in()): ?>
                    <span style="color: white; margin-right: 10px;">
                        Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>
                    </span>
                    <a href="logout.php">Logout</a>
                    <?php if(is_admin()): ?>
                        <a href="../admin/dashboard.php">Admin Panel</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="container">
        <div style="margin: 30px 0;">
            <h1 style="color: #333; margin-bottom: 20px;">Now Showing Movies</h1>
            
            <div style="margin-bottom: 20px;">
                <input type="text" id="search-movies" placeholder="Search movies by title or genre..." 
                       style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 4px; font-size: 14px;"
                       onkeyup="filterMovies(this.value)">
            </div>

            <?php if(!empty($movies)): ?>
                <div class="grid">
                    <?php foreach($movies as $movie): ?>
                        <div class="movie-card">
                            <div class="movie-poster">🎬</div>
                            <div class="movie-info">
                                <div class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></div>
                                <div class="movie-genre"><?php echo htmlspecialchars($movie['genre']); ?></div>
                                <div class="movie-rating">⭐ <?php echo $movie['rating']; ?>/10</div>
                                <div class="movie-description"><?php echo htmlspecialchars($movie['description']); ?></div>
                                <p style="font-size: 12px; color: #999; margin-top: 8px;">
                                    ⏱️ Duration: <?php echo $movie['duration']; ?> minutes
                                </p>
                                <a href="select-show.php?movie_id=<?php echo $movie['movie_id']; ?>" 
                                   class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                                    Book Tickets
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No movies available at the moment.</div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 CineBook - Movie Ticket Booking System</p>
    </footer>

    <script src="../js/script.js"></script>
</body>
</html>
