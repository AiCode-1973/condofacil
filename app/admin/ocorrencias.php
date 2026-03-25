<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_acesso'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$condominioId = $_SESSION['condominio_id'];
$mensagemSucesso = '';

// Atualizar Status da Ocorrência
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'atualizar_status') {
    $ocorrenciaId = $_POST['ocorrencia_id'];
    $novoStatus = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE ocorrencias SET status = ? WHERE id = ? AND condominio_id = ?");
    if ($stmt->execute([$novoStatus, $ocorrenciaId, $condominioId])) {
        $mensagemSucesso = "Status da ocorrência atualizado com sucesso!";
    }
}

// Buscar todas as ocorrências (JOIN com usuários)
// Ajuste de segurança: caso a coluna de data chame data_criacao invés de data_registro, usaremos o padrão
$sql = "
    SELECT o.*, u.nome as morador_nome, u.unidade 
    FROM ocorrencias o 
    JOIN usuarios u ON o.usuario_id = u.id 
    WHERE o.condominio_id = ? 
    ORDER BY o.id DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$condominioId]);
$ocorrencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

function badgeStatus($status) {
    switch($status) {
        case 'aberto': return '<span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full"><i class="fa-solid fa-circle-exclamation mr-1"></i> Aberto</span>';
        case 'em_andamento': return '<span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full"><i class="fa-solid fa-spinner mr-1"></i> Em Análise</span>';
        case 'resolvido': return '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full"><i class="fa-solid fa-check mr-1"></i> Resolvido</span>';
        default: return '<span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-full">'.$status.'</span>';
    }
}

function iconeTipo($tipo) {
    switch(strtolower($tipo)) {
        case 'reclamação': return '<i class="fa-solid fa-bullhorn text-red-500"></i>';
        case 'sugestão': return '<i class="fa-regular fa-lightbulb text-yellow-500"></i>';
        case 'manutenção': return '<i class="fa-solid fa-wrench text-blue-500"></i>';
        case 'barulho': return '<i class="fa-solid fa-volume-high text-orange-500"></i>';
        default: return '<i class="fa-regular fa-message text-gray-500"></i>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Ocorrências - CondoFácil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex h-screen font-sans">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-800 text-white flex flex-col shadow-lg">
        <div class="p-5 border-b border-slate-700 font-bold flex flex-col items-center justify-center gap-1">
            <i class="fa-solid fa-hotel text-3xl text-green-400"></i>
            <span class="text-sm uppercase tracking-wide text-slate-300">Administração</span>
        </div>
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto custom-scrollbar">
            <a href="index.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-slate-700' : 'hover:bg-slate-700' ?> rounded-lg transition">
                <i class="fa-solid fa-house w-5 text-center"></i> Início
            </a>
            <a href="moradores.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'moradores.php' ? 'bg-slate-700' : 'hover:bg-slate-700' ?> rounded-lg transition">
                <i class="fa-solid fa-users w-5 text-center"></i> Moradores
            </a>
            <a href="avisos.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'avisos.php' ? 'bg-slate-700' : 'hover:bg-slate-700' ?> rounded-lg transition">
                <i class="fa-solid fa-bullhorn w-5 text-center"></i> Avisos
            </a>
            <a href="areas_comuns.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'areas_comuns.php' ? 'bg-slate-700' : 'hover:bg-slate-700' ?> rounded-lg transition">
                <i class="fa-solid fa-swimming-pool w-5 text-center"></i> Áreas Comuns
            </a>
            <a href="reservas.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'reservas.php' ? 'bg-slate-700' : 'hover:bg-slate-700' ?> rounded-lg transition">
                <i class="fa-solid fa-calendar-check w-5 text-center"></i> Reservas Adm
            </a>
            <a href="ocorrencias.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'ocorrencias.php' ? 'bg-slate-700' : 'hover:bg-slate-700' ?> rounded-lg transition">
                <i class="fa-solid fa-clipboard-list w-5 text-center"></i> Ocorrências
            </a>
            <a href="regras.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'regras.php' ? 'bg-slate-700' : 'hover:bg-slate-700' ?> rounded-lg transition">
                <i class="fa-solid fa-scale-balanced w-5 text-center"></i> Regras
            </a>
            <a href="chat.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'chat.php' ? 'bg-slate-700' : 'hover:bg-slate-700' ?> rounded-lg transition">
                <i class="fa-solid fa-comments w-5 text-center"></i> Chat / Mensagens
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">
        <header class="bg-white shadow-sm px-8 py-5 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Chamados e Ocorrências</h1>
            <span class="bg-red-500 text-white text-xs px-3 py-1 rounded-full font-bold shadow-sm">
                <?php echo count(array_filter($ocorrencias, fn($o) => $o['status'] === 'aberto')); ?> Pendentes
            </span>
        </header>

        <div class="p-8 max-w-6xl mx-auto">
            <?php if ($mensagemSucesso): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <i class="fa-solid fa-check"></i> <?php echo $mensagemSucesso; ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-800"><i class="fa-solid fa-list-check text-blue-600 mr-2"></i> Lista de Chamados</h2>
                </div>
                
                <div class="p-0">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Morador</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Assunto</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Atualizar</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (count($ocorrencias) > 0): ?>
                                <?php foreach ($ocorrencias as $oco): ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900"><?php echo htmlspecialchars($oco['morador_nome']); ?></div>
                                            <div class="text-xs text-gray-500">Apto: <?php echo htmlspecialchars($oco['unidade']); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                                <span title="Tipo"><?php echo iconeTipo($oco['tipo']); ?></span>
                                                <?php echo htmlspecialchars($oco['tipo']); ?>
                                            </div>
                                            <div class="text-sm text-gray-600 mt-1 max-w-md break-words truncate">
                                                <?php echo htmlspecialchars($oco['descricao']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <?php echo badgeStatus($oco['status']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <form method="POST" class="flex justify-center gap-2">
                                                <input type="hidden" name="acao" value="atualizar_status">
                                                <input type="hidden" name="ocorrencia_id" value="<?php echo $oco['id']; ?>">
                                                
                                                <select name="status" class="border border-gray-300 rounded text-sm px-2 py-1 bg-white" onchange="this.form.submit()">
                                                    <option value="" disabled selected>Alterar...</option>
                                                    <option value="aberto" <?php if($oco['status'] == 'aberto') echo 'hidden'; ?>>Pendente</option>
                                                    <option value="em_andamento" <?php if($oco['status'] == 'em_andamento') echo 'hidden'; ?>>Em Análise</option>
                                                    <option value="resolvido" <?php if($oco['status'] == 'resolvido') echo 'hidden'; ?>>Resolvido</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                        <i class="fa-solid fa-clipboard-check text-4xl mb-3 text-green-200"></i><br>
                                        Nenhuma ocorrência registrada no condomínio. Tudo em ordem!
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </main>
</body>
</html>