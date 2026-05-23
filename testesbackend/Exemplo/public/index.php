<?php
// index.php — Interface funcional para testes de Back-end
// Responsável por executar testes e exibir o resultado ao usuário

$resultado = null;
$mensagem = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../src/config.php';
    require_once '../src/UsuarioRepository.php';
    require_once '../src/UsuarioService.php';

    $nome  = $_POST['nome']  ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    try {
        $repo = new UsuarioRepository($pdo);
        $service = new UsuarioService($repo);

        // TESTE DE REGRA DE NEGÓCIO + INTEGRAÇÃO COM BANCO
        $service->cadastrar($nome, $email, $senha);

        $resultado = 'success';
        $mensagem  = 'Teste executado com sucesso. Regras de negócio e banco validados.';

    } catch (Exception $e) {
        // TESTE DE TRATAMENTO DE ERROS
        $resultado = 'danger';
        $mensagem  = 'Falha detectada: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Teste de Back-end – Capítulo 3</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="card shadow-sm">
                <div class="card-body">

                    <h3 class="card-title mb-2">Teste de Back-end</h3>
                    <p class="text-muted">
                        Capítulo 3 – Validação de regras, erros, segurança e integração com banco
                    </p>

                    <?php if ($resultado): ?>
                        <div class="alert alert-<?= $resultado ?>">
                            <?= htmlspecialchars($mensagem) ?>
                        </div>
                    <?php endif; ?>

                    <!-- TESTE FUNCIONAL -->
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" name="senha" class="form-control" required>
                            <div class="form-text">Mínimo de 6 caracteres</div>
                        </div>

                        <button class="btn btn-primary w-100">
                            Executar Teste de Back-end
                        </button>
                    </form>

                    <hr class="my-4">

                    <!-- RASTREABILIDADE DOS TESTES -->
                    <h6>Testes Cobertos</h6>
                    <ul class="small mb-0">
                        <li>Análise de requisitos funcionais</li>
                        <li>Teste de regras de negócio</li>
                        <li>Tratamento de erros</li>
                        <li>Teste unitário (service)</li>
                        <li>Teste de integração com banco de dados</li>
                        <li>Base para testes de carga e segurança</li>
                    </ul>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
