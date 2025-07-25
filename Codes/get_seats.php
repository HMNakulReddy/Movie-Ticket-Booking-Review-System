<?php
include('db.php');

if (isset($_GET['show_id'])) {
    $show_id = intval($_GET['show_id']);

    $sql = "SELECT available_seats FROM Shows WHERE show_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $show_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $available_seats = $row['available_seats'];
        echo json_encode(['available_seats' => $available_seats]);
    } else {
        echo json_encode(['available_seats' => 0]);
    }

    $conn->close();
}
?>
