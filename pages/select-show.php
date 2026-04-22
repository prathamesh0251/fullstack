<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$movie_id = $_GET['movie_id'] ?? null;

if (!$movie_id) {
    header("Location: movies.php");
    exit();
}

$movie = get_movie_by_id($conn, $movie_id);
$shows = get_shows_by_movie($conn, $movie_id);

if (!$movie) {
    header("Location: movies.php?error=Movie not found");
    exit();
}

if (empty($shows)) {
    $no_shows = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie['title']); ?> - Select Show - CineBook</title>
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
                <?php if(is_logged_in()): ?>
                    <span style="color: white; margin-right: 10px;">
                        Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>
                    </span>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="container">
        <div style="margin: 30px 0;">
            <a href="movies.php" style="color: #667eea; text-decoration: none; margin-bottom: 20px; display: inline-block;">
                ← Back to Movies
            </a>

            <div class="grid-2">
                <div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <div style="width: 100%; height: 300px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                                border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 64px;">
                        🎬
                    </div>
                </div>

                <div>
                    <h1 style="color: #333; margin-bottom: 10px;"><?php echo htmlspecialchars($movie['title']); ?></h1>
                    <div style="margin-bottom: 20px;">
                        <p style="color: #667eea; font-weight: 600; text-transform: uppercase; margin-bottom: 10px;">
                            <?php echo htmlspecialchars($movie['genre']); ?>
                        </p>
                        <p style="color: #ffc107; font-size: 18px; font-weight: bold; margin-bottom: 10px;">
                            ⭐ <?php echo $movie['rating']; ?>/10
                        </p>
                    </div>

                    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <h3 style="margin-bottom: 10px;">About this movie</h3>
                        <p style="color: #666; line-height: 1.6;">
                            <?php echo htmlspecialchars($movie['description']); ?>
                        </p>
                        <p style="color: #999; margin-top: 15px; font-size: 14px;">
                            <strong>Duration:</strong> <?php echo $movie['duration']; ?> minutes<br>
                            <strong>Release Date:</strong> <?php echo format_date($movie['release_date']); ?>
                        </p>
                    </div>

                    <?php if (!is_logged_in()): ?>
                        <div class="alert alert-info">
                            Please <a href="login.php" style="color: #0c5460; text-decoration: underline;">login</a> to book tickets.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div style="margin-top: 40px;">
                <h2 style="color: #333; margin-bottom: 20px;">Select a Show</h2>

                <?php if (isset($no_shows)): ?>
                    <div class="alert alert-warning">
                        No shows available for this movie at the moment.
                    </div>
                <?php else: ?>
                    <form method="POST" action="seat-selection.php" id="show-form">
                        <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
                        <input type="hidden" name="show_id" id="selected_show" value="">

                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px; margin-bottom: 30px;">
                            <?php foreach($shows as $show): ?>
                                <div class="show-item" onclick="selectShow(this, <?php echo $show['show_id']; ?>, '<?php echo format_datetime($show['show_time']); ?>')">
                                    <div class="show-time">
                                        <?php echo date('H:i', strtotime($show['show_time'])); ?>
                                    </div>
                                    <div class="show-theater">
                                        📍 <?php echo htmlspecialchars($show['theater_name']); ?>
                                    </div>
                                    <div class="show-seats">
                                        ✅ <?php echo $show['available_seats']; ?> seats available
                                    </div>
                                    <div style="margin-top: 10px; color: #999; font-size: 12px;">
                                        <?php echo format_datetime($show['show_time']); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" class="btn btn-primary btn-large" style="width: 100%; padding: 15px; font-size: 16px;" 
                                id="proceed-btn" disabled>
                            Proceed to Seat Selection
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 CineBook - Movie Ticket Booking System</p>
    </footer>

    <script>
        function selectShow(element, showId, showTime) {
            if (!<?php echo is_logged_in() ? 'true' : 'false'; ?>) {
                alert('Please login to book tickets');
                window.location.href = 'login.php';
                return;
            }

            // Remove selection from all items
            document.querySelectorAll('.show-item').forEach(item => {
                item.classList.remove('selected');
            });

            // Add selection to clicked item
            element.classList.add('selected');
            document.getElementById('selected_show').value = showId;
            document.getElementById('proceed-btn').disabled = false;
        }
    </script>
</body>
</html>
