<?php
// Configurações do Banco de Dados
$host = '186.209.113.107';
$db   = 'dema5738_condofacil';
$user = 'dema5738_condofacil';
$pass = 'Dema@1973';
$charset = 'utf8mb4';

// Configuração do DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Cria a instância de conexão com o banco via PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Em caso de erro, exibe a mensagem (Em produção, o ideal é gravar em log)
    die("Erro de conexão com o banco de dados: " . $e->getMessage());
}
?>