document.addEventListener('DOMContentLoaded', function () {
    const selectedSeats = new Set();

    // Add click event listeners for available seats
    document.querySelectorAll('.seat.available').forEach(seat => {
        seat.addEventListener('click', function () {
            const seatId = this.dataset.seatId;

            // Toggle seat selection
            if (this.classList.contains('selected')) {
                this.classList.remove('selected');
                selectedSeats.delete(seatId);
            } else {
                this.classList.add('selected');
                selectedSeats.add(seatId);
            }
        });
    });

    // Handle booking
    document.getElementById('bookButton').addEventListener('click', function () {
        if (selectedSeats.size > 0) {
            const selectedSeatsArray = Array.from(selectedSeats);

            // Send selected seats to the server for processing
            fetch('process_booking.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    show_id: document.getElementById('show_id').value,
                    user_id: document.getElementById('user_id').value,
                    seats: selectedSeatsArray
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Booking successful!');
                    window.location.href = 'booking_confirmation.php';
                } else {
                    alert('Booking failed. Please try again.');
                }
            })
            .catch(error => console.error('Error processing booking:', error));
        } else {
            alert("Please select at least one seat.");
        }
    });
});
