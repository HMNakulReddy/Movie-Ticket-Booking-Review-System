<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
    <h2>Welcome to the Admin Dashboard</h2>
    
    <!-- Navigation Bar -->
    <div class="navbar">
        <ul>
            <li><a href="manage_movies.php">Manage Movies</a></li>
            <li><a href="manage_shows.php">Manage Shows</a></li>
            <li><a href="manage_theaters.php">Manage Theaters</a></li>
            <li><a href="view_all_booking.php">View Bookings</a></li>
            <li class="logout">
                <form method="POST" action="logout.php">
                    <button type="submit">
                        <img src="logout.png" alt="Logout">
                    </button>
                </form>
            </li>
        </ul>
    </div>
</body>
</html>
