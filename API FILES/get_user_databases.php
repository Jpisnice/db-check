<?php
header("Content-Type: application/json; charset=UTF-8");

// Check if username and password are provided
if (!isset($_POST['username']) || !isset($_POST['password'])) {
    echo json_encode(["error" => "Username and password are required"]);
    exit();
}

$username = $_POST['username'];
$password = $_POST['password'];

// Database connection parameters
$servername = "localhost";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Query to get all databases
$sql = "SHOW DATABASES";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $databases = [];
    while ($row = $result->fetch_assoc()) {
        $databases[] = $row["Database"];
    }
    echo json_encode(["databases" => $databases]);
} else {
    echo json_encode(["message" => "No databases found for user '$username'"]);
}

// Close connection
$conn->close();
?>
