<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Check if necessary parameters are provided
if (!isset($_POST['username'])|| !isset($_POST['database1']) || !isset($_POST['database2'])) {
    echo json_encode(["error" => "Username, password, and database names are required"]);
    exit();
}

$username = $_POST['username'];
$password = $_POST['password'];
$database1 = $_POST['database1'];
$database2 = $_POST['database2'];

// Database connection parameters
$servername = "localhost";

// Create connection
$conn1 = new mysqli($servername, $username, $password, $database1);
$conn2 = new mysqli($servername, $username, $password, $database2);

// Check connection
if ($conn1->connect_error || $conn2->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn1->connect_error . " / " . $conn2->connect_error]);
    exit();
}

function getTableColumns($conn, $database) {
    $sql = "SELECT table_name, column_name, data_type 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE table_schema = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $database);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $tables = [];
    while ($row = $result->fetch_assoc()) {
        $tables[$row['table_name']][$row['column_name']] = $row['data_type'];
    }
    return $tables;
}

$tables1 = getTableColumns($conn1, $database1);
$tables2 = getTableColumns($conn2, $database2);

$comparisons = [];

foreach ($tables1 as $table1 => $columns1) {
    foreach ($tables2 as $table2 => $columns2) {
        $commonFields = array_intersect_key($columns1, $columns2);
        
        if (!empty($commonFields)) {
            $fieldTypes = [];
            foreach ($commonFields as $field => $type) {
                $fieldTypes[$field] = $type;
            }
            
            $comparisons[] = [
                "tableName1" => $table1,
                "tableName2" => $table2,
                "similarities" => [
                    "commonFields" => array_keys($commonFields),
                    "fieldTypes" => $fieldTypes,
                    "totalSimilarFields" => count($commonFields)
                ]
            ];
        }
    }
}

$response = [
    "userId" => $username,
    "databases" => [
        [
            "dbName1" => $database1,
            "dbName2" => $database2,
            "tableComparisons" => $comparisons
        ]
    ]
];

echo json_encode($response);

$conn1->close();
$conn2->close();
?>
