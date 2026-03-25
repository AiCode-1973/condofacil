<?php
session_start();
require_once 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $_SESSION['erro_login'] = 'Preencha todos os campos.';
        header("Location: login.php");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, nome, email, senha, tipo_acesso, condominio_id, status FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        // Verifica a Hash (ou bypass se você inseriu pura via BD no início, embora tenhamos usado password_hash no instaler)
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            
            if ($usuario['status'] !== 'ativo') {
                $_SESSION['erro_login'] = 'Sua conta está inativa. Contate a administração.';
                header("Location: login.php");
                exit;
            }

            // Armazena as variáveis de sessão cruciais do SaaS
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['tipo_acesso'] = $usuario['tipo_acesso'];
            $_SESSION['condominio_id'] = $usuario['condominio_id'];

            // Redirecionamento Baseado no Nível de Acesso
            switch ($usuario['tipo_acesso']) {
                case 'superadmin':
                    header("Location: superadmin/index.php");
                    break;
                case 'admin':
                case 'sindico':
                    header("Location: admin/index.php");
                    break;
                case 'morador':
                    header("Location: morador/index.php");
                    break;
                default:
                    $_SESSION['erro_login'] = 'Tipo de acesso não configurado.';
                    header("Location: login.php");
                    break;
            }
            exit;

        } else {
            $_SESSION['erro_login'] = 'Credenciais incorretas (E-mail ou Senha).';
            header("Location: login.php");
            exit;
        }

    } catch (Exception $e) {
        $_SESSION['erro_login'] = 'Erro ao autenticar. Tente novamente mais tarde.';
        header("Location: login.php");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>