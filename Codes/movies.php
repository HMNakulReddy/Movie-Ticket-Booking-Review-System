<?php
session_start();
include('db.php');

// Fetch movies from the database
$sql = "SELECT * FROM Movies";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Movies</title>
</head>
<body>
<link rel="stylesheet" href="movies.css">
    <h2>Available Movies</h2>

    <ul>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<li>";
                echo "<strong>" . htmlspecialchars($row['title']) . "</strong><br>";
                echo "Genre: " . htmlspecialchars($row['genre']) . "<br>";
                echo "Duration: " . htmlspecialchars($row['duration']) . " minutes<br>";
                echo "Release Date: " . htmlspecialchars($row['release_date']) . "<br>";
                echo "</li><br>";
            }
        } else {
            echo "<p>No movies available.</p>";
        }
        ?>
    </ul>

    <form method="POST" action="user_dashboard.php" style="margin-top: 20px;">
        <button type="submit">Back to Dashboard</button>
    </form>

    <form method="POST" action="logout.php" style="margin-top: 20px;">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
