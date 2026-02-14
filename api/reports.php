<?php
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

// Disable caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

try {
    $pdo = getDBConnection();
    
    $termo = $_GET['termo'] ?? '';
    // $cemetery_id validation logic
    $cemetery_id = $_GET['cemetery_id'] ?? '';
    if ($cemetery_id === 'undefined' || $cemetery_id === 'null') {
        $cemetery_id = '';
    }

    // Preparar WHERE para estatísticas
    $whereStats = "";
    $paramsStats = [];
    
    // Preparar WHERE para detalhes (pode ter termo de busca também)
    $whereDetails = "WHERE 1=1";
    $paramsDetails = [];

    if ($cemetery_id && $cemetery_id !== 'all') {
        // Filtro para estatísticas
        $whereStats = "WHERE cemiterio_id = :id";
        $paramsStats = [':id' => $cemetery_id];
        
        // Filtro para detalhes
        $whereDetails .= " AND g.cemiterio_id = :id";
        $paramsDetails[':id'] = $cemetery_id;
    }

    if ($termo) {
        $whereDetails .= " AND d.nome LIKE :termo";
        $paramsDetails[':termo'] = "%$termo%";
    }

    // 1. Calcular Estatísticas
    // Total de Jazigos (Graves)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM graves $whereStats");
    $stmt->execute($paramsStats);
    $total_graves = (int)$stmt->fetchColumn();

    // Capacidade Total (Covas)
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(capacidade_total), 0) FROM graves $whereStats");
    $stmt->execute($paramsStats);
    $total_covas = (int)$stmt->fetchColumn();

    // Ocupados (Deceased)
    // Precisamos fazer JOIN se estiver filtrando por cemitério, pois deceased não tem cemiterio_id direto, apenas via grave_id
    if ($cemetery_id && $cemetery_id !== 'all') {
        $sqlOccupied = "SELECT COUNT(d.id) FROM deceased d JOIN graves g ON d.grave_id = g.id WHERE g.cemiterio_id = :id";
        $stmt = $pdo->prepare($sqlOccupied);
        $stmt->execute([':id' => $cemetery_id]);
    } else {
        $sqlOccupied = "SELECT COUNT(*) FROM deceased";
        $stmt = $pdo->query($sqlOccupied);
    }
    $occupied_graves = (int)$stmt->fetchColumn();

    $free_graves = $total_covas - $occupied_graves;
    
    // Inicializa array de resposta
    $stats = [
        'total_graves' => $total_graves,
        'total_covas' => $total_covas,
        'occupied_graves' => $occupied_graves,
        'exceeded_time' => 0, // Será calculado no loop abaixo
        'free_graves' => $free_graves,
        'details' => []
    ];

    // 2. Buscar Detalhes
    $sql = "SELECT g.numero, g.cemiterio_id, c.nome as cemiterio_nome, 
                   d.nome as morto_nome, d.data_falecimento, d.data_sepultamento 
            FROM graves g
            JOIN cemeteries c ON g.cemiterio_id = c.id
            JOIN deceased d ON d.grave_id = g.id
            $whereDetails
            ORDER BY g.numero ASC, d.data_sepultamento DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute($paramsDetails);
    $rows = $stmt->fetchAll();

    foreach ($rows as $row) {
        if ($row['morto_nome']) {
            $data_sep = new DateTime($row['data_sepultamento']);
            $hoje = new DateTime();
            $diff = $hoje->diff($data_sep);
            
            $anos = $diff->y;
            $falta_atingir = 5 - $anos;
            
            if ($anos >= 5) {
                // Aqui incrementamos o contador de excedidos APENAS para os registros que aparecem no filtro
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
    // Retornar resposta com valores zerados se houver erro
    $stats = [
        'total_graves' => 0,
        'total_covas' => 0,
        'occupied_graves' => 0,
        'exceeded_time' => 0,
        'free_graves' => 0,
        'details' => [],
        'error' => $e->getMessage()
    ];
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
