<?php
include('db.php');

if (isset($_POST['movie_id'])) {
    $movie_id = $_POST['movie_id'];

    // Fetch shows for the selected movie
    $sql = "SELECT show_id, show_time FROM Shows WHERE movie_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $shows = [];
    while ($row = $result->fetch_assoc()) {
        $shows[] = $row;
    }

    // Send the shows as JSON response
    echo json_encode(['shows' => $shows]);
}
?>
