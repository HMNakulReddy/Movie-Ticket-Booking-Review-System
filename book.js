document.addEventListener('DOMContentLoaded', function() {
    const seats = document.querySelectorAll('.seat');
    const selectedSeatsInput = document.getElementById('selected_seats');
    const confirmButton = document.getElementById('confirm-seats');
    let selectedSeats = [];

    seats.forEach(seat => {
        seat.addEventListener('click', function() {
            const seatNumber = seat.getAttribute('data-seat');
            if (!seat.classList.contains('occupied')) {
                seat.classList.toggle('selected');

                if (seat.classList.contains('selected')) {
                    selectedSeats.push(seatNumber);
                } else {
                    const index = selectedSeats.indexOf(seatNumber);
                    if (index > -1) {
                        selectedSeats.splice(index, 1);
                    }
                }
                updateSelectedSeats();
            }
        });
    });

    function updateSelectedSeats() {
        selectedSeatsInput.value = selectedSeats.join(',');
        confirmButton.disabled = selectedSeats.length === 0;
    }

    // Disable button initially if no seats are selected
    updateSelectedSeats();
});
