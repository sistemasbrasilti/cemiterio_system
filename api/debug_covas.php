<?php
require_once '../config/database.php';
$pdo = getDBConnection();

$sql_all = "SELECT * FROM graves";
$stmt_all = $pdo->query($sql_all);
$rows = $stmt_all->fetchAll(PDO::FETCH_ASSOC);

echo "Total Rows: " . count($rows) . "<br>";

$sum = 0;
foreach ($rows as $row) {
    echo "Row ID: " . $row['id'] . " - Cap: " . ($row['capacidade_total'] ?? 'NULL') . "<br>";
    $sum += (int)($row['capacidade_total'] ?? 0);
}

echo "Pre-computed Sum: " . $sum . "<br>";

$sql_sum = "SELECT COALESCE(SUM(capacidade_total), 0) FROM graves";
$val = $pdo->query($sql_sum)->fetchColumn();
echo "SQL Sum: " . var_export($val, true) . "<br>";
?>
