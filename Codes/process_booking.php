<?php
session_start();
include('db.php');

// Ensure show_id, user_id, and seats are sent
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['show_id'], $data['user_id'], $data['seats'])) {
    $show_id = $data['show_id'];
    $user_id = $data['user_id'];
    $seats = $data['seats'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Mark selected seats as booked
        foreach ($seats as $seat) {
            $sql = "UPDATE Seats SET status = 'booked' WHERE seat_number = ? AND show_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $seat, $show_id);
            $stmt->execute();
        }

        // Insert booking details
        $sql = "INSERT INTO Bookings (user_id, show_id, num_of_tickets, booking_date) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $num_of_tickets = count($seats);
        $stmt->bind_param("iii", $user_id, $show_id, $num_of_tickets);
        $stmt->execute();

        // Commit transaction
        $conn->commit();

        // Send success response
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error booking seats: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data received.']);
}
?>
