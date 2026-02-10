<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$pdo = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $grave_id = $_GET['grave_id'] ?? null;
        if ($grave_id) {
            $stmt = $pdo->prepare("
                SELECT d.*, g.tipo, g.capacidade_total 
                FROM deceased d
                JOIN graves g ON d.grave_id = g.id
                WHERE d.grave_id = ? 
                ORDER BY d.data_sepultamento DESC
            ");
            $stmt->execute([$grave_id]);
            echo json_encode($stmt->fetchAll());
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Verifica capacidade antes de inserir
        $stmt_cap = $pdo->prepare("SELECT capacidade_total, (SELECT COUNT(*) FROM deceased WHERE grave_id = ?) as atual FROM graves WHERE id = ?");
        $stmt_cap->execute([$data['grave_id'], $data['grave_id']]);
        $cap = $stmt_cap->fetch();

        if ($cap['atual'] >= $cap['capacidade_total']) {
            echo json_encode(['status' => 'error', 'message' => 'Capacidade máxima do jazigo atingida']);
            break;
        }

        $stmt = $pdo->prepare("INSERT INTO deceased (grave_id, nome, data_nascimento, data_falecimento, data_sepultamento) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['grave_id'], $data['nome'], $data['data_nascimento'], $data['data_falecimento'], $data['data_sepultamento']]);
        echo json_encode(['status' => 'success', 'id' => $pdo->lastInsertId()]);
        break;
}
?>
