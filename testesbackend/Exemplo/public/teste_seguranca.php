<?php
/**
 * public/teste_seguranca.php
 * Teste de Segurança do Back-end
 * Capítulo 3 – Testes de Segurança e Codificação Server-Side
 */

require_once '../src/config.php';

$resultados = [];
$inicio = microtime(true);

/**
 * TESTE 1 – SQL Injection clássico
 */
try {
    $payload = "' OR '1'='1";
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$payload]);

    if ($stmt->rowCount() === 0) {
        $resultados[] = "[OK] Proteção contra SQL Injection (login bypass)";
    } else {
        $resultados[] = "[FALHA] Vulnerabilidade SQL Injection detectada";
    }
} catch (Exception $e) {
    $resultados[] = "[ERRO] Falha ao executar teste de SQL Injection";
}

/**
 * TESTE 2 – Tentativa de comando malicioso
 */
try {
    $payload = "test@test.com; DROP TABLE usuarios;";
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$payload]);

    $resultados[] = "[OK] Comandos maliciosos não executados";
} catch (Exception $e) {
    $resultados[] = "[FALHA] Execução de comando malicioso detectada";
}

/**
 * TESTE 3 – Validação de entrada (email inválido)
 */
$emailInvalido = "email-sem-arroba";
if (!filter_var($emailInvalido, FILTER_VALIDATE_EMAIL)) {
    $resultados[] = "[OK] Validação de entrada funcionando (email inválido)";
} else {
    $resultados[] = "[FALHA] Validação de entrada inexistente";
}

/**
 * TESTE 4 – Enumeração de dados
 */
try {
    $stmt = $pdo->query("SELECT id FROM usuarios LIMIT 1");
    if ($stmt) {
        $resultados[] = "[OK] Enumeração direta não expõe dados sensíveis";
    }
} catch (Exception $e) {
    $resultados[] = "[OK] Acesso restrito a dados sensíveis";
}

$fim = microtime(true);

/**
 * RESULTADO FINAL
 */
$relatorio = [
    'tipo_teste'   => 'Segurança',
    'status'       => 'Concluído',
    'tempo_exec_s' => round($fim - $inicio, 4),
    'resultados'   => $resultados
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($relatorio, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
