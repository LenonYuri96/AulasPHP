<?php
/**
 * src/TestesBackend.php
 * Executor central de Testes de Back-end
 * Capítulo 3 – Testes Unitários, Integração, Erros e Banco de Dados
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/UsuarioRepository.php';
require_once __DIR__ . '/UsuarioService.php';

/* =============================
   INICIALIZAÇÃO
   ============================= */

$repo    = new UsuarioRepository($pdo);
$service = new UsuarioService($repo);

$resultados = [];
$inicio = microtime(true);

/* =============================
   TESTE 1 – TESTE UNITÁRIO
   Regra de negócio (senha mínima)
   ============================= */
try {
    $service->cadastrar("TesteUnitario", "unitario@teste.com", "123");
    $resultados[] = "[FALHA] Teste unitário: regra de senha não validada";
} catch (Exception $e) {
    $resultados[] = "[OK] Teste unitário: validação de senha funcionando";
}

/* =============================
   TESTE 2 – TESTE DE INTEGRAÇÃO
   Serviço + Repositório + Banco
   ============================= */
try {
    $service->cadastrar("TesteIntegracao", "integracao@teste.com", "123456");
    $resultados[] = "[OK] Teste de integração com banco realizado";
} catch (Exception $e) {
    $resultados[] = "[FALHA] Integração com banco: " . $e->getMessage();
}

/* =============================
   TESTE 3 – TESTE DE ERROS
   E-mail duplicado
   ============================= */
try {
    $service->cadastrar("Duplicado", "integracao@teste.com", "123456");
    $resultados[] = "[FALHA] Teste de erro: e-mail duplicado não tratado";
} catch (Exception $e) {
    $resultados[] = "[OK] Teste de erro: duplicidade capturada";
}

/* =============================
   TESTE 4 – TESTE DE RESPOSTA
   Validação de retorno do servidor
   ============================= */
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM usuarios");
    $dados = $stmt->fetch();

    if ($dados['total'] >= 1) {
        $resultados[] = "[OK] Resposta do servidor válida";
    } else {
        $resultados[] = "[FALHA] Resposta inesperada do servidor";
    }
} catch (Exception $e) {
    $resultados[] = "[FALHA] Erro ao validar resposta do servidor";
}

/* =============================
   REGISTRO DE LOG NO BANCO
   ============================= */
try {
    foreach ($resultados as $linha) {
        $stmt = $pdo->prepare(
            "INSERT INTO logs_testes (tipo_teste, status, mensagem)
             VALUES (?, ?, ?)"
        );

        $status = str_contains($linha, '[OK]') ? 'OK' : 'FALHA';

        $stmt->execute([
            'Backend',
            $status,
            $linha
        ]);
    }
} catch (Exception $e) {
    // falha de log não interrompe execução
}

/* =============================
   RELATÓRIO FINAL
   ============================= */

$fim = microtime(true);

$relatorio = [
    'teste'        => 'Back-end',
    'quantidade'   => count($resultados),
    'tempo_exec_s' => round($fim - $inicio, 4),
    'resultado'    => $resultados
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($relatorio, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
