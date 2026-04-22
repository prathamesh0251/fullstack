# CineBook - Movie Ticket Booking System

A full-featured online movie ticket booking system built with HTML, CSS, JavaScript, PHP, and MySQL.

## Project Overview

CineBook is a real-world simulation of an online movie ticket booking platform. It includes comprehensive user features for browsing and booking movies, as well as a complete admin panel for managing movies, shows, seats, and generating reports.

## Features

### User Features
✅ User Registration & Login with secure password hashing
✅ Profile Management (view/update personal details)
✅ Browse movies with detailed information (genre, rating, duration)
✅ View available showtimes for each movie
✅ Interactive seat selection with real-time availability
✅ Shopping cart-style booking workflow
✅ Simulated payment confirmation
✅ Booking confirmation with e-ticket details
✅ Print/Download ticket functionality
✅ View booking history and past bookings
✅ Responsive mobile-friendly design

### Admin Features
✅ Secure admin login with role-based access control
✅ Add, edit, and delete movies
✅ Create shows with showtimes and theaters
✅ Manage seat layouts for each show
✅ View all bookings with user details
✅ Generate revenue and sales reports
✅ View system statistics and analytics
✅ User management and tracking

### Technical Features
✅ MySQL database with proper relationships (foreign keys)
✅ Secured session management
✅ Password hashing with bcrypt
✅ Form validation (client & server-side)
✅ Error handling for edge cases
✅ RESTful database operations
✅ Proper transaction handling for bookings

## System Requirements

- **Web Server**: Apache with PHP support
- **PHP Version**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Browser**: Modern browser with JavaScript enabled

## Installation & Setup

### 1. Extract Project Files
```
Extract the project to your web server directory (e.g., htdocs, www, or public_html)
```

### 2. Database Setup

#### 2a. Create Database and Tables
1. Open phpMyAdmin or MySQL command line
2. Run the SQL schema file to create database and tables:
   ```sql
   source /path/to/database/schema.sql
   ```
   
   Alternatively, copy and paste the contents of `database/schema.sql` into phpMyAdmin SQL tab

#### 2b. Insert Sample Data
Run the sample data file to populate movies, shows, users, and seats:
   ```sql
   source /path/to/database/sample_data.sql
   ```

### 3. Configure Database Connection
Edit `config/config.php` and update database credentials:
```php
define('DB_HOST', 'localhost');    // Usually 'localhost'
define('DB_USER', 'root');           // Your MySQL username
define('DB_PASS', '');               // Your MySQL password
define('DB_NAME', 'movie_ticket_booking');
```

### 4. Set Up Web Server
- **Apache**: Ensure `mod_rewrite` is enabled
- **Nginx**: Configure to serve PHP files correctly
- Set appropriate folder permissions (755 for directories, 644 for files)

### 5. Access the Application
Open your browser and navigate to:
```
http://localhost/Project/
```
(Replace 'Project' with your actual folder name)

## Demo Credentials

### User Login
- **Email**: user1@example.com
- **Password**: password

### Admin Login
- **Email**: admin@example.com
- **Password**: password

## File Structure

```
Project/
├── index.php                  # Homepage
├── config/
│   └── config.php            # Database configuration
├── includes/
│   ├── auth.php              # Authentication functions
│   └── functions.php         # Utility and business logic functions
├── database/
│   ├── schema.sql            # Database schema
│   └── sample_data.sql       # Sample data
├── pages/
│   ├── login.php             # User login page
│   ├── register.php          # User registration
│   ├── movies.php            # Movie listing
│   ├── select-show.php       # Show selection
│   ├── seat-selection.php    # Seat selection
│   ├── booking-confirmation.php  # Confirmation page
│   ├── profile.php           # User profile
│   ├── bookings.php          # User bookings list
│   ├── booking-details.php   # Booking details
│   └── logout.php            # Logout handler
├── admin/
│   ├── dashboard.php         # Admin dashboard
│   ├── manage-movies.php     # Movie management
│   ├── manage-shows.php      # Show management
│   ├── manage-seats.php      # Seat management
│   ├── view-bookings.php     # Bookings view
│   ├── reports.php           # Reports & analytics
│   └── manage-users.php      # User management
├── css/
│   └── style.css             # Main stylesheet
├── js/
│   └── script.js             # JavaScript functionality
└── README.md                 # This file
```

## Usage Guide

### For Users

#### 1. Registration & Login
- Click "Register" to create a new account
- Enter your details: first name, last name, email, phone (optional), password
- After registration, login with your email and password

#### 2. Browse Movies
- Visit the "Movies" page to see all available movies
- Each movie shows: title, genre, rating, duration, and description
- Use the search bar to filter movies by title or genre

#### 3. Select a Show
- Click "Book Tickets" on any movie
- Choose your preferred showtime from the list
- Confirm by clicking "Proceed to Seat Selection"

#### 4. Select Seats
- Click on available seats to select them (green seats)
- Booked seats (gray) cannot be selected
- The booking summary updates in real-time with total cost
- ₹300 per seat

#### 5. Complete Booking
- Confirm your selection to proceed to payment (simulated)
- Booking confirmation page displays:
  - Booking ID
  - Movie details
  - Selected seats
  - Total amount
- Payment status shows as "Completed" (simulated)

#### 6. Download/Print Ticket
- On confirmation page, click "Print Ticket" to print
- Click "Download Ticket" to save as HTML
- View booking details anytime from "My Bookings"

#### 7. Manage Profile
- Click "Profile" to view and edit your information
- View your booking history
- Cannot change email address

### For Admin

#### 1. Admin Login
- Login with admin credentials
- Access admin dashboard from navigation

#### 2. Manage Movies
- Add new movies with details: title, genre, duration, rating, description
- Edit existing movies
- Delete movies (removes associated shows and bookings)

#### 3. Manage Shows
- Create new shows for movies
- Specify showtimes, theater name, and total seats
- Seats are automatically created (10x10 grid = 100 seats)

#### 4. Manage Seats
- View seat layout for any show
- Toggle seat status between available and booked
- Useful for marking seats closed for maintenance or correcting bookings

#### 5. View Bookings
- See all bookings with user information
- Filter by booking ID, user name, or movie
- Check payment and booking status

#### 6. Generate Reports
- View tickets sold per movie
- Analyze top performing shows
- Monitor monthly revenue trends
- Track ticket sales by show

#### 7. Manage Users
- View all registered users
- See total bookings per user
- User signup dates and contact information

## Database Schema

### Users Table
- user_id (Primary Key)
- email (Unique)
- password (hashed with bcrypt)
- first_name, last_name
- phone
- user_type (user/admin)
- created_at, updated_at

### Movies Table
- movie_id (Primary Key)
- title, description, genre
- duration, rating
- release_date, poster_url
- status (active/inactive)

### Shows Table
- show_id (Primary Key)
- movie_id (Foreign Key → movies)
- show_time (DateTime)
- theater_name
- total_seats, available_seats

### Seats Table
- seat_id (Primary Key)
- show_id (Foreign Key → shows)
- seat_number (e.g., A1, B5)
- seat_status (available/booked)

### Bookings Table
- booking_id (Primary Key)
- user_id (Foreign Key → users)
- show_id (Foreign Key → shows)
- booking_date
- total_amount
- payment_status (pending/completed)
- booking_status (confirmed/cancelled)

### Booking Details Table
- booking_detail_id (Primary Key)
- booking_id (Foreign Key → bookings)
- seat_id (Foreign Key → seats)
- ticket_number (Unique)
- price

## Security Features

✅ **Password Hashing**: Bcrypt password hashing for user accounts
✅ **Session Management**: Secure PHP session handling
✅ **SQL Injection Prevention**: Parameterized prepared statements
✅ **Role-Based Access Control**: Admin vs User access levels
✅ **CSRF Protection**: Session-based state management
✅ **Input Validation**: Client-side and server-side validation
✅ **Error Handling**: Proper exception and error handling

## Testing Scenarios

### Scenario 1: Normal User Booking Flow
1. Register as new user
2. Browse movies
3. Select a show
4. Choose seats
5. Complete booking
6. View confirmation
7. Check bookings in profile

### Scenario 2: Multiple Bookings
- Same user books multiple movies
- Verify seat availability updates correctly
- Check booking history is comprehensive

### Scenario 3: Concurrent bookings
- Two users select same seats
- Second user should get "seats unavailable" error
- Demonstrate transaction integrity

### Scenario 4: Admin Activities
1. Add a new movie
2. Create shows for the movie
3. View bookings
4. Generate sales report
5. Manage seats (toggle availability)

## Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check database credentials in `config/config.php`
- Ensure database and tables are created from schema.sql

### Session Issues
- Ensure cookies are enabled in browser
- Check folder permissions on server
- Verify PHP session.save_path is writable

### Seat Layout Not Showing
- Verify seats were created when show was added
- Check seats table has data for the show
- Ensure show_id is correctly passed

### Payment Confirmation Not Working
- This is simulated - no real payment gateway integrated
- Booking should complete successfully regardless
- Check server error logs if issues persist

## Future Enhancements

🔄 Actual payment gateway integration (Stripe, PayPal, Razorpay)
🔄 Email notifications for booking confirmation
🔄 SMS notifications
🔄 Real PDF ticket generation
🔄 Advanced seat selection features (row-wise booking)
🔄 Movie ratings/reviews system
🔄 Promotional discounts and coupons
🔄 Group booking discounts
🔄 Used refund and cancellation system
🔄 Real-time seat availability updates (WebSocket)
🔄 Mobile app
🔄 Multiple language support

## Technical Notes

### Session Management
- Sessions are stored in PHP's default session directory
- Session timeout: 1 hour (configurable)
- Session destroyed on logout

### Mail & Notifications
- Currently simulated - ready for email integration
- Can be extended with PHPMailer or similar

### File Upload
- Poster URL currently stored as filename
- Can be extended with actual file upload functionality

## Performance Considerations

- Database indexes created on frequently queried columns
- Use pagination for large datasets
- Cache movie listings for better performance
- Optimize seat query with proper joins
- Consider adding database query caching

## Browser Compatibility

✅ Chrome (latest 2 versions)
✅ Firefox (latest 2 versions)
✅ Safari (latest 2 versions)
✅ Edge (latest 2 versions)
✅ Mobile browsers (iOS Safari, Chrome Mobile)

## Support & Contributions

For issues or suggestions, please review the code structure:
- All authentication logic is in `includes/auth.php`
- Business logic is in `includes/functions.php`
- Database config is in `config/config.php`

## License

This project is created for educational purposes as a demonstration of a real-world movie ticket booking system.

## Author

Created as a comprehensive educational project showcasing:
- Full-stack web development
- Database design and relationships
- User authentication and security
- Admin panel development
- Report generation
- Business logic implementation

---

**Last Updated**: March 19, 2026
**Version**: 1.0

For setup assistance or additional information, refer to the documentation in each directory.
