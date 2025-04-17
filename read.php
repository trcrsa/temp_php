<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With"); 

require_once 'db.php';

$table = $_GET['table'] ?? null;
if (!$table) {
    echo json_encode(["error" => "Missing table name"]);
    exit;
}
$allowedTables = [
    "Company" => ["primaryKey" => "Ticker"],
    "Stock" => ["primaryKey" => "Stock_ticker"],
    "Income_Statement" => ["primaryKey" => "Statement_id"],
    "Balance_Sheet" => ["primaryKey" => "Statement_id"],
    "Cash_Flow" => ["primaryKey" => "Statement_id"],
    "Earnings" => ["primaryKey" => "Earnings_id"]
];

$primaryKey = $allowedTables[$table]["primaryKey"];
$query = "SHOW COLUMNS FROM $table";
$result = $conn->query($query);

$columns = [];
while ($row = $result->fetch_assoc()) {
    $columns[] = $row['Field'];
}

$query = "SELECT * FROM $table WHERE 1=1";
foreach ($_GET as $key => $value) {
    if ($key !== "table" && $key !== "sort" && $key !== "direction") {
        $safeValue = $conn->real_escape_string($value); 
        $query .= " AND $key LIKE '%$safeValue%'";
    }
}

if (isset($_GET['sort']) && isset($_GET['direction'])) {
    $sortColumn = $conn->real_escape_string($_GET['sort']);
    $direction = strtoupper($_GET['direction']) === "DESC" ? "DESC" : "ASC";
    $query .= " ORDER BY $sortColumn $direction";
}

$result = $conn->query($query);

if (!$result) {
    echo json_encode(["error" => "Failed to fetch data: " . $conn->error]);
    exit;
}

$rows = $result->fetch_all(MYSQLI_ASSOC);
$response = [
    "primaryKey" => $primaryKey,
    "columns" => $columns,
    "data" => $rows
];
echo json_encode($response);

$conn->close();
?>
