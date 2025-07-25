<?php
session_start(); // Start the session
include('db.php');

// Ensure the user is logged in before accessing the bookings
if (!isset($_SESSION['user_id'])) {
    echo "Error: User not logged in.";
    exit();
}

// Fetch bookings for the logged-in user
$user_id = $_SESSION['user_id'];

$bookings_sql = "
    SELECT b.booking_id, b.booking_date, b.num_of_tickets, s.show_time, m.title
    FROM bookings b
    JOIN Shows s ON b.show_id = s.show_id
    JOIN Movies m ON s.movie_id = m.movie_id
    WHERE b.user_id = '$user_id'
";

// Execute query and check for errors
$bookings_result = $conn->query($bookings_sql);

if (!$bookings_result) {
    echo "Error in bookings query: " . $conn->error; // Error message
    exit();
}

if ($bookings_result->num_rows > 0) {
    while ($booking = $bookings_result->fetch_assoc()) {
        echo "<div class='booking'>";
        echo "<h3>Booking : " . $booking['booking_id'] . "</h3>";
        echo "<p>Movie: " . $booking['title'] . "</p>";
        echo "<p>Show Time: " . $booking['show_time'] . "</p>";
        
        // Fetch booked seats for this booking
        $booking_id = $booking['booking_id'];
        $seats_sql = "SELECT seat_number, row_label FROM bookingseats WHERE booking_id = '$booking_id'";
        $seats_result = $conn->query($seats_sql);
        
        if (!$seats_result) {
            echo "Error in seats query: " . $conn->error; // Error message
            exit();
        }

        if ($seats_result->num_rows > 0) {
            echo "<p>Seats Booked: ";
            $seat_details = [];
            while ($seat = $seats_result->fetch_assoc()) {
                $seat_details[] = $seat['row_label'] . $seat['seat_number'];
            }
            echo implode(', ', $seat_details);
            echo "</p>";
        } else {
            echo "<p>No seats booked.</p>";
        }
        
        echo "</div>";
    }
} else {
    echo "<p>No bookings found.</p>";
}

$conn->close();
?>
