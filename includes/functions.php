<?php
// General utility functions

function get_all_movies($conn) {
    $query = "SELECT * FROM movies WHERE status = 'active' ORDER BY release_date DESC";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_movie_by_id($conn, $movie_id) {
    $query = "SELECT * FROM movies WHERE movie_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function get_shows_by_movie($conn, $movie_id) {
    $query = "SELECT * FROM shows WHERE movie_id = ? AND show_time >= NOW() ORDER BY show_time ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function get_show_by_id($conn, $show_id) {
    $query = "SELECT s.*, m.title, m.duration FROM shows s 
              JOIN movies m ON s.movie_id = m.movie_id 
              WHERE s.show_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $show_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function get_seats_by_show($conn, $show_id) {
    $query = "SELECT * FROM seats WHERE show_id = ? ORDER BY seat_number";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $show_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function check_seat_availability($conn, $show_id, $seat_number) {
    $query = "SELECT seat_status FROM seats WHERE show_id = ? AND seat_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $show_id, $seat_number);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return false;
    }
    
    $seat = $result->fetch_assoc();
    return $seat['seat_status'] === 'available';
}

function create_booking($conn, $user_id, $show_id, $seats, $total_amount) {
    $conn->begin_transaction();
    
    try {
        // Create booking record
        $booking_query = "INSERT INTO bookings (user_id, show_id, total_amount, payment_status) 
                         VALUES (?, ?, ?, 'completed')";
        $stmt = $conn->prepare($booking_query);
        $stmt->bind_param("iid", $user_id, $show_id, $total_amount);
        $stmt->execute();
        $booking_id = $stmt->insert_id;
        
        // Process each seat
        foreach ($seats as $seat_number) {
            // Get seat_id
            $seat_query = "SELECT seat_id FROM seats WHERE show_id = ? AND seat_number = ?";
            $stmt = $conn->prepare($seat_query);
            $stmt->bind_param("is", $show_id, $seat_number);
            $stmt->execute();
            $seat_result = $stmt->get_result()->fetch_assoc();
            $seat_id = $seat_result['seat_id'];
            
            // Update seat status
            $update_query = "UPDATE seats SET seat_status = 'booked' WHERE seat_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("i", $seat_id);
            $stmt->execute();
            
            // Create booking detail
            $ticket_number = 'TKT-' . date('YmdHis') . '-' . rand(1000, 9999);
            $detail_query = "INSERT INTO booking_details (booking_id, seat_id, ticket_number, price) 
                            VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($detail_query);
            $price = $total_amount / count($seats);
            $stmt->bind_param("iiss", $booking_id, $seat_id, $ticket_number, $price);
            $stmt->execute();
        }
        
        // Update available seats count
        $update_show = "UPDATE shows SET available_seats = available_seats - ? WHERE show_id = ?";
        $stmt = $conn->prepare($update_show);
        $seat_count = count($seats);
        $stmt->bind_param("ii", $seat_count, $show_id);
        $stmt->execute();
        
        $conn->commit();
        return ['success' => true, 'booking_id' => $booking_id];
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function get_booking_details($conn, $booking_id) {
    $query = "SELECT b.*, bd.ticket_number, s.seat_number, m.title, sh.show_time, sh.theater_name
              FROM bookings b
              JOIN booking_details bd ON b.booking_id = bd.booking_id
              JOIN seats s ON bd.seat_id = s.seat_id
              JOIN shows sh ON b.show_id = sh.show_id
              JOIN movies m ON sh.movie_id = m.movie_id
              WHERE b.booking_id = ?
              ORDER BY s.seat_number";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function get_user_bookings($conn, $user_id) {
    $query = "SELECT b.booking_id, b.booking_date, b.total_amount, b.booking_status,
                     m.title, sh.show_time, sh.theater_name, COUNT(bd.booking_detail_id) as seat_count
              FROM bookings b
              JOIN shows sh ON b.show_id = sh.show_id
              JOIN movies m ON sh.movie_id = m.movie_id
              JOIN booking_details bd ON b.booking_id = bd.booking_id
              WHERE b.user_id = ?
              GROUP BY b.booking_id
              ORDER BY b.booking_date DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function generate_ticket_pdf($booking_details) {
    // Simplified PDF generation (returns HTML for printing)
    $html = '<div style="border: 2px solid #333; padding: 20px; max-width: 500px;">';
    $html .= '<h2 style="text-align: center;">Movie Ticket</h2>';
    
    foreach ($booking_details as $detail) {
        $html .= '<p><strong>Movie:</strong> ' . htmlspecialchars($detail['title']) . '</p>';
        $html .= '<p><strong>Theater:</strong> ' . htmlspecialchars($detail['theater_name']) . '</p>';
        $html .= '<p><strong>Date & Time:</strong> ' . date('M d, Y H:i', strtotime($detail['show_time'])) . '</p>';
        $html .= '<p><strong>Seat:</strong> ' . htmlspecialchars($detail['seat_number']) . '</p>';
        $html .= '<p><strong>Ticket #:</strong> ' . htmlspecialchars($detail['ticket_number']) . '</p>';
        $html .= '<hr>';
    }
    
    $html .= '</div>';
    return $html;
}

function format_date($date) {
    return date('M d, Y', strtotime($date));
}

function format_datetime($datetime) {
    return date('M d, Y H:i', strtotime($datetime));
}
?>
