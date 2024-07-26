<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['database1']) || !isset($_POST['database2'])) {
    echo json_encode(["error" => "Username, password, and database names are required"]);
    exit();
}

$username = $_POST['username'];
$password = $_POST['password'];
$database1 = $_POST['database1'];
$database2 = $_POST['database2'];

$servername = "localhost";

$conn1 = new mysqli($servername, $username, $password, $database1);
$conn2 = new mysqli($servername, $username, $password, $database2);

if ($conn1->connect_error || $conn2->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn1->connect_error . " / " . $conn2->connect_error]);
    exit();
}

function getTables($conn, $database) {
    $sql = "SELECT table_name FROM INFORMATION_SCHEMA.TABLES WHERE table_schema = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $database);
    $stmt->execute();
    $result = $stmt->get_result();

    $tables = [];
    while ($row = $result->fetch_assoc()) {
        $tables[] = $row['table_name'];
    }
    return $tables;
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

$tables1 = getTables($conn1, $database1);
$tables2 = getTables($conn2, $database2);

$matchingTables = array_intersect($tables1, $tables2);
$missingTablesInDb1 = array_diff($tables1, $tables2);
$missingTablesInDb2 = array_diff($tables2, $tables1);

$columns1 = getTableColumns($conn1, $database1);
$columns2 = getTableColumns($conn2, $database2);

$comparisons = [];

foreach ($matchingTables as $table) {
    $commonFields = array_intersect_key($columns1[$table], $columns2[$table]);
    
    $fieldTypes = [];
    foreach ($commonFields as $field => $type) {
        $fieldTypes[$field] = $type;
    }
    
    $comparisons[] = [
        "tableName1" => $table,
        "tableName2" => $table,
        "similarities" => [
            "commonFields" => array_keys($commonFields),
            "fieldTypes" => $fieldTypes,
            "totalSimilarFields" => count($commonFields)
        ]
    ];
}

$response = [
    "userId" => $username,
    "databases" => [
        [
            "dbName1" => $database1,
            "dbName2" => $database2,
            "matchingTables" => $matchingTables,
            "missingTables" => [
                "inDb1" => $missingTablesInDb2,
                "inDb2" => $missingTablesInDb1
                
            ],
            "tableComparisons" => $comparisons
        ]
    ]
];

echo json_encode($response);

$conn1->close();
$conn2->close();
?>
