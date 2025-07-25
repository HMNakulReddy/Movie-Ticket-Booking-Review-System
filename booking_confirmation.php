<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch the latest booking details for the logged-in user
$user_id = $_SESSION['user_id'];
$sql = "SELECT Bookings.booking_id, Bookings.num_of_tickets, Bookings.booking_date, Shows.show_time, Movies.title, Theaters.name AS theater_name
        FROM Bookings
        JOIN Shows ON Bookings.show_id = Shows.show_id
        JOIN Movies ON Shows.movie_id = Movies.movie_id
        JOIN Theaters ON Shows.theater_id = Theaters.theater_id
        WHERE Bookings.user_id = '$user_id'
        ORDER BY Bookings.booking_date DESC
        LIMIT 1";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $booking = $result->fetch_assoc();
} else {
    $booking = null;
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
</head>
<body>
<link rel="stylesheet" href="confirmation.css">
    <h2>Booking Confirmation</h2>
    <?php if ($booking) { ?>
        <p>Thank you for your booking! Here are your details:</p>
        <ul>
            <li><strong>Booking ID:</strong> <?php echo $booking['booking_id']; ?></li>
            <li><strong>Movie Title:</strong> <?php echo $booking['title']; ?></li>
            <li><strong>Theater:</strong> <?php echo $booking['theater_name']; ?></li>
            <li><strong>Show Time:</strong> <?php echo $booking['show_time']; ?></li>
            <li><strong>Number of Tickets:</strong> <?php echo $booking['num_of_tickets']; ?></li>
            <li><strong>Booking Date:</strong> <?php echo $booking['booking_date']; ?></li>
        </ul>
    <?php } else { ?>
        <p>No booking information available.</p>
    <?php } ?>

    <form method="POST" action="logout.php" style="margin-top: 20px;">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
