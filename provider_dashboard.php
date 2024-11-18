<?php
    include_once "db.php";
    session_start();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST["id"]) && isset($_POST["status"])) {
            $stmt = $db->prepare("UPDATE appointments SET status = :status WHERE id = :id");
            $stmt->bindParam(':id', $_POST["id"]);
            $stmt->bindParam(':status', $_POST["status"]);
            $stmt->execute();
        }
    } 
    $stmt = $db->prepare("SELECT * FROM patients");
    $stmt->execute();
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Provider Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Custom dropdown styling */
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 200px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 0.5rem;
            padding: 1rem;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .notification-icon {
            position: relative;
            cursor: pointer;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .tab-content {
            background-color: #f3e8ff; /* Light purple background */
        }

        .section-title {
            color: #6b21a8; /* Dark purple */
        }

        .call-item {
            border: 1px solid #e5e7eb; /* Light gray border */
            border-radius: 0.375rem; /* Rounded corners */
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: white;
        }

        /* Custom styles for chat messages */
        .chat-container {
            max-height: 300px;
            overflow-y: auto;
        }

        .message {
            border-radius: 0.375rem; /* Rounded corners */
            padding: 0.75rem;
            margin-bottom: 1rem;
            max-width: 70%;
        }

        .message.sent {
            background-color: #e0ffe0; /* Light green background */
            align-self: flex-end;
            text-align: right;
        }

        .message.received {
            background-color: #f0f0f0; /* Light gray background */
            align-self: flex-start;
            text-align: left;
        }

        .hidden {
            display: none;
        }

        .visible {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-6">
    <div class="bg-white p-8 rounded-lg shadow-md">
        <!-- Dashboard Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Provider Dashboard</h1>
            <div class="relative flex items-center space-x-4">
                <div class="notification-icon">
                    <img src="https://via.placeholder.com/30" alt="Notifications" class="w-8 h-8">
                    <div class="notification-badge">3</div>
                </div>
                <div class="relative">
                    <button class="flex items-center space-x-2 dropdown">
                        <img src="https://via.placeholder.com/40" alt="Profile" class="w-10 h-10 rounded-full">
                    </button>
                    <!-- Dropdown Content -->
                    <div class="dropdown-content mt-2 rounded-lg shadow-lg">
                        <h2 class="text-lg font-semibold">Dr. John Doe</h2>
                        <button id="edit-profile-btn" class="mt-2 w-full text-left text-blue-600 hover:underline">Edit Profile</button>
                        <button id="logout-btn" class="mt-2 w-full text-left text-red-600 hover:underline">Logout</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs for Navigation -->
        <div class="flex space-x-4 mb-6">
            <button id="appointments-tab" class="tab-btn px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition ease-in-out duration-200 active">Appointments</button>
            <button id="patient-management-tab" class="tab-btn px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition ease-in-out duration-200">Patient Management</button>
            <button id="communication-tab" class="tab-btn px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600 transition ease-in-out duration-200">Communication</button>
            <!-- <button id="feedback-tab" class="tab-btn px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition ease-in-out duration-200">Feedback</button> -->
        </div>

        <!-- Tab Content -->
        <div id="appointments-section" class="tab-content bg-blue-50 p-6 rounded-lg shadow-sm active">
            <h2 class="text-xl font-semibold text-blue-800 mb-4">Manage Appointments</h2>
            <ul id="appointments-list" class="space-y-4 text-blue-700"></ul>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                fetch('view-appointments.php')
                    .then(response => response.json())
                    .then(data => {
                        const appointmentsList = document.getElementById('appointments-list');
                        appointmentsList.innerHTML = '';
        
                        data.forEach(appointment => {
                            const listItem = document.createElement('li');
                            listItem.classList.add('flex', 'justify-between', 'items-center');
                            
                            const statusClass = appointment.status === 'accepted' ? 'text-green-500' :
                                appointment.status === 'canceled' ? 'text-red-500' : 'text-yellow-500';
                            if (appointment.status != 'canceled' && appointment.provider === "<?php echo  $_SESSION['user'];?>") {
                               listItem.innerHTML = `
                                
                                <span class="${statusClass}">${appointment.provider} - ${new Date(appointment.appointment_date).toLocaleDateString()} ${appointment.appointment_time}</span>
                                <div class="flex space-x-2">
                                    <form action="provider_dashboard.php" method="post">
                                        <input name="id" type = "hidden" value="${appointment.id}">
                                        <input name="status" type = "hidden" value="accepted">
                                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600" onclick="updateAppointmentStatus(${appointment.id}, 'accepted')">Accept</button>
                                    </form>
                                    <!--
                                    <button class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600" onclick="promptRescheduleOrCancel(${appointment.id}, 'Reschedule')">Reschedule</button>
                                    -->
                                    <form action="provider_dashboard.php" method="post">
                                        <input name="id" type = "hidden" value="${appointment.id}">
                                        <input name="status" type = "hidden" value="canceled">
                                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600" onclick="updateAppointmentStatus(${appointment.id}, 'Canceled')">Cancel</button>
                                    </form>
                                </div>
                                `;
                                appointmentsList.appendChild(listItem);
                            }
                        });
                    })
                    .catch(error => console.error('Error fetching appointments:', error));
            });
        
            function updateAppointmentStatus(appointmentId, newStatus) {
                // Update the status in the database using AJAX or fetch
                fetch('provider_dashboard.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: appointmentId,
                        status: newStatus
                    })
                });
            }
        
            function promptRescheduleOrCancel(appointmentId, action) {
                const reason = prompt(`Please provide a reason for ${action.toLowerCase()}:`);
                if (reason) {
                    // Update appointment status in the database with the reason (e.g., "rescheduled" or "canceled")
                    fetch('update-appointment-status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            appointmentId: appointmentId,
                            status: action.toLowerCase(),
                            reason: reason
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(`${action} appointment successful.`);
                            location.reload();  // Refresh the page to reflect changes
                        } else {
                            alert(`Failed to ${action.toLowerCase()} appointment.`);
                        }
                    })
                    .catch(error => console.error('Error updating status:', error));
                }
            }
        </script>
                

        <div id="patient-management-section" class="tab-content bg-green-50 p-6 rounded-lg shadow-sm hidden">
            <h2 class="text-xl font-semibold text-green-800 mb-4">Patient Management</h2>
            <div>
                <h3 class="text-lg font-semibold text-green-700 mb-2">Patient Records</h3>
                <ul class="space-y-2 text-green-700">
                   <li class="flex justify-between items-center">
                        <span>John Smith: Last Visit - Aug 10, 2024</span>
                        <a href="patient_details.html" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">View</a>
                    </li>
                    <li class="flex justify-between items-center">
                        <span>Jane Doe: Last Visit - Aug 8, 2024</span>
                        <a href="patient_details.html" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">View</a>
                    </li>
                </ul>
            </div>
        </div>
      
        <div id="communication-section" class="tab-content bg-purple-50 p-6 rounded-lg shadow-sm hidden">
            <div class="flex space-x-6">
                <!-- Messages Section -->
            <div id="messages-section" class="bg-gradient-to-r from-yellow-100 to-yellow-50 p-6 rounded-xl shadow-lg">
                <h2 class="text-2xl font-semibold text-yellow-900 mb-4">Messages</h2>

             <!-- Messages List -->
                <div class="mb-6">
                    <ul class="list-disc pl-5 text-yellow-800">
                        <!-- <li><strong>Dr. Lee:</strong> Your blood test results are ready.</li>
                        <li><strong>Nurse Sarah:</strong> Please remember to schedule your follow-up appointment.</li> -->
                        <?php
                            $stmt = $db->prepare("SELECT * FROM messages WHERE recipient = :user");
                            $stmt->bindParam(':user', $_SESSION['user']);
                            $stmt->execute();
                            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            if (!empty($messages)) {
                                foreach ($messages as $message) {
                                    echo "<li><strong>" . $message["sender"] .  ":</strong> " . $message["message"] .  "</li>";
                                }
                            }
                        ?>
                    </ul>
                </div>

                <!-- Send a Message -->
                <div class="bg-white p-8 rounded-lg shadow-xl">
                    <h1 class="text-2xl font-bold text-yellow-900 mb-6">Send a Message</h1>
                    <form id="message-form" action="send_message.php" method="POST" class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-2">From</label>
                            <select name="sender" class="w-full p-3 border border-yellow-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:outline-none" required>
                                <!-- <option value="dr_lee">Dr. Lee</option>
                                <option value="nurse_sarah">Nurse Sarah</option> -->
                                <?php
                                    if (!empty($patients)) {
                                        foreach ($patients as $patient) {
                                            echo '<option value="'.$patient['name'].'">'.$patient['name'].'</option>';
                                        } 
                                    }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-2">Message</label>
                            <textarea name="message" rows="4" class="w-full p-3 border border-yellow-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:outline-none" required></textarea>
                        </div>
                        <div>                            
                            <input type="hidden" name = "recipient" value = "<?php echo $_SESSION['user'];?>">
                            <input type="hidden" name = "return" value = "provider_dashboard.php">
                            <button type="submit" class="w-full bg-yellow-500 text-white py-3 rounded-lg shadow-lg hover:bg-yellow-600 transition-all duration-200">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>

                <!-- Video Call Section 
                <div class="w-1/2 bg-white p-4 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold section-title mb-4">Video Call</h2>
                    <ul class="space-y-4">
                        <li class="call-item">
                            <h3 class="text-lg font-semibold">John Smith - Aug 12, 2024, 10:00 AM</h3>
                            <a href="video_webrtc.html"  class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Make Call</a>
                        </li>
                        <li class="call-item">
                            <h3 class="text-lg font-semibold">Jane Doe - Aug 15, 2024, 2:00 PM</h3>
                            <a href="video_webrtc.html"  class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Make Call</a>
                        </li>
                    </ul>
                </div> -->
            </div>
        </div>
        <!--feedback section-->

        <div id="feedback-section" class="tab-content bg-red-50 p-6 rounded-lg shadow-sm hidden flex justify-center items-center">
            <div class="w-full max-w-md bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold text-red-800 mb-4 text-center">Feedback</h2>
                <form id="feedback-form" class="space-y-4">
                    <div>
                        <label for="feedback-rating" class="block text-lg font-semibold text-center">Rate Our Service</label>
                        <div id="feedback-rating" class="flex justify-center space-x-1 text-yellow-500">
                            <span class="star text-2xl cursor-pointer">&#9734;</span>
                            <span class="star text-2xl cursor-pointer">&#9734;</span>
                            <span class="star text-2xl cursor-pointer">&#9734;</span>
                            <span class="star text-2xl cursor-pointer">&#9734;</span>
                            <span class="star text-2xl cursor-pointer">&#9734;</span>
                        </div>
                    </div>
                    <div>
                        <label for="feedback-message" class="block text-lg font-semibold text-center">Your Feedback</label>
                        <textarea id="feedback-message" rows="3" class="w-full p-2 border border-gray-300 rounded"></textarea>
                    </div>
                    <div class="flex justify-center">
                        <button id="submit-feedback-btn" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Submit Feedback</button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>

    <script src="provider_dashboard.js"></script>
        
</body>
</html>
