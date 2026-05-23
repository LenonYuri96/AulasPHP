<?php
/**
 * src/UsuarioRepository.php
 * Camada de acesso a dados (Repository)
 * Capítulo 3 – Integração com Banco de Dados e Testes de Back-end
 */

class UsuarioRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /* =============================
       CREATE – Inserção
       ============================= */
    public function salvar(string $nome, string $email, string $senha): bool
    {
        $sql = "
            INSERT INTO usuarios (nome, email, senha)
            VALUES (:nome, :email, :senha)
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':nome'  => $nome,
            ':email' => $email,
            ':senha' => password_hash($senha, PASSWORD_DEFAULT)
        ]);
    }

    /* =============================
       READ – Buscar por e-mail
       ============================= */
    public function buscarPorEmail(string $email): ?array
    {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);

        $usuario = $stmt->fetch();
        return $usuario ?: null;
    }

    /* =============================
       READ – Listar usuários
       ============================= */
    public function listarTodos(): array
    {
        $sql = "SELECT id, nome, email, criado_em FROM usuarios ORDER BY id DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    /* =============================
       DELETE – Remoção controlada
       ============================= */
    public function removerPorEmail(string $email): bool
    {
        $sql = "DELETE FROM usuarios WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':email' => $email]);
    }

    /* =============================
       TESTE DE INTEGRAÇÃO
       ============================= */
    public function testarConexao(): bool
    {
        try {
            $this->pdo->query("SELECT 1");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
