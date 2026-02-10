-- Criar o banco de dados
CREATE DATABASE IF NOT EXISTS cemiterio_db;
USE cemiterio_db;

-- Tabela de Utilizadores
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Cemitérios (Nome corrigido para cemeteries)
CREATE TABLE IF NOT EXISTS cemeteries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    endereco VARCHAR(255),
    cidade VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Jazigos (Nome corrigido para graves)
CREATE TABLE IF NOT EXISTS graves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cemiterio_id INT NOT NULL,
    numero VARCHAR(50) NOT NULL,
    capacidade_total INT DEFAULT 1,
    posicao_x INT DEFAULT 0,
    posicao_y INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cemiterio_id) REFERENCES cemeteries(id) ON DELETE CASCADE
);

-- Tabela de Falecidos (Nome corrigido para deceased)
CREATE TABLE IF NOT EXISTS deceased (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grave_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    data_nascimento DATE,
    data_falecimento DATE,
    data_sepultamento DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (grave_id) REFERENCES graves(id) ON DELETE CASCADE
);