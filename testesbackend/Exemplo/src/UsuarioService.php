<?php
/**
 * src/UsuarioService.php
 * Camada de serviço – Regras de Negócio
 * Capítulo 3 – Testes Unitários, Validação e Segurança
 */

class UsuarioService
{
    private UsuarioRepository $repository;

    public function __construct(UsuarioRepository $repository)
    {
        $this->repository = $repository;
    }

    /* =============================
       CADASTRO DE USUÁRIO
       ============================= */
    public function cadastrar(string $nome, string $email, string $senha): bool
    {
        // REGRA 1 – Nome mínimo
        if (strlen(trim($nome)) < 3) {
            throw new Exception('Nome inválido');
        }

        // REGRA 2 – Email válido
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('E-mail inválido');
        }

        // REGRA 3 – Senha forte
        if (strlen($senha) < 6) {
            throw new Exception('Senha deve conter no mínimo 6 caracteres');
        }

        // REGRA 4 – Usuário duplicado
        if ($this->repository->buscarPorEmail($email)) {
            throw new Exception('Usuário já cadastrado');
        }

        // INTEGRAÇÃO COM REPOSITORY
        return $this->repository->salvar($nome, $email, $senha);
    }

    /* =============================
       VALIDAÇÃO DE LOGIN
       ============================= */
    public function autenticar(string $email, string $senha): bool
    {
        $usuario = $this->repository->buscarPorEmail($email);

        if (!$usuario) {
            throw new Exception('Usuário não encontrado');
        }

        if (!password_verify($senha, $usuario['senha'])) {
            throw new Exception('Credenciais inválidas');
        }

        return true;
    }

    /* =============================
       TESTE DE REGRA DE NEGÓCIO
       ============================= */
    public function validarRegras(): bool
    {
        // Método simples para teste unitário
        return true;
    }
}
