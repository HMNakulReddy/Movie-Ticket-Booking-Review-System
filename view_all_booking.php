<?php
include('db.php');

// Fetch all bookings along with user, movie, theater, and seat details
$bookings_sql = "
    SELECT u.username, m.title AS movie_title, s.show_time, b.booking_date, t.name, 
           GROUP_CONCAT(CONCAT(bs.row_label, bs.seat_number) ORDER BY bs.row_label, bs.seat_number ASC) AS seats_booked
    FROM bookings b
    JOIN bookingseats bs ON b.booking_id = bs.booking_id
    JOIN Shows s ON b.show_id = s.show_id
    JOIN Movies m ON s.movie_id = m.movie_id
    JOIN Theaters t ON s.theater_id = t.theater_id
    JOIN Users u ON b.user_id = u.user_id
    GROUP BY b.booking_id
";
$bookings_result = $conn->query($bookings_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View All Bookings</title>
    <link rel="stylesheet" href="view_all_booking.css">
</head>
<body>
    <h2>All Bookings</h2>
    <table>
        <tr>
            <th>Username</th>
            <th>Movie</th>
            <th>Show Time</th>
            <th>Booking Time</th>
            <th>Theater</th>
            <th>Seats</th>
        </tr>
        <?php if ($bookings_result->num_rows > 0): ?>
            <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $booking['username']; ?></td>
                    <td><?php echo $booking['movie_title']; ?></td>
                    <td><?php echo $booking['show_time']; ?></td>
                    <td><?php echo $booking['booking_date']; ?></td>
                    <td><?php echo $booking['name']; ?></td>
                    <td><?php echo $booking['seats_booked']; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No bookings found.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>

<?php $conn->close(); ?>
