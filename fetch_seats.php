<?php
include('db.php');

if (isset($_POST['show_id'])) {
    $show_id = $_POST['show_id'];

    // Fetch available seats for the show
    $seats_sql = "SELECT * FROM Seats WHERE show_id = '$show_id'";
    $seats_result = $conn->query($seats_sql);

    $seats = [];
    if ($seats_result->num_rows > 0) {
        while ($row = $seats_result->fetch_assoc()) {
            $seats[] = $row;
        }
    }

    // Return JSON response
    echo json_encode(['seats' => $seats]);
}
?>
