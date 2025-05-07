document.addEventListener('DOMContentLoaded', function () {
    const sessions = document.querySelectorAll('.session-option');
    const seatsInput = document.getElementById('seats');

    sessions.forEach(session => {
        session.addEventListener('click', () => {
            // Remove 'selected' from all
            sessions.forEach(s => s.classList.remove('selected'));
            // Add 'selected' to the clicked one
            session.classList.add('selected');

            // Get the session ID from the data attribute
            const sessionId = session.getAttribute('sessionId');
            console.log(`Session ID: ${sessionId}`);
            // get input field
            const inputField = document.getElementById('sessionId');
            inputField.value = sessionId;

            let seatCost = session.getAttribute('cost');
            const totalCostDisplay = document.getElementById('totalCost');
            let seats = document.getElementById('seats').value;

            let total = seatCost * seats;
            totalCostDisplay.innerText = `$${total}`;
        });
    });

    // for seats input change
    seatsInput.addEventListener('input', () => {
        console.log('Seats input changed');
        const selectedSession = document.querySelector('.session-option.selected');
        if (selectedSession) {
            let seatCost = selectedSession.getAttribute('cost');
            let seats = seatsInput.value;
            const totalCostDisplay = document.getElementById('totalCost');
            let total = seatCost * seats;
            totalCostDisplay.innerText = `$${total}`;
        }
    });
});