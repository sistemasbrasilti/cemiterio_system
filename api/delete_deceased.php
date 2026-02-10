<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;

    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM deceased WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID não fornecido']);
    }
}
?>