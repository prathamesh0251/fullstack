<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$movies = get_all_movies($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineBook - Movie Ticket Booking System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">🎬 CineBook</div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="pages/movies.php">Movies</a></li>
                <?php if(is_logged_in()): ?>
                    <li><a href="pages/profile.php">Profile</a></li>
                    <li><a href="pages/bookings.php">My Bookings</a></li>
                <?php endif; ?>
            </ul>
            <div class="auth-buttons">
                <?php if(is_logged_in()): ?>
                    <span style="color: white; margin-right: 10px;">
                        Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>
                    </span>
                    <a href="pages/logout.php">Logout</a>
                    <?php if(is_admin()): ?>
                        <a href="admin/dashboard.php">Admin Panel</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="pages/login.php">Login</a>
                    <a href="pages/register.php">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="container">
        <section style="margin: 40px 0; text-align: center;">
            <h1 style="font-size: 48px; margin-bottom: 10px; color: #667eea;">Welcome to CineBook</h1>
            <p style="font-size: 18px; color: #666; margin-bottom: 30px;">
                Your ultimate destination for booking movie tickets online
            </p>
        </section>

        <section style="background: white; border-radius: 8px; padding: 30px; margin-bottom: 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 20px; color: #333;">Now Showing</h2>
            
            <?php if(!empty($movies)): ?>
                <div class="grid">
                    <?php foreach($movies as $movie): ?>
                        <div class="movie-card" onclick="window.location.href='pages/select-show.php?movie_id=<?php echo $movie['movie_id']; ?>'">
                            <div class="movie-poster">🎬</div>
                            <div class="movie-info">
                                <div class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></div>
                                <div class="movie-genre"><?php echo htmlspecialchars($movie['genre']); ?></div>
                                <div class="movie-rating">⭐ <?php echo $movie['rating']; ?>/10</div>
                                <div class="movie-description"><?php echo substr(htmlspecialchars($movie['description']), 0, 80); ?>...</div>
                                <p style="font-size: 12px; color: #999; margin-top: 10px;">
                                    Duration: <?php echo $movie['duration']; ?> minutes
                                </p>
                                <a href="pages/select-show.php?movie_id=<?php echo $movie['movie_id']; ?>" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                                    Book Tickets
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #999; padding: 40px;">
                    No movies available at the moment.
                </p>
            <?php endif; ?>
        </section>

        <section style="background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 20px; color: #333;">Why Choose CineBook?</h2>
            <div class="grid">
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 40px; margin-bottom: 10px;">🎟️</div>
                    <h3>Easy Booking</h3>
                    <p>Book your favorite movie seats in just a few clicks</p>
                </div>
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 40px; margin-bottom: 10px;">💳</div>
                    <h3>Secure Payment</h3>
                    <p>Safe and secure payment processing</p>
                </div>
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 40px; margin-bottom: 10px;">📱</div>
                    <h3>Mobile Friendly</h3>
                    <p>Book from anywhere, anytime on any device</p>
                </div>
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 40px; margin-bottom: 10px;">⚡</div>
                    <h3>Instant Confirmation</h3>
                    <p>Get your e-ticket instantly after booking</p>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 CineBook - Movie Ticket Booking System. All rights reserved.</p>
        <p>For support, contact: support@cinebook.com | Phone: +1-800-CINEMA</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
