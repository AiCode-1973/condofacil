<?php
require_once 'config/database.php';

try {
    // Definir as queries da documentação de requisitos para construir o banco
    
    // Tabela 1: condominios
    $pdo->exec("CREATE TABLE IF NOT EXISTS condominios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        endereco TEXT,
        cnpj VARCHAR(20),
        logo VARCHAR(255),
        status VARCHAR(50) DEFAULT 'ativo',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Tabela 2: usuarios
    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        senha VARCHAR(255) NOT NULL,
        tipo_acesso VARCHAR(50) NOT NULL COMMENT 'superadmin, admin, morador',
        condominio_id INT NULL,
        unidade VARCHAR(50),
        bloco VARCHAR(50),
        status VARCHAR(50) DEFAULT 'ativo',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (condominio_id) REFERENCES condominios(id) ON DELETE CASCADE
    )");

    // Tabela 3: areas_comuns
    $pdo->exec("CREATE TABLE IF NOT EXISTS areas_comuns (
        id INT AUTO_INCREMENT PRIMARY KEY,
        condominio_id INT NOT NULL,
        nome VARCHAR(255) NOT NULL,
        descricao TEXT,
        capacidade INT,
        horario_inicio TIME,
        horario_fim TIME,
        dias_disponiveis VARCHAR(255),
        requer_aprovacao BOOLEAN DEFAULT FALSE,
        status VARCHAR(50) DEFAULT 'ativo',
        FOREIGN KEY (condominio_id) REFERENCES condominios(id) ON DELETE CASCADE
    )");

    // Tabela 4: reservas
    $pdo->exec("CREATE TABLE IF NOT EXISTS reservas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        area_id INT NOT NULL,
        usuario_id INT NOT NULL,
        condominio_id INT NOT NULL,
        data_reserva DATE,
        horario_inicio TIME,
        horario_fim TIME,
        status VARCHAR(50) DEFAULT 'pendente',
        observacao TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (area_id) REFERENCES areas_comuns(id) ON DELETE CASCADE,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
        FOREIGN KEY (condominio_id) REFERENCES condominios(id) ON DELETE CASCADE
    )");

    // Tabela 5: avisos
    $pdo->exec("CREATE TABLE IF NOT EXISTS avisos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        condominio_id INT NOT NULL,
        titulo VARCHAR(255) NOT NULL,
        conteudo TEXT,
        autor_id INT NOT NULL,
        fixado BOOLEAN DEFAULT FALSE,
        status VARCHAR(50) DEFAULT 'ativo',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (condominio_id) REFERENCES condominios(id) ON DELETE CASCADE,
        FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE CASCADE
    )");

    // Tabela 6: ocorrencias
    $pdo->exec("CREATE TABLE IF NOT EXISTS ocorrencias (
        id INT AUTO_INCREMENT PRIMARY KEY,
        condominio_id INT NOT NULL,
        usuario_id INT NOT NULL,
        titulo VARCHAR(255) NOT NULL,
        descricao TEXT,
        categoria VARCHAR(100),
        status VARCHAR(50) DEFAULT 'aberto',
        resposta TEXT,
        respondido_por INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (condominio_id) REFERENCES condominios(id) ON DELETE CASCADE,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
        FOREIGN KEY (respondido_por) REFERENCES usuarios(id) ON DELETE SET NULL
    )");

    // Tabela 7: regras
    $pdo->exec("CREATE TABLE IF NOT EXISTS regras (
        id INT AUTO_INCREMENT PRIMARY KEY,
        condominio_id INT NOT NULL,
        titulo VARCHAR(255) NOT NULL,
        conteudo TEXT,
        categoria VARCHAR(100),
        ordem INT DEFAULT 0,
        status VARCHAR(50) DEFAULT 'ativo',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (condominio_id) REFERENCES condominios(id) ON DELETE CASCADE
    )");

    // Tabela 8: mensagens_chat
    $pdo->exec("CREATE TABLE IF NOT EXISTS mensagens_chat (
        id INT AUTO_INCREMENT PRIMARY KEY,
        condominio_id INT NOT NULL,
        remetente_id INT NOT NULL,
        destinatario_id INT NOT NULL,
        mensagem TEXT,
        lida BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (condominio_id) REFERENCES condominios(id) ON DELETE CASCADE,
        FOREIGN KEY (remetente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
        FOREIGN KEY (destinatario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    )");

    // Tabela 9: notificacoes
    $pdo->exec("CREATE TABLE IF NOT EXISTS notificacoes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        titulo VARCHAR(255) NOT NULL,
        mensagem TEXT,
        tipo VARCHAR(100),
        lida BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    )");

    // Criar um usuário default Super Admin para testes no SaaS (se não existir)
    $email_super = 'admin@condofacil.com';
    $senha_hash = password_hash('123456', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email_super]);
    
    if($stmt->rowCount() == 0){
        $stmt_insert = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo_acesso) VALUES (?, ?, ?, ?)");
        $stmt_insert->execute(['Super Administrador', $email_super, $senha_hash, 'superadmin']);
        $msg_admin = ". Criado usuário superadmin: admin@condofacil.com / Senha: 123456";
    }

    echo "<h1>Sucesso!</h1><p>Todas as 9 tabelas do SaaS (Banco em nuvem) foram criadas com êxito! {$msg_admin}</p>";
    echo "<br><a href='login.php'>Ir para o Login</a>";

} catch (\PDOException $e) {
    echo "<h1>Erro na criação das tabelas MySQL:</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>