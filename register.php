<?php
header('Content-Type: application/json');

// Example database connection using MySQLi
$servername = "localhost";
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "ipash_system"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

// Get the raw POST data from the request body
$data = json_decode(file_get_contents('php://input'), true);

// Check if the required data is provided
if (!isset($data['user_type'], $data['name'], $data['email'], $data['phone'], $data['password'])) {
    echo json_encode(['error' => 'Missing required fields']);
    exit();
}

// Extract user data from the incoming request
$user_type = $data['user_type'];
$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];
$password = $data['password'];

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check for existing user (by email) in the respective user table
if ($user_type == 'patient') {
    $query = "SELECT COUNT(*) FROM patients WHERE email = ?";
} elseif ($user_type == 'provider') {
    $query = "SELECT COUNT(*) FROM providers WHERE email = ?";
    // Check if specialization is provided for providers
    if (!isset($data['specialization'])) {
        echo json_encode(['error' => 'Specialization is required for providers']);
        exit();
    }
} elseif ($user_type == 'admin') {
    $query = "SELECT COUNT(*) FROM admins WHERE email = ?";
} else {
    echo json_encode(['error' => 'Invalid user type']);
    exit();
}

// Prepare the query to check if email already exists
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($email_count);
$stmt->fetch();
$stmt->close();

// If email already exists, return an error
if ($email_count > 0) {
    echo json_encode(['error' => 'Email already exists']);
    exit();
}

// Prepare the insert query based on user type
if ($user_type == 'patient') {
    $query = "INSERT INTO patients (name, email, phone, password) VALUES (?, ?, ?, ?)";
} elseif ($user_type == 'provider') {
    $specialization = $data['specialization'];
    $query = "INSERT INTO providers (name, email, phone, specialization, password) VALUES (?, ?, ?, ?, ?)";
} elseif ($user_type == 'admin') {
    $query = "INSERT INTO admins (name, email, phone, password) VALUES (?, ?, ?, ?)";
}

// Prepare and execute the query
$stmt = $conn->prepare($query);

if ($user_type == 'provider') {
    $stmt->bind_param('sssss', $name, $email, $phone, $specialization, $hashed_password);
} else {
    $stmt->bind_param('ssss', $name, $email, $phone, $hashed_password);
}

if ($stmt->execute()) {
    // Registration successful
    echo json_encode(['message' => ucfirst($user_type) . ' registered successfully.']);
} else {
    // Error occurred
    echo json_encode(['error' => 'Failed to register user. ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
