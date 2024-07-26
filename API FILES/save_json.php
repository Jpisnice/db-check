<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Decode the JSON data from the request
$data = json_decode(file_get_contents("php://input"), true);

if (json_last_error() === JSON_ERROR_NONE) {
    // Save the JSON file in the current directory
    $jsonFile = 'script.json';  // file will be saved in the same directory as the PHP script
    
    // Save the data to the JSON file
    file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));
    
    // Respond with a success message and provide the download link
    echo json_encode([
        "message" => "Data saved successfully",
        "download_link" => "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/" . $jsonFile
    ]);
} else {
    // Respond with an error message if the JSON data is invalid
    echo json_encode(["error" => "Invalid JSON data"]);
}
?>
