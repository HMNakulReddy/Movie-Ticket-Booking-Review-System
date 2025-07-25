<?php
session_start();

// Redirect to login if not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

include('db.php');

// Handle show addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_show'])) {
    $movie_id = $_POST['movie_id'];
    $theater_id = $_POST['theater_id'];
    $show_time = $_POST['show_time'];
    $available_seats = $_POST['available_seats'];

    // Fetch total_seats from the selected theater
    $theater_sql = "SELECT total_seats FROM Theaters WHERE theater_id = '$theater_id'";
    $theater_result = $conn->query($theater_sql);

    if ($theater_result->num_rows > 0) {
        $theater_row = $theater_result->fetch_assoc();
        $total_seats = $theater_row['total_seats'];

        // Now insert the new show with total_seats
        $sql = "INSERT INTO Shows (movie_id, theater_id, show_time, available_seats, total_seats) 
                VALUES ('$movie_id', '$theater_id', '$show_time', '$available_seats', '$total_seats')";

        if ($conn->query($sql) === TRUE) {
            $message = "Show added successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

// Handle show deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_show'])) {
    $show_id = $_POST['show_id'];

    $sql = "DELETE FROM Shows WHERE show_id='$show_id'";
    $conn->query($sql);
}

// Fetch shows from the database
$sql = "SELECT Shows.show_id, Shows.show_time, Shows.available_seats, Shows.total_seats, Movies.title AS movie_title, Theaters.name AS theater_name
        FROM Shows
        JOIN Movies ON Shows.movie_id = Movies.movie_id
        JOIN Theaters ON Shows.theater_id = Theaters.theater_id";
$result_shows = $conn->query($sql);

// Fetch movies and theaters for the dropdowns
$sql_movies = "SELECT * FROM Movies";
$result_movies = $conn->query($sql_movies);

$sql_theaters = "SELECT * FROM Theaters";
$result_theaters = $conn->query($sql_theaters);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Shows</title>
    <link rel="stylesheet" href="manage_shows.css">
</head>
<body>
    <h2>Manage Shows</h2>

    <!-- Form to Add a New Show -->
    <form method="POST" action="manage_shows.php">
        <select name="movie_id" required>
            <option value="">Select Movie</option>
            <?php while ($movie = $result_movies->fetch_assoc()): ?>
                <option value="<?php echo $movie['movie_id']; ?>"><?php echo $movie['title']; ?></option>
            <?php endwhile; ?>
        </select><br>

        <select name="theater_id" required>
            <option value="">Select Theater</option>
            <?php while ($theater = $result_theaters->fetch_assoc()): ?>
                <option value="<?php echo $theater['theater_id']; ?>"><?php echo $theater['name']; ?></option>
            <?php endwhile; ?>
        </select><br>

        <input type="datetime-local" name="show_time" required><br>
        <input type="number" name="available_seats" placeholder="Available Seats" required><br>
        <button type="submit" name="add_show">Add Show</button>
    </form>

    <h3>Show List</h3>
    <div class="show-list">
        <?php if ($result_shows->num_rows > 0): ?>
            <?php while ($row = $result_shows->fetch_assoc()): ?>
                <div class="show-card">
                    <div class="show-details">
                        <div><strong>Movie:</strong> <?php echo $row['movie_title']; ?></div>
                        <div><strong>Theater:</strong> <?php echo $row['theater_name']; ?></div>
                        <div><strong>Show Time:</strong> <?php echo $row['show_time']; ?></div>
                        <div><strong>Available Seats:</strong> <?php echo $row['available_seats']; ?></div>
                        <div><strong>Total Seats:</strong> <?php echo $row['total_seats']; ?></div>
                    </div>
                    <form method="POST" action="manage_shows.php" class="delete-form">
                        <input type="hidden" name="show_id" value="<?php echo $row['show_id']; ?>">
                        <button type="submit" name="delete_show" class="delete-button">Delete</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No shows available.</p>
        <?php endif; ?>
    </div>

    <!-- Logout Button -->
    <form method="POST" action="logout.php" style="margin-top: 20px;">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
