# CineBook - API Documentation

This document describes the internal functions and APIs available in the CineBook system.

## Authentication Functions (`includes/auth.php`)

### register_user($conn, $email, $password, $first_name, $last_name, $phone = '')

Register a new user account.

**Parameters:**
- `$conn` (mysqli): Database connection
- `$email` (string): User email (must be unique)
- `$password` (string): Plain password (will be hashed with bcrypt)
- `$first_name` (string): User's first name
- `$last_name` (string): User's last name
- `$phone` (string, optional): User's phone number

**Returns:**
```php
[
    'success' => bool,
    'message' => string,
    'user_id' => int (only if success)
]
```

**Example:**
```php
$result = register_user($conn, 'user@example.com', 'password123', 'John', 'Doe', '9876543210');
if ($result['success']) {
    echo "User created with ID: " . $result['user_id'];
}
```

---

### login_user($conn, $email, $password)

Login a user and create session.

**Parameters:**
- `$conn` (mysqli): Database connection
- `$email` (string): User email
- `$password` (string): User password

**Returns:**
```php
[
    'success' => bool,
    'message' => string,
    'user_type' => string (if success, 'user' or 'admin')
]
```

**Sets:**
- `$_SESSION['user_id']`
- `$_SESSION['email']`
- `$_SESSION['first_name']`
- `$_SESSION['last_name']`
- `$_SESSION['user_type']`

---

### logout_user()

Destroy user session and logout.

**Returns:**
```php
['success' => true, 'message' => 'Logged out successfully']
```

---

### is_logged_in()

Check if user is currently logged in.

**Returns:** `bool` (true if logged in)

---

### is_admin()

Check if logged-in user is an admin.

**Returns:** `bool` (true if admin)

---

### require_login()

Force user to login. Redirects to login page if not authenticated.

**Usage:**
```php
require_login();
// Code here only executes if user is logged in
```

---

### require_admin()

Force admin access. Redirects if not an admin.

**Usage:**
```php
require_admin();
// Code here only executes if user is admin
```

---

## Movie/Show Functions (`includes/functions.php`)

### get_all_movies($conn)

Get all active movies.

**Returns:** Array of movie records
```php
[
    [
        'movie_id' => int,
        'title' => string,
        'description' => string,
        'genre' => string,
        'duration' => int,
        'rating' => float,
        'poster_url' => string,
        'release_date' => string,
        'status' => string,
        'created_at' => datetime
    ],
    ...
]
```

---

### get_movie_by_id($conn, $movie_id)

Get specific movie by ID.

**Parameters:**
- `$conn` (mysqli): Database connection
- `$movie_id` (int): Movie ID

**Returns:** Single movie array or null

---

### get_shows_by_movie($conn, $movie_id)

Get all future shows for a movie.

**Parameters:**
- `$conn` (mysqli): Database connection
- `$movie_id` (int): Movie ID

**Returns:** Array of show records with movie title and duration

---

### get_show_by_id($conn, $show_id)

Get specific show by ID.

**Parameters:**
- `$conn` (mysqli): Database connection
- `$show_id` (int): Show ID

**Returns:** Single show array with movie details

---

### get_seats_by_show($conn, $show_id)

Get all seats for a show.

**Parameters:**
- `$conn` (mysqli): Database connection
- `$show_id` (int): Show ID

**Returns:** Array of seat records ordered by seat number

---

### check_seat_availability($conn, $show_id, $seat_number)

Check if a specific seat is available for booking.

**Parameters:**
- `$conn` (mysqli): Database connection
- `$show_id` (int): Show ID
- `$seat_number` (string): Seat number (e.g., "A1", "B5")

**Returns:** `bool` (true if available)

---

## Booking Functions

### create_booking($conn, $user_id, $show_id, $seats, $total_amount)

Create a new booking with multiple seats.

**Parameters:**
- `$conn` (mysqli): Database connection
- `$user_id` (int): User booking the seats
- `$show_id` (int): Show ID
- `$seats` (array): Array of seat numbers ["A1", "A2", "B1"]
- `$total_amount` (float): Total amount paid

**Returns:**
```php
[
    'success' => bool,
    'booking_id' => int (if success),
    'message' => string (if failed)
]
```

**Details:**
- Uses database transactions for data integrity
- Updates seat status to 'booked'
- Creates booking_details records for each seat
- Generates unique ticket numbers
- Updates available_seats count in shows table

---

### get_booking_details($conn, $booking_id)

Get all details for a specific booking.

**Returns:** Array of booking detail records with:
- Ticket number
- Seat number
- Movie title
- Show time
- Theater name
- Price

---

### get_user_bookings($conn, $user_id)

Get all bookings for a user.

**Returns:** Array of bookings with seat count and summary

---

## Utility Functions

### format_date($date)

Format date string to readable format.

**Parameters:** `$date` (string): Date string

**Returns:** Formatted date (e.g., "Mar 20, 2026")

---

### format_datetime($datetime)

Format datetime string to readable format.

**Parameters:** `$datetime` (string): DateTime string

**Returns:** Formatted datetime (e.g., "Mar 20, 2026 14:30")

---

### get_user_info($conn, $user_id)

Get full user information.

**Returns:** User record array

---

## Database Schema Reference

### users table
```
user_id (PK, int)
email (UNIQUE, varchar)
password (varchar - hashed)
first_name (varchar)
last_name (varchar)
phone (varchar)
user_type (ENUM: 'user', 'admin')
created_at (timestamp)
updated_at (timestamp)
```

### movies table
```
movie_id (PK, int)
title (varchar)
description (text)
genre (varchar)
duration (int) - minutes
rating (decimal) - 0-10
poster_url (varchar)
release_date (date)
status (ENUM: 'active', 'inactive')
created_at (timestamp)
```

### shows table
```
show_id (PK, int)
movie_id (FK -> movies)
show_time (datetime)
theater_name (varchar)
total_seats (int)
available_seats (int)
created_at (timestamp)
```

### seats table
```
seat_id (PK, int)
show_id (FK -> shows)
seat_number (varchar) e.g., "A1"
seat_status (ENUM: 'available', 'booked')
created_at (timestamp)
```

### bookings table
```
booking_id (PK, int)
user_id (FK -> users)
show_id (FK -> shows)
booking_date (timestamp)
total_amount (decimal)
payment_status (ENUM: 'pending', 'completed', 'cancelled')
booking_status (ENUM: 'confirmed', 'cancelled', 'expired')
updated_at (timestamp)
```

### booking_details table
```
booking_detail_id (PK, int)
booking_id (FK -> bookings)
seat_id (FK -> seats)
ticket_number (varchar, UNIQUE)
price (decimal)
created_at (timestamp)
```

## Query Examples

### Get total revenue
```php
$result = $conn->query("
    SELECT SUM(total_amount) as total 
    FROM bookings 
    WHERE payment_status = 'completed'
")->fetch_assoc();
echo "Total Revenue: ₹" . $result['total'];
```

### Get most popular movie
```php
$result = $conn->query("
    SELECT m.title, COUNT(b.booking_id) as bookings
    FROM movies m
    JOIN shows sh ON m.movie_id = sh.movie_id
    JOIN bookings b ON sh.show_id = b.show_id
    GROUP BY m.movie_id
    ORDER BY bookings DESC
    LIMIT 1
")->fetch_assoc();
```

### Get bookings for a user with details
```php
$result = $conn->query("
    SELECT b.*, m.title, sh.show_time, COUNT(bd.seat_id) as seats
    FROM bookings b
    JOIN shows sh ON b.show_id = sh.show_id
    JOIN movies m ON sh.movie_id = m.movie_id
    JOIN booking_details bd ON b.booking_id = bd.booking_id
    WHERE b.user_id = 5
    GROUP BY b.booking_id
")->fetch_all(MYSQLI_ASSOC);
```

## Error Handling

All functions return consistent error responses with 'success' flag.

**Always check for success before using returned data:**
```php
$result = register_user($conn, $email, $password, $fname, $lname);

if ($result['success']) {
    // Use $result['user_id']
} else {
    // Show error message
    echo $result['message'];
}
```

## Security Notes

- All functions use prepared statements to prevent SQL injection
- Passwords are hashed with bcrypt (cost factor 10)
- Session-based authentication
- Admin functions check `is_admin()` before execution
- Input validation on both client and server side
- Transaction handling for data integrity in bookings

## Version

API Version: 1.0  
Last Updated: March 19, 2026
