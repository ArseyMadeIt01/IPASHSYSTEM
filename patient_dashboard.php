<?php
    include_once "db.php";
    session_start();
    if(!isset($_SESSION['user'])) {
        header("Location: index.html");
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST["id"])) {
            $stmt = $db->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = :id");
            $stmt->bindParam(':id', $_POST["id"]);
            $stmt->execute();
        }
    } 

    $appointments = [];
    $stmt = $db->prepare("SELECT * FROM appointments WHERE patient = :name AND (status = 'scheduled' OR status = 'accepted')");
    $stmt->bindParam(':name', $_SESSION['user']);
    $stmt->execute();
    $allRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($allRows as $row) {
        $appointment = [
            'doctor_name' => $row['provider'] . ' - ' .$row['provider_specialization'],
            'date' => $row['appointment_date'],
            'time' => $row['appointment_time'],
            'status' => $row['status'],
            'id' => $row['id'],
        ];
        array_push($appointments, $appointment);
    }
    $stmt = $db->prepare("SELECT * FROM providers");
    $stmt->execute();
    $providers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Custom style for the profile dropdown */
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

        /* Custom style for file preview */
        .file-preview {
            margin-top: 1rem;
            padding: 1rem;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
        }

        /* Hidden sections */
        .hidden {
            display: none;
        }
        .rating input:checked ~ label {
    color: #f5c518; /* Gold color for filled stars */
}
.rating label {
    font-size: 2rem;
    margin-right: 0.1rem;
}

    </style>
</head>
<body class="bg-gray-100 min-h-screen p-6">
    <div class="bg-white p-6 rounded-lg shadow-md max-w-6xl mx-auto">

       <!-- Dashboard Header -->
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Patient Dashboard</h1>
    <div class="flex items-center space-x-4">
        <!-- Notifications Icon -->
        <button class="relative">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405C19.842 15.237 20 14.374 20 13.5V10a8 8 0 10-16 0v3.5c0 .874.158 1.737.405 2.495L3 17h5m4 4a2 2 0 11-4 0h4z"></path>
            </svg>
            <!-- Notification Badge -->
            <span
                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">3</span>
        </button>
        <!-- Profile Icon -->
        <div class="dropdown relative">
            <button class="flex items-center space-x-2">
                <img src="https://via.placeholder.com/40" alt="Profile" class="w-10 h-10 rounded-full">
                <span class="font-medium text-gray-700"><?php echo $_SESSION['user'];?></span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <!-- Dropdown Content -->
            <div class="dropdown-content mt-2 rounded-lg shadow-lg">
                <h2 class="text-lg font-semibold"><?php echo $_SESSION['user'];?></h2>
                <p><?php echo $_SESSION['email'];?></p>
                <p><?php echo $_SESSION['phone'];?></p>
                <button id="editProfileBtn" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Edit Profile</button>
                <a href="logout.html" class="mt-4 inline-block bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- Editable Profile Form (Initially Hidden) -->
<div id="editProfileForm" class="hidden mt-4 p-4 border rounded bg-gray-100">
    <h3 class="text-lg font-semibold mb-2">Edit Profile</h3>
    <form id="profileForm">
        <div class="mb-4">
            <label class="block text-gray-700" for="email">Email</label>
            <input class="border rounded w-full px-3 py-2" type="email" id="email" value="john.doe@example.com">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700" for="phone">Phone</label>
            <input class="border rounded w-full px-3 py-2" type="text" id="phone" value="+1 234 567 890">
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Save Changes</button>
        <button type="button" id="cancelEdit" class="ml-2 bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Cancel</button>
    </form>
</div>

<script>
    // Show/Hide Edit Profile Form
    document.getElementById('editProfileBtn').addEventListener('click', function() {
        const form = document.getElementById('editProfileForm');
        form.classList.toggle('hidden');
    });

    // Cancel Edit Profile
    document.getElementById('cancelEdit').addEventListener('click', function() {
        document.getElementById('editProfileForm').classList.add('hidden');
    });

    // Handle Profile Form Submission
    document.getElementById('profileForm').addEventListener('submit', function(event) {
        event.preventDefault();
        // Add your form submission logic here
        alert('Profile updated!');
        document.getElementById('editProfileForm').classList.add('hidden');
    });
</script>


        <!-- Tabs for Navigation -->
        <div class="mb-6">
            <button id="appointments-tab" class="tab-button px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Appointments</button>
            <button id="health-data-tab" class="tab-button px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 ml-4">Health Data</button>
            <button id="communication-tab" class="tab-button px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 ml-4">Communication</button>
            <!-- <button id="feedback-tab" class="tab-button px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600 ml-4">Feedback</button> -->
        </div>

        <!-- Appointments Section -->
<div id="appointments-section" class="section bg-gray-50 p-6 rounded-lg shadow-lg">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Your Appointments</h2>
    
    <!-- Container for grid layout -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Appointment list on the left -->
        <div id="appointment-list" class="space-y-4">
            <!-- Placeholder appointment card -->
             <?php
                if (!empty($appointments)) {
                    foreach ($appointments as $appointment) {
                        echo '
                        <form action="patient_dashboard.php" method ="post" class="bg-gradient-to-r from-purple-100 to-purple-50 p-4 rounded-lg shadow-md">
                            <input name="id" type = "hidden" value="'.$appointment["id"].'">
                            <p class="text-lg font-semibold text-purple-900">'.$appointment["doctor_name"].'</p>
                            <p class="text-sm text-purple-700">Date: '.$appointment["date"].'</p>
                            <p class="text-sm text-purple-700">Time: '.$appointment["time"].'</p>
                            <p class="text-sm text-purple-700">Status: '.$appointment["status"].'</p>
                            <!-- -->
                            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 transition-all duration-200">Cancel Appointment</button>
                            <!-- -->
                        </form>
                        ' ;         
                    }
                }
             ?>
            
            <!-- Add more appointment cards as needed -->
        </div>
        
        <!-- Book an Appointment Form on the right -->
        <div class="bg-white p-8 rounded-lg shadow-xl">
            <h1 class="text-2xl font-bold text-blue-900 mb-6">Book an Appointment</h1>
            <form id="appointment-form" action="schedule_appointment.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Date Selection -->
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">Select Date</label>
                    <input type="date" name="appointment_date" class="w-full p-3 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                </div>
                
                <!-- Time Selection -->
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">Select Time</label>
                    <input type="time" name="appointment_time" class="w-full p-3 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                </div>
                
                <!-- Specialization Selection -->
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">Provider Specialization</label>
                    <select id = 'select1' name="provider_specialization" class="w-full p-3 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                        <!-- <option value="general">General Practitioner</option>
                        <option value="cardiologist">Cardiologist</option> -->
                        <!-- More specializations as needed -->
                        <?php
                            if (!empty($providers)) {
                                foreach ($providers as $provider) {
                                    echo '<option value="'.$provider['specialization'].'">'.$provider['specialization'].'</option>';
                                } 
                            }
                        ?>
                    </select>
                </div>
                
                <!-- Provider Selection -->
                <div>
                    <label class="block font-semibold text-gray-700 mb-2">Select Provider</label>
                    <select id = 'select2' name="provider" class="w-full p-3 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                        <!-- <option value="dr_smith">Dr. Smith</option> -->
                        <?php
                            if (!empty($providers)) {
                                foreach ($providers as $provider) {
                                    echo '<option value="'.$provider['name'].'">'.$provider['name'].'</option>';
                                }
                            }
                        ?>
                    </select>
                </div>
                
                <!-- Submit Button -->
                <div class="md:col-span-2">
                    <button type="submit" class="w-full bg-blue-500 text-white py-3 rounded-lg shadow-lg hover:bg-blue-600 transition-all duration-200">Book Appointment</button>
                </div>
                <script>
                    function synchronizeSelectTags() {
                        const select1 = document.getElementById('select1');
                        const select2 = document.getElementById('select2');

                        select1.addEventListener('change', () => {
                            select2.selectedIndex = select1.selectedIndex;
                        });

                        select2.addEventListener('change', () => {
                            select1.selectedIndex = select2.selectedIndex;
                        });
                    }

                    // Call the function to initialize the synchronization
                    synchronizeSelectTags();

                </script>
            </form>
        </div>
    </div>
</div>

      <!-- Health Data Section -->
<div id="health-data-section" class="section hidden">
    <h2 class="text-3xl font-bold mb-6 text-green-800">Your Health Data</h2>

    <!-- Grid Layout for Subsections -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Health Data -->
        <div class="bg-gradient-to-r from-green-100 to-green-50 p-6 rounded-xl shadow-lg">
            <h2 class="text-2xl font-semibold text-green-900 mb-4">Health Data</h2>
            <div class="space-y-3 text-green-800">
                <span class="block">Blood Pressure: <strong>120/80 mmHg</strong></span>
                <span class="block">Blood Sugar: <strong>90 mg/dL</strong></span>
                <span class="block">Heart Rate: <strong>72 bpm</strong></span>
            </div>
        </div>

        <!-- Medication Management -->
        <div class="bg-gradient-to-r from-yellow-100 to-yellow-50 p-6 rounded-xl shadow-lg">
            <h2 class="text-2xl font-semibold text-yellow-900 mb-4">Medication Management</h2>
            <ul class="space-y-3 text-yellow-800">
                <li class="border-b border-yellow-200 pb-2">Metformin - <strong>500 mg, 1x daily</strong></li>
                <li class="border-b border-yellow-200 pb-2">Lisinopril - <strong>10 mg, 1x daily</strong></li>
            </ul>
        </div>

        <!-- Symptom Tracker -->
        <div class="bg-gradient-to-r from-red-100 to-red-50 p-6 rounded-xl shadow-lg">
            <h2 class="text-2xl font-semibold text-red-900 mb-4">Symptom Tracker</h2>
                    <form action="submit-symptoms.php" method="POST">
                        <label for="symptoms">Describe Your Symptoms:</label>
                        <textarea id="symptoms" name="symptoms" rows="4" cols="50" required></textarea>
                        
                        <button type="submit"
                            class="bg-red-500 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 transition-all duration-300">
                            Submit Symptoms
                        </button>
                    </form>
                    
        </div>

        <!-- Download Health Report -->
        <div class="bg-white p-8 rounded-lg shadow-xl">
            <h1 class="text-2xl font-bold text-green-900 mb-6">Download Health Report</h1>
            <div id="download-section" class="space-y-4">
                <!-- Dynamic file download options will be injected here -->
            </div>
        </div>
    </div>
</div>
      <!-- Health Data Section -->
<div id="health-data-section" class="section hidden">
    <h2 class="text-3xl font-bold mb-6 text-green-800">Your Health Data</h2>

    <!-- Grid Layout for Subsections -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Health Data -->
        <div class="bg-gradient-to-r from-green-100 to-green-50 p-6 rounded-xl shadow-lg">
            <h2 class="text-2xl font-semibold text-green-900 mb-4">Health Data</h2>
            <div class="space-y-3 text-green-800">
                <span class="block">Blood Pressure: <strong>120/80 mmHg</strong></span>
                <span class="block">Blood Sugar: <strong>90 mg/dL</strong></span>
                <span class="block">Heart Rate: <strong>72 bpm</strong></span>
            </div>
        </div>

        <!-- Medication Management -->
        <div class="bg-gradient-to-r from-yellow-100 to-yellow-50 p-6 rounded-xl shadow-lg">
            <h2 class="text-2xl font-semibold text-yellow-900 mb-4">Medication Management</h2>
            <ul class="space-y-3 text-yellow-800">
                <li class="border-b border-yellow-200 pb-2">Metformin - <strong>500 mg, 1x daily</strong></li>
                <li class="border-b border-yellow-200 pb-2">Lisinopril - <strong>10 mg, 1x daily</strong></li>
            </ul>
        </div>

        <!-- Symptom Tracker -->
        <div class="bg-gradient-to-r from-red-100 to-red-50 p-6 rounded-xl shadow-lg">
            <h2 class="text-2xl font-semibold text-red-900 mb-4">Symptom Tracker</h2>
            <form id="symptom-tracker-form" class="flex flex-col space-y-3">
                <textarea class="p-4 border border-red-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                    rows="3" placeholder="Log your symptoms"></textarea>
                <button type="submit"
                    class="bg-red-500 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 transition-all duration-300">
                    Submit Symptoms
                </button>
            </form>
        </div>

        <!-- Download Health Report -->
        <div class="bg-white p-8 rounded-lg shadow-xl">
            <h1 class="text-2xl font-bold text-green-900 mb-6">Download Health Report</h1>
            <div id="download-section" class="space-y-4">
                <!-- Dynamic file download options will be injected here -->
            </div>
        </div>
    </div>
</div>



    <!-- Communication -->
<div id="communication-section" class="section" style="display: none;">
    <h2 class="text-3xl font-bold mb-6 text-yellow-800">Your Communications</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
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
                                echo "<li><strong>" . $message["sender"].  ":</strong>" . $message["message"].  "</li>";
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
                        <label class="block font-semibold text-gray-700 mb-2">To</label>
                        <select name="recipient" class="w-full p-3 border border-yellow-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:outline-none" required>
                            <!-- <option value="dr_lee">Dr. Lee</option>
                            <option value="nurse_sarah">Nurse Sarah</option> -->
                            <?php
                            if (!empty($providers)) {
                                foreach ($providers as $provider) {
                                    echo '<option value="'.$provider['name'].'">'.$provider['name'].'</option>';
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
                        <input type="hidden" name = "sender" value = "<?php echo $_SESSION['user'];?>">
                        <input type="hidden" name = "return" value = "patient_dashboard.php">
                        <button type="submit" class="w-full bg-yellow-500 text-white py-3 rounded-lg shadow-lg hover:bg-yellow-600 transition-all duration-200">Send Message</button>
                    </div>
                </form>
            </>
        </div>

        <!-- Video Calls Section 
        <div id="video-calls-section" class="bg-gradient-to-r from-purple-100 to-purple-50 p-6 rounded-xl shadow-lg">
            <h2 class="text-2xl font-semibold text-purple-900 mb-4">Video Calls</h2>

           
            <div class="mb-6">
                <ul class="list-disc pl-5 text-purple-800">
                    <li><strong>Dr. Johnson:</strong> 2024-09-10, 11:00 AM</li>
                    <li><strong>Dr. Carter:</strong> 2024-09-15, 02:00 PM</li>
                </ul>
            </div>

            <div class="bg-white p-8 rounded-lg shadow-xl">
                <h1 class="text-2xl font-bold text-purple-900 mb-6">Schedule a Video Call</h1>
                <form action="schedule-video-call.php" method="POST">
                    <label for="call_date">Date:</label>
                    <input type="date" id="call_date" name="call_date" required class="w-full p-2 mb-4 border rounded">
                
                    <label for="call_time">Time:</label>
                    <input type="time" id="call_time" name="call_time" required class="w-full p-2 mb-4 border rounded">
                
                    <label for="recipient">Recipient:</label>
                    <input type="text" id="recipient" name="recipient" required class="w-full p-2 mb-4 border rounded">
                
                    <button type="submit" class="w-full bg-purple-500 text-white py-3 rounded-lg shadow-lg hover:bg-purple-600 transition-all duration-200">
                        Schedule Video Call
                    </button>
                </form>
                
                </form>
            </div>
        </div> -->
    </div>
</div>

<!-- Feedback Section -->
<div id="feedback-section" class="section" style="display: none;">
    <h2 class="text-3xl font-bold mb-6 text-purple-800">Feedback</h2>

    <!-- Feedback Form -->
    <div class="bg-gradient-to-r from-purple-100 to-purple-50 p-6 rounded-xl shadow-lg mb-6">
        <h2 class="text-2xl font-semibold text-purple-900 mb-4">Leave Your Feedback</h2>
        <form id="feedback-form" action="submit-feedback.php" method="POST" class="grid grid-cols-1 gap-6">
            <!-- Rating System -->
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Rate Us</label>
                <div class="flex items-center">
                    <input type="radio" name="rating" value="1" id="star1" class="hidden" />
                    <label for="star1" class="cursor-pointer text-yellow-500">&#9733;</label>
                    <input type="radio" name="rating" value="2" id="star2" class="hidden" />
                    <label for="star2" class="cursor-pointer text-yellow-500">&#9733;</label>
                    <input type="radio" name="rating" value="3" id="star3" class="hidden" />
                    <label for="star3" class="cursor-pointer text-yellow-500">&#9733;</label>
                    <input type="radio" name="rating" value="4" id="star4" class="hidden" />
                    <label for="star4" class="cursor-pointer text-yellow-500">&#9733;</label>
                    <input type="radio" name="rating" value="5" id="star5" class="hidden" />
                    <label for="star5" class="cursor-pointer text-yellow-500">&#9733;</label>
                </div>
            </div>

            <!-- Feedback Message -->
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Your Feedback</label>
                <textarea name="feedback" rows="4" class="w-full p-3 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:outline-none" placeholder="Share your thoughts..." required></textarea>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" class="w-full bg-purple-500 text-white py-3 rounded-lg shadow-lg hover:bg-purple-600 transition-all duration-200">Submit Feedback</button>
            </div>
        </form>
    </div>
</div>



    <script src="patient_dashboard.js"></script>
</body>

</html>
