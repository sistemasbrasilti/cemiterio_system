<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$pdo = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['nome']) || !isset($data['email']) || !isset($data['senha'])) {
        echo json_encode(['status' => 'error', 'message' => 'Nome, email e senha são obrigatórios']);
        exit;
    }
    
    $nome = $data['nome'];
    $email = $data['email'];
    $senha = password_hash($data['senha'], PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(['status' => 'error', 'message' => 'Este email já está cadastrado']);
            exit;
        }
        
        $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $email, $senha]);
        
        echo json_encode(['status' => 'success', 'message' => 'Usuário cadastrado com sucesso']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar usuário']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
}
?>
