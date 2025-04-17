<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once 'db.php';

$allowedTables = [
    "Company" => ["primaryKey" => "Ticker", "columns" => ["Name", "Sector", "Industry", "Ticker"]],
    "Stock" => ["primaryKey" => "Stock_ticker", "columns" => ["Stock_ticker", "Eod_price", "IPO_date"]],
    "Income_Statement" => [
        "primaryKey" => "Statement_id",
        "columns" => [
            "Stock_ticker", "Fiscal_year_end", "Total_revenue", "Cost_of_revenue",
            "Gross_profit", "Operating_income", "Net_income_common_stockholders",
            "Pretax_income", "Ebit", "Ebitda"
        ]
    ],
    "Balance_Sheet" => [
        "primaryKey" => "Statement_id",
        "columns" => [
            "Stock_ticker", "Fiscal_year_end", "Total_assets", "Current_assets",
            "Cash_and_cash_equivalents", "Accounts_receivable", "Total_debt", "Long_term_debt",
            "Net_tangible_assets", "Working_capital", "Invested_capital", "Tangible_book_value",
            "Total_capitalization", "Shares_issued", "Stockholders_equity",
            "Retained_earnings", "Common_stock_equity"
        ]
    ],
    "Cash_Flow" => [
        "primaryKey" => "Statement_id",
        "columns" => [
            "Stock_ticker", "Fiscal_year_end", "Operating_cashflow", "Capital_expenditures",
            "Free_cash_flow", "Cash_dividends_paid"
        ]
    ],
    "Earnings" => [
        "primaryKey" => "Earnings_id",
        "columns" => ["Stock_ticker", "Fiscal_date_end", "Reported_eps"]
    ]
];

$table = $_GET['table'] ?? null;


$primaryKey = $allowedTables[$table]["primaryKey"];
$columns = $allowedTables[$table]["columns"];

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data[$primaryKey])) {
    echo json_encode(["error" => "Primary key and data are required."]);
    exit;
}

$primaryKey = $allowedTables[$table]["primaryKey"];
$id = $data[$primaryKey];
unset($data[$primaryKey]); 



$data = array_filter($data, fn($key) => in_array($key, $columns), ARRAY_FILTER_USE_KEY);

if (empty($data)) {
    echo json_encode(["error" => "No valid fields to update."]);
    exit;
}


$setClause = implode(", ", array_map(fn($key) => "$key = ?", array_keys($data)));
$query = "UPDATE $table SET $setClause WHERE $primaryKey = ?";
$stmt = $conn->prepare($query);


$types = str_repeat("s", count($data)) . "s"; 
$params = array_values($data);
$params[] = $id;

$stmt->bind_param($types, ...$params);


if ($stmt->execute()) {
    echo json_encode(["message" => "Record updated successfully"]);
} else {
    echo json_encode(["error" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
