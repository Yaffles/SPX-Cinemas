document.addEventListener('DOMContentLoaded', function () {
    const sessions = document.querySelectorAll('.session-option');
    const seatsInput = document.getElementById('seats');

    sessions[0].classList.add('selected'); // Default select the first session

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

    document.getElementById('locationFilter').addEventListener('change', function () {
    const selected = this.value.toLowerCase();
    document.querySelectorAll('.location').forEach(loc => {
        const locationName = loc.getAttribute('data-location').toLowerCase();
        if (selected === 'all' || locationName === selected) {
            loc.style.display = '';
        } else {
            loc.style.display = 'none';
        }
    });
});

    // load on the fly as too many iframes can cause performance issues
    document.querySelectorAll('.movie-button').forEach(button => {
        button.addEventListener('click', () => {
            const dialog = button.nextElementSibling;
            console.log('Button clicked, opening dialog');
            const container = dialog.querySelector('.trailer-container');

            // Only load iframe once
            if (!container.querySelector('iframe')) {
                const iframe = document.createElement('iframe');
                iframe.width = 560;
                iframe.height = 315;
                iframe.src = container.dataset.src;
                iframe.title = "YouTube video player";
                iframe.frameBorder = 0;
                iframe.allow =
                    "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share";
                iframe.allowFullscreen = true;

                container.appendChild(iframe);
            }
        });
    });


});