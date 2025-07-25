<?php
session_start();
include('db.php');

// Get booking_id from query parameter
if (!isset($_GET['booking_id'])) {
    die("Error: booking_id not provided.");
}

$booking_id = $_GET['booking_id'];
// Sanitize booking_id to prevent SQL injection
$booking_id = $conn->real_escape_string($booking_id);

// Fetch booking details
$booking_sql = "SELECT b.user_id, b.show_id, b.num_of_tickets, b.booking_date 
                FROM bookings b 
                WHERE b.booking_id = '$booking_id'";

$booking_result = $conn->query($booking_sql);

if ($booking_result->num_rows > 0) {
    $booking_details = $booking_result->fetch_assoc();
    $user_id = $booking_details['user_id'];
    $show_id = $booking_details['show_id'];
    $num_of_tickets = $booking_details['num_of_tickets'];
    $booking_date = $booking_details['booking_date'];

    // Fetch movie name associated with this show
    $movie_sql = "SELECT m.title FROM Shows s JOIN Movies m ON s.movie_id = m.movie_id WHERE s.show_id = '$show_id'";
    $movie_result = $conn->query($movie_sql);
    $movie_name = $movie_result->fetch_assoc()['title'];

    // Fetch the seat numbers associated with this booking
    $seats_sql = "SELECT seat_number, row_label FROM bookingseats WHERE booking_id = '$booking_id'";
    $seats_result = $conn->query($seats_sql);
    
    $seats = [];
    while ($row = $seats_result->fetch_assoc()) {
        $row_label = $row['row_label']; // Assuming you store the row label
        $seat_number = $row['seat_number'];
        $seats[] = $row_label . $seat_number; // Combine row label and seat number (e.g., A5)
    }
} else {
    echo "No booking found for booking_id: " . htmlspecialchars($booking_id);
    die("Error: No booking found with the provided ID.");
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
</head>
<body>
    <h2>Booking Confirmation</h2>
    <p>Movie: <strong><?php echo htmlspecialchars($movie_name); ?></strong></p>
    <p>Show Time: <strong><?php echo htmlspecialchars($booking_date); ?></strong></p>
    <p>Seats Booked: <strong><?php echo htmlspecialchars(implode(', ', $seats)); ?></strong></p>
    <p>Number of Tickets: <strong><?php echo htmlspecialchars($num_of_tickets); ?></strong></p>
</body>
</html>
