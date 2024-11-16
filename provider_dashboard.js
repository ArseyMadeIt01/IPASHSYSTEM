document.addEventListener('DOMContentLoaded', function () {
    // Tab functionality
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Remove 'active' class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // Add 'active' class to the clicked button and its respective content
            this.classList.add('active');
            const sectionId = this.id.replace('-tab', '-section');
            document.getElementById(sectionId).classList.add('active');
        });
    });

    // Star rating functionality
    const stars = document.querySelectorAll('.star');
    stars.forEach((star, index) => {
        star.addEventListener('click', function () {
            // Update the star display based on selected rating
            stars.forEach((s, i) => {
                s.textContent = i <= index ? '★' : '☆';
                s.classList.toggle('selected', i <= index);
            });
        });
    });

    // Handle feedback submission
    document.getElementById('submit-feedback-btn').addEventListener('click', async function (e) {
        e.preventDefault();
        const rating = [...stars].filter(star => star.textContent === '★').length;
        const message = document.getElementById('feedback-message').value;

        if (rating === 0 || message.trim() === "") {
            alert("Please provide a rating and feedback.");
            return;
        }

        // Send feedback data to the server
        try {
            const response = await fetch("submit_feedback.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ rating, message }),
            });
            const data = await response.json();
            if (data.success) {
                alert("Feedback submitted successfully!");
                document.getElementById("feedback-form").reset();
                stars.forEach(star => star.textContent = '☆'); // Reset stars after submission
            } else {
                alert("Failed to submit feedback.");
            }
        } catch (error) {
            console.error("Error:", error);
        }
    });

    // Handle appointment acceptance (accept button functionality)
    document.addEventListener('DOMContentLoaded', function () {
        // Accept Button Functionality
        document.querySelectorAll('.bg-green-500').forEach(button => {
            button.addEventListener('click', async function () {
                const patientName = this.closest('li').querySelector('span').textContent.split(' - ')[0];
                try {
                    const response = await fetch('api.php?action=acceptAppointment', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ patientName }),
                    });
                    const result = await response.json();
                    alert(`Appointment accepted for ${patientName}. ${result.message}`);
                } catch (error) {
                    console.error("Error:", error);
                }
            });
        });
    
        // Reschedule and Cancel Button Functionality
        document.querySelectorAll('.bg-yellow-500, .bg-red-500').forEach(button => {
            button.addEventListener('click', async function () {
                const action = button.classList.contains('bg-yellow-500') ? 'Reschedule' : 'Cancel';
                const patientName = this.closest('li').querySelector('span').textContent.split(' - ')[0];
                const reason = prompt(`Please provide a reason for ${action.toLowerCase()}ing this appointment:`);
    
                if (reason) {
                    try {
                        const response = await fetch('api.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                action: action.toLowerCase(),
                                patientName,
                                reason,
                            }),
                        });
                        const result = await response.json();
                        alert(`${action} notification sent to ${patientName}. ${result.message}`);
                    } catch (error) {
                        console.error("Error:", error);
                    }
                } else {
                    alert("Action canceled. Reason is required.");
                }
            });
        });
    });
    
    // Handle sending message in chat
    document.getElementById('send-message-btn').addEventListener('click', () => {
        const newMessage = document.getElementById('new-message').value;
        if (newMessage.trim() === '') {
            alert('Message cannot be empty.');
            return;
        }

        // Append the new message to chat
        const chatContainer = document.querySelector('.chat-container');
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message', 'sent');
        messageDiv.innerHTML = `
            <p class="font-semibold">Provider:</p>
            <p>${newMessage}</p>
        `;
        chatContainer.appendChild(messageDiv);

        // Clear the input field
        document.getElementById('new-message').value = '';
    });
});
