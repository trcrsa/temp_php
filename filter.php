<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Include the database connection
require_once 'db.php';

// Check if the table is provided
$table = $_GET['table'] ?? null;
if (!$table) {
    echo json_encode(["error" => "Missing table name"]);
    exit;
}

// Fetch distinct values for all columns in the table
$query = "SHOW COLUMNS FROM $table";
$result = $conn->query($query);

if (!$result) {
    echo json_encode(["error" => "Error fetching columns"]);
    exit;
}

$columns = [];
while ($row = $result->fetch_assoc()) {
    $columns[] = $row['Field'];
}

$filterOptions = [];
foreach ($columns as $column) {
    $distinctQuery = "SELECT DISTINCT $column FROM $table";
    $distinctResult = $conn->query($distinctQuery);

    if ($distinctResult) {
        $filterOptions[$column] = [];
        while ($distinctRow = $distinctResult->fetch_assoc()) {
            $filterOptions[$column][] = $distinctRow[$column];
        }
    }
}

echo json_encode($filterOptions);

$conn->close();
?>
