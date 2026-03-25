<?php
// Configuração de Banco de Dados Múltiplo - SaaS
// Usa a conexão remota conforme exigido pelo usuário

$host = '186.209.113.107';
$db   = 'dema5738_condofacil';
$user = 'dema5738_condofacil';
$pass = 'Dema@1973';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Erro Crítico de Conexão no Banco Nuvem: " . $e->getMessage());
}
?>