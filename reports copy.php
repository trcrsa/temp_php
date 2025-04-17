<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once 'db.php';

$queryId = $_GET['query'] ?? null;

if (!$queryId) {
    echo json_encode(["error" => "No query ID provided"]);
    exit;
}

switch ($queryId) {
    case "1":
        $sql = "SELECT Name, Ticker FROM Company;";
        break;
    case "2":
        $sql = "SELECT Name FROM Company WHERE Sector = 'Technology';";
        break;
    case "3":
        $sql = "SELECT Stock_ticker, Eod_price FROM Stock WHERE Stock_ticker = 'AAPL';";
        break;
    case "4":
        $sql = "
            SELECT 
                Company.Name, 
                Stock.IPO_date
            FROM 
                Stock
            JOIN 
                Company ON Stock.Stock_ticker = Company.Ticker
            WHERE 
                Stock.IPO_date > '2010-01-01'
            ORDER BY 
                Stock.IPO_date;
        ";
        break;
    case "5":
        $sql = "
            SELECT Total_revenue, Net_income_common_stockholders 
            FROM Income_Statement 
            WHERE Stock_ticker = (SELECT Stock_ticker FROM Company WHERE Ticker = 'AAPL');
        ";
        break;
    case "6":
        $sql = "
            SELECT 
                Company.Name, 
                Balance_Sheet.Total_debt,
                Balance_Sheet.Fiscal_year_end
            FROM 
                Company 
            JOIN 
                Stock ON Company.Ticker = Stock.Stock_ticker
            JOIN 
                Balance_Sheet ON Stock.Stock_ticker = Balance_Sheet.Stock_ticker
            WHERE 
                Company.Sector = 'Consumer Cyclical'
            ORDER BY 
                Balance_Sheet.Fiscal_year_end ASC;
        ";
        break;
    case "7":
        $sql = "
            SELECT Company.Name, Income_Statement.Gross_profit, Income_Statement.Fiscal_year_end
            FROM Company
            JOIN Income_Statement ON Company.Ticker = Income_Statement.Stock_Ticker
            WHERE Income_Statement.Gross_profit > 50000000
            ORDER BY Income_Statement.Fiscal_year_end ASC;
        ";
        break;
    case "8":
        $sql = "
            SELECT Company.Name, Balance_Sheet.Fiscal_year_end
            FROM Company
            JOIN Balance_Sheet ON Company.Ticker = Balance_Sheet.Stock_ticker
            WHERE Balance_Sheet.Total_assets > 1000000000
            ORDER BY Balance_Sheet.Fiscal_year_end ASC;
        ";
        break;
    case "9":
        $sql = "
            SELECT Company.Name, Income_Statement.Fiscal_year_end
            FROM Company
            JOIN Income_Statement ON Company.Ticker = Income_Statement.Stock_ticker
            WHERE Company.Name = 'Microsoft Corporation'
            ORDER BY Income_Statement.Fiscal_year_end;
        ";
        break;
    case "10":
        $sql = "
            SELECT Company.Name, Balance_Sheet.Working_capital
            FROM Company 
            JOIN Balance_Sheet ON Balance_Sheet.Stock_Ticker = Company.Ticker
            WHERE Balance_Sheet.Working_capital > 50000000
            ORDER BY Balance_Sheet.Working_capital;
        ";
        break;
    case "11":
        $sql = "SELECT Name, Industry FROM Company WHERE Industry = 'Software Infrastructure';";
        break;
    case "12":
        $sql = "
            SELECT Company.Name, Income_Statement.Total_revenue, Income_Statement.Cost_of_revenue 
            FROM Company
            JOIN Income_Statement ON Income_Statement.Stock_Ticker = Company.Ticker 
            WHERE Company.Ticker = (SELECT Ticker FROM Company WHERE Ticker = 'TSLA');
        ";
        break;
    case "13":
        $sql = "
            SELECT Company.Name, Income_Statement.Net_income_common_stockholders
            FROM Company
            JOIN Income_Statement ON Income_Statement.Stock_Ticker = Company.Ticker 
            WHERE Income_Statement.Net_income_common_stockholders > 10000000;
        ";
        break;
    case "14":
        $sql = "
            SELECT Company.Name, Cash_Flow.Operating_cashflow
            FROM Company
            JOIN Cash_Flow ON Cash_Flow.Stock_Ticker = Company.Ticker
            WHERE Company.Ticker = (SELECT Ticker FROM Company WHERE Ticker = 'NVDA');
        ";
        break;
    case "15":
        $sql = "
            SELECT Company.Ticker, Earnings.Reported_eps, Earnings.Fiscal_date_end
            FROM Company
            JOIN Earnings ON Earnings.Stock_ticker = Company.Ticker
            WHERE Earnings.Fiscal_date_end = '2022-12-31';
        ";
        break;
    case "16":
        $sql = "
            SELECT Company.Name, Cash_Flow.Capital_expenditures
            FROM Company
            JOIN Cash_Flow ON Cash_Flow.Stock_Ticker = Company.Ticker
            WHERE Cash_Flow.Capital_expenditures > -2000000;
        ";
        break;
    case "17":
        $sql = "
            SELECT Company.Name, Cash_Flow.Cash_dividends_paid, Cash_Flow.Fiscal_year_end
            FROM Company
            JOIN Cash_Flow ON Cash_Flow.Stock_Ticker = Company.Ticker
            WHERE Cash_Flow.Cash_dividends_paid IS NOT NULL
            ORDER BY Cash_Flow.Fiscal_year_end;
        ";
        break;
    case "18":
        $sql = "SELECT Stock_Ticker, Free_cash_flow FROM Cash_Flow WHERE Free_cash_flow > 50000000;";
        break;
    case "19":
        $sql = "
            SELECT Fiscal_year_end, Operating_cashflow
            FROM Cash_Flow
            WHERE Operating_cashflow < 10000000;
        ";
        break;
    case "20":
        $sql = "
            SELECT Stock_Ticker, Capital_expenditures, Fiscal_year_end
            FROM Cash_Flow
            WHERE Capital_expenditures < 0;
        ";
        break;
    default:
        echo json_encode(["error" => "Invalid query ID"]);
        exit;
}

$result = $conn->query($sql);

if ($result) {
    $data = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(["data" => $data]);
} else {
    echo json_encode(["error" => "Query failed: " . $conn->error]);
}

$conn->close();
?>
