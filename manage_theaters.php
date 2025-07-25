<?php
session_start();
include('db.php');

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Add Theater
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_theater'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $location = $conn->real_escape_string($_POST['location']);
    $total_seats = (int)$_POST['total_seats'];

    $sql = "INSERT INTO Theaters (name, location, total_seats) VALUES ('$name', '$location', '$total_seats')";
    if ($conn->query($sql) === TRUE) {
        $message = "Theater added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle Update Theater Seats
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_theater'])) {
    $theater_id = (int)$_POST['theater_id'];
    $total_seats = (int)$_POST['total_seats'];

    // Update the theater seats
    $update_theater_sql = "UPDATE Theaters SET total_seats = '$total_seats' WHERE theater_id = '$theater_id'";
    if ($conn->query($update_theater_sql) === TRUE) {
        // Update total seats in all associated shows
        $update_shows_sql = "UPDATE Shows SET total_seats = '$total_seats' WHERE theater_id = '$theater_id'";
        $conn->query($update_shows_sql);

        $message = "Theater seats updated successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle Delete Theater
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_theater'])) {
    $theater_id = (int)$_POST['theater_id'];

    // Check if there are shows associated with the theater before deleting
    $show_check = "SELECT COUNT(*) AS show_count FROM Shows WHERE theater_id = $theater_id";
    $show_result = $conn->query($show_check);
    $show_row = $show_result->fetch_assoc();

    if ($show_row['show_count'] > 0) {
        $message = "Cannot delete theater with active shows!";
    } else {
        $delete_sql = "DELETE FROM Theaters WHERE theater_id = $theater_id";
        if ($conn->query($delete_sql) === TRUE) {
            $message = "Theater deleted successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

// Fetch all theaters
$sql = "SELECT * FROM Theaters";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Theaters</title>
</head>
<body>
<link rel="stylesheet" href="manage_theaters.css">
    <h2>Manage Theaters</h2>

    <?php if (isset($message)) { echo "<p>$message</p>"; } ?>

    <!-- Form to Add Theater -->
    <form method="POST" action="manage_theaters.php">
        <h3>Add New Theater</h3>
        <input type="text" name="name" placeholder="Name" required><br>
        <input type="text" name="location" placeholder="Location" required><br>
        <input type="number" name="total_seats" placeholder="Total Seats" required><br>
        <button type="submit" name="add_theater">Add Theater</button>
    </form>

    <!-- List of Theaters -->
    <h3>Theater List</h3>
    <ul>
        <?php if ($result->num_rows > 0) { ?>
            <?php while($row = $result->fetch_assoc()) { ?>
                <li>
                    <?php echo $row['name']; ?> - <?php echo $row['location']; ?> (<?php echo $row['total_seats']; ?> seats)
                    <form method="POST" action="manage_theaters.php" style="display:inline;">
                        <input type="hidden" name="theater_id" value="<?php echo $row['theater_id']; ?>">
                        <button type="submit" name="delete_theater" onclick="return confirm('Are you sure you want to delete this theater?');">Delete</button>
                    </form>
                    <!-- Form to Update Theater Seats -->
                    <form method="POST" action="manage_theaters.php" style="display:inline;">
                        <input type="hidden" name="theater_id" value="<?php echo $row['theater_id']; ?>">
                        <input type="number" name="total_seats" value="<?php echo $row['total_seats']; ?>" required>
                        <button type="submit" name="update_theater">Update Seats</button>
                    </form>
                </li>
            <?php } ?>
        <?php } else { ?>
            <p>No theaters available.</p>
        <?php } ?>
    </ul>

    <!-- Logout Button -->
    <form method="POST" action="logout.php" style="margin-top: 20px;">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
