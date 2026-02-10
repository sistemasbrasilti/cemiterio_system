<?php
require_once '../config/database.php';
header('Content-Type: application/json');

try {
    $pdo = getDBConnection();
    
    // Estatísticas Gerais - Garantir valores numéricos
    $total_graves = $pdo->query("SELECT COUNT(*) FROM graves")->fetchColumn();
    $total_covas = $pdo->query("SELECT COALESCE(SUM(capacidade_total), 0) FROM graves")->fetchColumn();
    $occupied_graves = $pdo->query("SELECT COUNT(grave_id) FROM deceased")->fetchColumn();
    $free_graves = $pdo->query("SELECT COUNT(*) FROM graves WHERE id NOT IN (SELECT grave_id FROM deceased)")->fetchColumn();
    
    $stats = [
        'total_graves' => (int)($total_graves ?? 0),
        'total_covas' => (int)($total_covas ?? 0),
        'occupied_graves' => (int)($occupied_graves ?? 0),
        'exceeded_time' => 0,
        'free_graves' => (int)($free_graves ?? 0),
        'details' => []
    ];
} catch (Exception $e) {
    // Retornar resposta com valores padrão se houver erro
    http_response_code(200);
    echo json_encode([
        'total_graves' => 0,
        'total_covas' => 0,
        'occupied_graves' => 0,
        'exceeded_time' => 0,
        'free_graves' => 0,
        'details' => []
    ]);
    exit;
}

// Detalhes por cova
try {
    $sql = "SELECT g.numero, g.cemiterio_id, c.nome as cemiterio_nome, 
                   d.nome as morto_nome, d.data_falecimento, d.data_sepultamento 
            FROM graves g
            JOIN cemeteries c ON g.cemiterio_id = c.id
            LEFT JOIN deceased d ON d.grave_id = g.id
            ORDER BY g.numero ASC, d.data_sepultamento DESC";
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll();

    foreach ($rows as $row) {
        if ($row['morto_nome']) {
            $data_sep = new DateTime($row['data_sepultamento']);
            $hoje = new DateTime();
            $diff = $hoje->diff($data_sep);
            
            $anos = $diff->y;
            $falta_atingir = 5 - $anos;
            
            if ($anos >= 5) {
                $stats['exceeded_time']++;
                $row['status_tempo'] = "Excedido";
                $row['tempo_restante'] = "0 anos";
            } else {
                $row['status_tempo'] = "No prazo";
                $row['tempo_restante'] = $falta_atingir . " anos";
            }
        } else {
            $row['status_tempo'] = "Livre";
            $row['tempo_restante'] = "N/A";
        }
        $stats['details'][] = $row;
    }
} catch (Exception $e) {
    // Se houver erro, details fica vazio
    $stats['details'] = [];
}

echo json_encode($stats);
?>
