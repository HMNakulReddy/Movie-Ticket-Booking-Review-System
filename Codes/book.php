<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch available movies and shows along with theaters for the form
$movies_sql = "SELECT * FROM Movies";
$movies_result = $conn->query($movies_sql);

// Fetch shows with associated theater details
$shows_sql = "
    SELECT s.show_id, s.show_time, t.name 
    FROM Shows s
    JOIN Theaters t ON s.theater_id = t.theater_id
";
$shows_result = $conn->query($shows_sql);

// Handle seat selection and booking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['select_seats'])) {
    $show_id = $_POST['show_id'];
    header("Location: select_seats.php?show_id=$show_id");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Movie</title>
    <link rel="stylesheet" href="book.css">
</head>
<body>
    <h2>Book Movie</h2>
    <form method="POST" action="book.php">
        <label for="movie_id">Select Movie:</label>
        <select name="movie_id" id="movie_id" required>
            <?php while($movie = $movies_result->fetch_assoc()) { ?>
                <option value="<?php echo $movie['movie_id']; ?>"><?php echo $movie['title']; ?></option>
            <?php } ?>
        </select><br>

        <label for="show_id">Select Show & Theater:</label>
        <select name="show_id" id="show_id" required>
            <?php while($show = $shows_result->fetch_assoc()) { ?>
                <option value="<?php echo $show['show_id']; ?>">
                    <?php echo $show['show_time'] . " - " . $show['name']; ?>
                </option>
            <?php } ?>
        </select><br>

        <button type="submit" name="select_seats">Select Seats</button>
    </form>

    <!-- Logout Button -->
    <form method="POST" action="logout.php" style="margin-top: 20px;">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
