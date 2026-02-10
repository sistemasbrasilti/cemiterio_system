<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$pdo = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM cemeteries WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            echo json_encode($stmt->fetch());
        } else {
            $stmt = $pdo->query("SELECT * FROM cemeteries");
            echo json_encode($stmt->fetchAll());
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("INSERT INTO cemeteries (nome, endereco, cidade) VALUES (?, ?, ?)");
        $stmt->execute([$data['nome'], $data['endereco'], $data['cidade']]);
        echo json_encode(['status' => 'success', 'id' => $pdo->lastInsertId()]);
        break;
}
?>
