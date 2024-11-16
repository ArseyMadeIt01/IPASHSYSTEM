<?php
// Database configuration
$host = 'localhost';
$dbname = 'ipash_system';
$username = 'root';
$password = '';


// Set the response type to JSON
header('Content-Type: application/json');

// Initialize an empty array to store the fetched symptoms
$response = array();

// Fetch symptoms from the database
$sql = "SELECT * FROM symptoms"; // Adjust the table name if necessary
$result = mysqli_query($conn, $sql);

if ($result) {
    // If there are results, fetch all rows as an associative array
    $symptoms = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    // Prepare the response with success status and data
    $response['success'] = true;
    $response['data'] = $symptoms;
} else {
    // If there is an error, set the response success to false and add the error message
    $response['success'] = false;
    $response['message'] = 'Error fetching symptoms: ' . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);

// Output the response as JSON
echo json_encode($response);
?>
