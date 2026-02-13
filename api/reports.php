<?php
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

// Disable caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

try {
    $pdo = getDBConnection();
    
    // Estatísticas Gerais - Garantir valores numéricos
    $total_graves = (int)$pdo->query("SELECT COUNT(*) FROM graves")->fetchColumn();
    $total_covas = (int)$pdo->query("SELECT COALESCE(SUM(capacidade_total), 0) FROM graves")->fetchColumn();
    $occupied_graves = (int)$pdo->query("SELECT COUNT(grave_id) FROM deceased")->fetchColumn();
    $free_graves = $total_covas - $occupied_graves;
    
    $stats = [
        'total_graves' => $total_graves,
        'total_covas' => $total_covas,
        'occupied_graves' => $occupied_graves,
        'exceeded_time' => 0,
        'free_graves' => $free_graves,
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
    $termo = $_GET['termo'] ?? '';
    $cemetery_id = $_GET['cemetery_id'] ?? '';
    
    $sql = "SELECT g.numero, g.cemiterio_id, c.nome as cemiterio_nome, 
                   d.nome as morto_nome, d.data_falecimento, d.data_sepultamento 
            FROM graves g
            JOIN cemeteries c ON g.cemiterio_id = c.id
            JOIN deceased d ON d.grave_id = g.id
            WHERE 1=1";
            
    $params = [];

    if ($termo) {
        $sql .= " AND d.nome LIKE :termo";
        $params['termo'] = "%$termo%";
    }

    if ($cemetery_id && $cemetery_id !== 'all') {
        $sql .= " AND g.cemiterio_id = :cemetery_id";
        $params['cemetery_id'] = $cemetery_id;
    }
            
    $sql .= " ORDER BY g.numero ASC, d.data_sepultamento DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
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

try{
    $sql = "SELECT * FROM cemeteries";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $cemeteries = $stmt->fetchAll();
    $stats['cemeteries'] = $cemeteries;
}catch(Exception $e){
    $stats['cemeteries'] = [];
}
echo json_encode($stats);
?>
