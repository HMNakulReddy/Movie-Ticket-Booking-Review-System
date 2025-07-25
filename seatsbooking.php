<?php
session_start();
include('db.php');

// Fetch user bookings
$user_id = $_SESSION['user_id']; // Assuming user ID is stored in session
$bookings_sql = "SELECT b.booking_id, b.show_id, b.num_of_tickets, b.booking_date 
                 FROM bookings b WHERE b.user_id = '$user_id'";

$bookings_result = $conn->query($bookings_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Bookings</title>
</head>
<body>
    <h2>Your Bookings</h2>
    <?php if ($bookings_result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Show ID</th>
                    <th>Number of Tickets</th>
                    <th>Booking Date</th>
                    <th>Seats</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                        <td><?php echo htmlspecialchars($booking['show_id']); ?></td>
                        <td><?php echo htmlspecialchars($booking['num_of_tickets']); ?></td>
                        <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                        <td>
                            <?php
                            // Fetch the seat numbers for this booking
                            $seats_sql = "SELECT seat_number, row_label FROM bookingseats WHERE booking_id = '".$booking['booking_id']."'";
                            $seats_result = $conn->query($seats_sql);
                            $seats = [];
                            while ($seat_row = $seats_result->fetch_assoc()) {
                                $seats[] = $seat_row['row_label'] . $seat_row['seat_number'];
                            }
                            echo htmlspecialchars(implode(', ', $seats));
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No bookings found.</p>
    <?php endif; ?>

    <?php $conn->close(); ?>
</body>
</html>
