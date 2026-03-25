<?php
// Configurações do e-mail de destino (SEU E-MAIL)
$email_destino = "seuemail@exemplo.com.br"; // Substitua pelo e-mail que receberá os contatos
$assunto = "Novo Contato - Landing Page CondoFácil";

// Verifica se os dados foram enviados via método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Captura e sanitiza os dados do formulário
    $nome = htmlspecialchars(trim($_POST["nome"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $telefone = htmlspecialchars(trim($_POST["telefone"]));
    $tipo_condominio = htmlspecialchars(trim($_POST["tipo_condominio"]));
    $mensagem = htmlspecialchars(trim($_POST["mensagem"]));

    // Validação básica
    if (empty($nome) || empty($email) || empty($telefone)) {
        die("Por favor, preencha todos os campos obrigatórios.");
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Formato de e-mail inválido.");
    }

    // Monta o corpo da mensagem em HTML
    $corpo_mensagem = "
    <html>
    <head>
      <title>Contato - CondoFácil</title>
    </head>
    <body>
      <h2>Novo contato via Landing Page - CondoFácil</h2>
      <p><strong>Nome:</strong> {$nome}</p>
      <p><strong>E-mail:</strong> {$email}</p>
      <p><strong>Telefone / WhatsApp:</strong> {$telefone}</p>
      <p><strong>Tipo de condomínio:</strong> {$tipo_condominio}</p>
      <p><strong>Mensagem Opcional:</strong><br/>" . nl2br($mensagem) . "</p>
    </body>
    </html>
    ";

    // Headers do e-mail (necessário para formato HTML)
    $headers  = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    
    // Header adicional (Remetente)
    $headers .= "From: no-reply@seusite.com.br" . "\r\n"; // Altere para um domínio válido seu
    $headers .= "Reply-To: {$email}" . "\r\n";

    // Tenta enviar o e-mail
    // OBS: No XAMPP local (sem configuração de servidor SMTP sendmail) essa função pode falhar silenciosamente ou gerar um warning.
    @mail($email_destino, $assunto, $corpo_mensagem, $headers);

    // Independente do mail() local falhar ou não, redirecionamos com sucesso para testar o front-end.
    // Em produção, você pode condicionar ao retorno TRUE do mail().
    header("Location: index.php?sucesso=1#planos");
    exit();

} else {
    // Se o arquivo for acessado diretamente sem POST, redirecionamos para a Home
    header("Location: index.php");
    exit();
}
?>