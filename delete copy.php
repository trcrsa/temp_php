<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['table']) || !isset($data['id'])) {
    echo json_encode(["error" => "Table name and ID are required."]);
    exit;
}
$table = $data['table'];
$id = $data['id'];
$primaryKeys = [
    "Company" => "Ticker",
    "Stock" => "Stock_ticker",
    "Income_Statement" => "Statement_id",
    "Balance_Sheet" => "Statement_id",
    "Cash_Flow" => "Statement_id",
    "Earnings" => "Earnings_id"
];

$primaryKey = $primaryKeys[$table];

$query = "DELETE FROM $table WHERE $primaryKey = ?";
$stmt = $conn->prepare($query);

$stmt->bind_param("s", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["message" => "Record deleted successfully."]);
    } else {
        echo json_encode(["error" => "No record found with the given ID."]);
    }
} else {
    echo json_encode(["error" => "Failed to delete record: " . $stmt->error]);
}

$stmt->close();
$conn->close();
