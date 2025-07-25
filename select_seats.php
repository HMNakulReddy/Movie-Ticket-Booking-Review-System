<?php
session_start();
include('db.php');

// Get show_id from query parameter
$show_id = $_GET['show_id'];

// Fetch total seats and available seats for the show
$show_sql = "SELECT total_seats FROM Shows WHERE show_id = '$show_id'";
$show_result = $conn->query($show_sql);
$show = $show_result->fetch_assoc();

$total_seats = $show['total_seats'];

// Define the number of seats per row
$seats_per_row = 10; // 10 seats in each row
$rows = ceil($total_seats / $seats_per_row); // Calculate total rows needed

// Get booked seats for the show
$booked_seats_sql = "SELECT seat_number FROM bookingseats WHERE show_id = '$show_id'";
$booked_seats_result = $conn->query($booked_seats_sql);

$booked_seats = [];
while ($row = $booked_seats_result->fetch_assoc()) {
    $booked_seats[] = $row['seat_number'];
}

// Handle seat booking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selected_seats'])) {
    $selected_seats = $_POST['selected_seats'];

    if (count($selected_seats) > 0) {
        $user_id = $_SESSION['user_id']; // Assuming user ID is stored in session
        $num_of_tickets = count($selected_seats);

        // Insert booking into the bookings table
        $booking_sql = "INSERT INTO bookings (user_id, show_id, num_of_tickets, booking_date) 
                        VALUES ('$user_id', '$show_id', '$num_of_tickets', NOW())";
        if ($conn->query($booking_sql) === TRUE) {
            $booking_id = $conn->insert_id; // Get the last inserted ID for reference

            // Insert each selected seat into bookingseats table
            foreach ($selected_seats as $seat) {
                $seat_sql = "INSERT INTO bookingseats (booking_id, show_id, seat_number) 
                             VALUES ('$booking_id', '$show_id', '$seat')";
                $conn->query($seat_sql);
            }

            // Redirect to a confirmation page with booking ID and seat numbers
            header("Location: confirmation.php?booking_id=$booking_id&seats=" . implode(',', $selected_seats));
            exit();
        } else {
            $error_message = "Error in booking: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seats</title>
    <link rel="stylesheet" href="select_seats.css">
    <script>
        function validateForm() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
            if (checkboxes.length === 0) {
                alert("Please select at least one seat.");
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }
    </script>
</head>
<body>
    <h2>Select Seats</h2>

    <div class="screen">Screen</div>

    <form method="POST" action="select_seats.php?show_id=<?php echo $show_id; ?>" onsubmit="return validateForm();">
    <div class="seat-container">
        <?php
        $seat_number = 1; // Starting seat number
        $row_labels = range('A', 'Z'); // Array of row labels

        for ($i = 0; $i < $rows; $i++) {
            echo "<div class='seat-row'>";
            echo "<span class='row-label'>" . $row_labels[$i % count($row_labels)] . "</span>"; // Row label (A, B, C...)

            for ($j = 1; $j <= $seats_per_row && $seat_number <= $total_seats; $j++) {
                $is_booked = in_array($seat_number, $booked_seats) ? 'unavailable' : 'seat';
                echo "<input type='checkbox' id='seat$seat_number' name='selected_seats[]' value='$seat_number' class='$is_booked' ". ($is_booked === 'unavailable' ? 'disabled' : '') .">";
                echo "<label for='seat$seat_number' class='seat-label'>$seat_number</label>"; // Seat number inside the symbol
                $seat_number++;
            }

            echo "</div>";
        }
        ?>
    </div>
    <div class="button-container">
        <button type="submit" name="book_seats" class="book-button">Book Selected Seats</button>
    </div>
    <?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>
</form>
</body>
</html>
