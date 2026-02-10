<?php
require_once '../config/database.php';
header('Content-Type: application/json');
session_start();

$pdo = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['email']) || !isset($data['senha'])) {
        echo json_encode(['status' => 'error', 'message' => 'Email e senha são obrigatórios']);
        exit;
    }
    
    $email = $data['email'];
    $senha = $data['senha'];
    
    try {
        $stmt = $pdo->prepare("SELECT id, nome, email, senha FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($senha, $user['senha'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_nome'] = $user['nome'];
            
            echo json_encode(['status' => 'success', 'message' => 'Login realizado com sucesso']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Email ou senha incorretos']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao conectar ao banco de dados']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
}
?>