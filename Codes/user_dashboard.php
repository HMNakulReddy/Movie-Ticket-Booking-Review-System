<?php
session_start();

// Check if the user is logged in and if they are not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: login.php');
    exit();
}

// User-specific content can go here

?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" type="text/css" href="user_dashboard.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="book.php">Book a Ticket</a></li>
            <li><a href="movies.php">View Movies</a></li>
            <li><a href="view_bookings.php">View Your Bookings</a></li>
            <li><a href="review.php">Leave a Review</a></li>
            <li>
                <form method="POST" action="logout.php" style="display:inline;">
                    <button type="submit" style="background:none; border:none; padding:0;">
                        <img src="logout.png" alt="Logout" style="width:20px; height:20px; vertical-align:middle;">
                        Logout
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    <h2>Welcome to the User Dashboard</h2>
</body>
</html>
