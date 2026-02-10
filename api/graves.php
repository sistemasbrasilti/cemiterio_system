<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$pdo = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $cemetery_id = $_GET['cemetery_id'] ?? null;
        if ($cemetery_id) {
            // Busca jazigos e o sepultamento mais recente para cada um
            // No arquivo api/graves.php
                $sql = "SELECT g.*, 
                        (SELECT MAX(data_sepultamento) FROM deceased WHERE grave_id = g.id AND nome IS NOT NULL AND nome != '') as ultimo_sepultamento,
                        (SELECT COUNT(*) FROM deceased WHERE grave_id = g.id AND nome IS NOT NULL AND nome != '') as total_corpos
                        FROM graves g 
                        WHERE g.cemiterio_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$cemetery_id]);
            $graves = $stmt->fetchAll();

            foreach ($graves as &$grave) {
                if ($grave['total_corpos'] == 0) {
                    $grave['status'] = 'verde'; // Livre
                } else {
                    $data_sep = new DateTime($grave['ultimo_sepultamento']);
                    $hoje = new DateTime();
                    $intervalo = $hoje->diff($data_sep);
                    
                    if ($intervalo->y >= 5) {
                        $grave['status'] = 'vermelho'; // Ocupado > 5 anos
                    } else {
                        $grave['status'] = 'amarelo'; // Ocupado < 5 anos
                    }
                }
            }
            echo json_encode($graves);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("INSERT INTO graves (cemiterio_id, numero, Tipo, capacidade_total) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['cemiterio_id'], $data['numero'], $data['tipo'], $data['capacidade_total']]);
        echo json_encode(['status' => 'success', 'id' => $pdo->lastInsertId()]);
        break;
}
?>
