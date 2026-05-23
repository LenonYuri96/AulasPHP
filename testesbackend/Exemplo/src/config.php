<?php
/**
 * src/config.php
 * Configuração central do Back-end
 * Capítulo 3 – Requisitos Não Funcionais, Segurança e Integração com BD
 */

/* =============================
   CONFIGURAÇÕES BÁSICAS
   ============================= */

$host     = 'localhost';
$dbname   = 'backend_testes';
$user     = 'root';
$password = '';
$charset  = 'utf8mb4';

/* =============================
   CONFIGURAÇÃO DE ERROS
   ============================= */

ini_set('display_errors', 0);          // não expor erro em produção
ini_set('log_errors', 1);
error_reporting(E_ALL);

/* =============================
   TIMEZONE (PADRÃO CORPORATIVO)
   ============================= */

date_default_timezone_set('America/Sao_Paulo');

/* =============================
   CONEXÃO PDO (MYSQL)
   ============================= */

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // falha explícita
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // padrão mercado
    PDO::ATTR_EMULATE_PREPARES   => false,                  // segurança
    PDO::ATTR_PERSISTENT         => false
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (PDOException $e) {
    // falha controlada e rastreável
    http_response_code(500);
    echo json_encode([
        'status'  => 'erro',
        'mensagem'=> 'Falha na conexão com o banco de dados'
    ]);
    exit;
}

/* =============================
   CABEÇALHOS DE SEGURANÇA
   ============================= */

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

/* =============================
   FUNÇÃO AUXILIAR (LOG)
   ============================= */

function registrarLog($mensagem) {
    $linha = '[' . date('Y-m-d H:i:s') . '] ' . $mensagem . PHP_EOL;
    file_put_contents(__DIR__ . '/../logs/app.log', $linha, FILE_APPEND);
}
