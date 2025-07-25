<?php
session_start();

// Redirect to login if not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location:login.php');
    exit();
}

include('db.php');

// Handle movie addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_movie'])) {
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $duration = $_POST['duration'];
    $release_date = $_POST['release_date'];

    $sql = "INSERT INTO Movies (title, genre, duration, release_date) VALUES ('$title', '$genre', '$duration', '$release_date')";
    $conn->query($sql);
}

// Handle movie deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_movie'])) {
    $movie_id = $_POST['movie_id'];

    $sql = "DELETE FROM Movies WHERE movie_id='$movie_id'";
    $conn->query($sql);
}

// Fetch movies from the database
$sql = "SELECT * FROM Movies";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Movies</title>
    <link rel="stylesheet" href="manage_movies.css">
</head>
<body>
    <h2>Manage Movies</h2>
    <!-- Form to Add a New Movie -->
    <form method="POST" action="manage_movies.php">
        <input type="text" name="title" placeholder="Title" required><br>
        <input type="text" name="genre" placeholder="Genre" required><br>
        <input type="number" name="duration" placeholder="Duration (minutes)" required><br>
        <input type="date" name="release_date" placeholder="Release Date" required><br>
        <button type="submit" name="add_movie">Add Movie</button>
    </form>

    <h3>Movies List</h3>
    <div class="movies-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="movie-card">
                    <div class="movie-details">
                        <h4><?php echo $row['title']; ?></h4>
                        <p>Genre: <?php echo $row['genre']; ?></p>
                        <p>Duration: <?php echo $row['duration']; ?> minutes</p>
                        <p>Release Date: <?php echo $row['release_date']; ?></p>
                    </div>
                    <form method="POST" action="manage_movies.php">
                        <input type="hidden" name="movie_id" value="<?php echo $row['movie_id']; ?>">
                        <button type="submit" name="delete_movie" class="delete-button">Delete</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No movies available</p>
        <?php endif; ?>
    </div>

    <!-- Logout Button -->
    <form method="POST" action="logout.php" style="margin-top: 20px;">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
