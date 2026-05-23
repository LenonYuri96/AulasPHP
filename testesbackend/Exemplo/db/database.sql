/* =========================================================
   db/database.sql
   Banco de Dados – Testes de Back-end
   Capítulo 3 – Requisitos, Integração, Segurança e Performance
   ========================================================= */

/* ---------- CRIAÇÃO DO BANCO ---------- */
CREATE DATABASE IF NOT EXISTS backend_testes
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE backend_testes;

/* ---------- TABELA DE USUÁRIOS ---------- */
/* Suporte a testes funcionais, segurança e regras de negócio */
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

/* ---------- TABELA DE LOG DE TESTES ---------- */
/* Rastreabilidade e registro padronizado de falhas */
CREATE TABLE IF NOT EXISTS logs_testes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_teste VARCHAR(50) NOT NULL,
    status VARCHAR(20) NOT NULL,
    mensagem TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

/* ---------- ÍNDICES PARA PERFORMANCE ---------- */
/* Suporte a testes de carga e estresse */
CREATE INDEX idx_email_usuario ON usuarios (email);
CREATE INDEX idx_tipo_teste ON logs_testes (tipo_teste);

/* ---------- DADOS DE APOIO (SEED) ---------- */
/* Facilita testes de integração e segurança */
INSERT INTO usuarios (nome, email, senha) VALUES
('Admin Teste', 'admin@teste.com', '$2y$10$abcdefghijklmnopqrstuv'),
('Usuario Demo', 'demo@teste.com', '$2y$10$abcdefghijklmnopqrstuv');

/* ---------- VIEW PARA TESTES DE LEITURA ---------- */
/* Suporte a testes de integração e desempenho */
CREATE OR REPLACE VIEW vw_usuarios_basico AS
SELECT id, nome, email, criado_em
FROM usuarios;

/* ---------- PROCEDURE DE TESTE DE CARGA ---------- */
/* Simula leitura repetitiva no banco */
DELIMITER $$

CREATE PROCEDURE sp_teste_carga()
BEGIN
    DECLARE i INT DEFAULT 0;
    WHILE i < 100 DO
        SELECT id FROM usuarios;
        SET i = i + 1;
    END WHILE;
END$$

DELIMITER ;

/* ---------- CONSTRAINTS DE SEGURANÇA ---------- */
/* Evita dados inválidos */
ALTER TABLE usuarios
ADD CONSTRAINT chk_nome CHECK (CHAR_LENGTH(nome) >= 3),
ADD CONSTRAINT chk_email CHECK (email LIKE '%@%');

/* ---------- FINAL ---------- */
/* Banco pronto para:
   ✔ Testes de integração
   ✔ Testes de carga
   ✔ Testes de segurança
   ✔ Validação de regras
   ✔ Rastreabilidade
*/
