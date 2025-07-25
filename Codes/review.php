<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['movie_id'])) {
    $movie_id = $_POST['movie_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $sql = "INSERT INTO Reviews (user_id, movie_id, rating, comment, review_date) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $user_id, $movie_id, $rating, $comment);

        if ($stmt->execute()) {
            $message = "Review added successfully.";
        } else {
            $message = "Error adding review: " . $stmt->error;
        }
    } else {
        $message = "You need to log in to add a review.";
    }
}

// Handle movie selection
$selected_movie_id = isset($_GET['movie_id']) ? intval($_GET['movie_id']) : 0;

// Fetch available movies for the dropdown
$movies_sql = "SELECT * FROM Movies";
$movies_result = $conn->query($movies_sql);

// Fetch reviews for the selected movie
$reviews_sql = "SELECT Reviews.rating, Reviews.comment, Reviews.review_date, Users.username
                FROM Reviews
                JOIN Users ON Reviews.user_id = Users.user_id
                WHERE Reviews.movie_id=?";
$reviews_stmt = $conn->prepare($reviews_sql);
$reviews_stmt->bind_param("i", $selected_movie_id);
$reviews_stmt->execute();
$reviews_result = $reviews_stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Movie Reviews</title>
</head>
<body>
<link rel="stylesheet" href="review.css">
    <h2>Movie Reviews</h2>

    <?php if (isset($message)) { echo "<p>$message</p>"; } ?>

    <form method="GET" action="review.php">
        <label for="movie_id">Select Movie:</label>
        <select name="movie_id" id="movie_id" onchange="this.form.submit()" required>
            <option value="">Select a movie</option>
            <?php while ($movie = $movies_result->fetch_assoc()) { ?>
                <option value="<?php echo htmlspecialchars($movie['movie_id']); ?>" <?php echo $selected_movie_id == $movie['movie_id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($movie['title']); ?>
                </option>
            <?php } ?>
        </select>
    </form>

    <?php if ($selected_movie_id) { ?>
        <h3>Add Review</h3>
        <form method="POST" action="review.php">
            <input type="hidden" name="movie_id" value="<?php echo htmlspecialchars($selected_movie_id); ?>">
            <label for="rating">Rating:</label>
            <input type="number" name="rating" id="rating" min="1" max="5" required><br>
            
            <label for="comment">Comment:</label>
            <textarea name="comment" id="comment" required></textarea><br>
            
            <button type="submit">Submit Review</button>
        </form>

        <h3>Reviews for Selected Movie</h3>
        <ul>
            <?php
            if ($reviews_result->num_rows > 0) {
                while ($row = $reviews_result->fetch_assoc()) {
                    echo "<li>";
                    echo "<strong>" . htmlspecialchars($row['username']) . "</strong> (Rating: " . htmlspecialchars($row['rating']) . "/5)<br>";
                    echo htmlspecialchars($row['comment']) . "<br>";
                    echo "<small>Reviewed on: " . htmlspecialchars($row['review_date']) . "</small>";
                    echo "</li>";
                }
            } else {
                echo "<p>No reviews available for this movie.</p>";
            }
            ?>
        </ul>
    <?php } ?>

    <form method="POST" action="logout.php" style="margin-top: 20px;">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
