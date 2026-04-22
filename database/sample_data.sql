-- Sample Data for Movie Ticket Booking System

USE movie_ticket_booking;

-- Insert Sample Users
-- Password: "password" (hashed with bcrypt)
INSERT INTO users (email, password, first_name, last_name, phone, user_type) VALUES
('user1@example.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36DRcg3O', 'John', 'Doe', '9876543210', 'user'),
('user2@example.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36DRcg3O', 'Jane', 'Smith', '9876543211', 'user'),
('admin@example.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36DRcg3O', 'Admin', 'User', '9876543212', 'admin');

-- Insert Sample Movies
INSERT INTO movies (title, description, genre, duration, rating, poster_url, release_date, status) VALUES
('The Quantum Enigma', 'A mind-bending sci-fi thriller about alternate dimensions.', 'Science Fiction', 148, 8.5, 'poster1.jpg', '2026-03-15', 'active'),
('Romance in Paris', 'A romantic drama set in the heart of Paris.', 'Romance', 125, 7.8, 'poster2.jpg', '2026-03-28', 'active'),
('Action Heroes', 'An action-packed adventure with explosive stunts.', 'Action', 138, 8.2, 'poster3.jpg', '2026-03-23', 'active'),
('Mystery Manor', 'A thrilling mystery about a haunted mansion.', 'Thriller', 132, 7.9, 'poster4.jpg', '2026-03-23', 'active'),
('Comedy Nights', 'A hilarious comedy that will make you laugh out loud.', 'Comedy', 110, 7.5, 'poster5.jpg', '2026-03-24', 'active');

-- Insert Sample Shows
INSERT INTO shows (movie_id, show_time, theater_name, total_seats, available_seats) VALUES
(1, '2026-03-20 10:00:00', 'Theater A', 100, 75),
(1, '2026-03-20 14:00:00', 'Theater B', 100, 60),
(1, '2026-03-20 18:00:00', 'Theater A', 100, 45),
(2, '2026-03-20 11:00:00', 'Theater C', 80, 65),
(2, '2026-03-20 19:00:00', 'Theater B', 80, 50),
(3, '2026-03-21 10:30:00', 'Theater A', 100, 70),
(3, '2026-03-21 15:00:00', 'Theater C', 100, 55),
(4, '2026-03-21 12:00:00', 'Theater B', 80, 40),
(5, '2026-03-22 14:00:00', 'Theater A', 100, 80);

-- Insert Sample Seats for Show 1 (10x10 grid)
INSERT INTO seats (show_id, seat_number, seat_status) VALUES
(1, 'A1', 'available'), (1, 'A2', 'available'), (1, 'A3', 'available'), (1, 'A4', 'booked'), (1, 'A5', 'available'),
(1, 'A6', 'available'), (1, 'A7', 'booked'), (1, 'A8', 'available'), (1, 'A9', 'available'), (1, 'A10', 'available'),
(1, 'B1', 'available'), (1, 'B2', 'available'), (1, 'B3', 'booked'), (1, 'B4', 'available'), (1, 'B5', 'available'),
(1, 'B6', 'booked'), (1, 'B7', 'available'), (1, 'B8', 'available'), (1, 'B9', 'available'), (1, 'B10', 'booked'),
(1, 'C1', 'available'), (1, 'C2', 'available'), (1, 'C3', 'available'), (1, 'C4', 'available'), (1, 'C5', 'booked'),
(1, 'C6', 'available'), (1, 'C7', 'available'), (1, 'C8', 'booked'), (1, 'C9', 'available'), (1, 'C10', 'available');

-- Insert Sample Seats for Show 2-9 (simplified - 100 available seats each)
-- This creates a basic seat structure for all other shows
DELIMITER //
CREATE PROCEDURE populate_seats()
BEGIN
  DECLARE show_num INT DEFAULT 2;
  DECLARE row_char CHAR(1);
  DECLARE seat_num INT;
  
  WHILE show_num <= 9 DO
    SET row_char = 'A';
    WHILE row_char <= 'J' DO
      SET seat_num = 1;
      WHILE seat_num <= 10 DO
        INSERT INTO seats (show_id, seat_number, seat_status) 
        VALUES (show_num, CONCAT(row_char, seat_num), 'available');
        SET seat_num = seat_num + 1;
      END WHILE;
      SET row_char = CHAR(ORD(row_char) + 1);
    END WHILE;
    SET show_num = show_num + 1;
  END WHILE;
END//
DELIMITER ;

-- Call the procedure to populate seats
CALL populate_seats();
