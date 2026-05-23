<?php
/**
 * public/teste_carga.php
 * Teste de Carga e Estresse do Back-end
 * Capítulo 3 – Teste de Back-end
 */

require_once '../src/config.php';
require_once '../src/UsuarioRepository.php';
require_once '../src/UsuarioService.php';

set_time_limit(0);

$requisicoes = 100; // ajuste conforme o cenário
$sucessos = 0;
$falhas = 0;

$inicio = microtime(true);

$repo = new UsuarioRepository($pdo);
$service = new UsuarioService($repo);

for ($i = 1; $i <= $requisicoes; $i++) {
    try {
        $service->cadastrar(
            "Carga_$i",
            "carga_$i@teste.com",
            "123456"
        );
        $sucessos++;
    } catch (Exception $e) {
        $falhas++;
    }
}

$fim = microtime(true);
$tempoTotal = round($fim - $inicio, 4);

$resultado = [
    'tipo_teste'        => 'Carga / Estresse',
    'total_requisicoes' => $requisicoes,
    'sucessos'          => $sucessos,
    'falhas'            => $falhas,
    'tempo_total_seg'   => $tempoTotal,
    'media_req_por_seg' => $tempoTotal > 0 ? round($requisicoes / $tempoTotal, 2) : 0
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
