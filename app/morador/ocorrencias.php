<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_acesso'] !== 'morador') {
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$condominioId = $_SESSION['condominio_id'];
$usuarioId = $_SESSION['usuario_id'];
$mensagemSucesso = '';
$mensagemErro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'nova_ocorrencia') {
    $tipo = $_POST['tipo'];
    $descricao = trim($_POST['descricao']);

    if (!empty($descricao) && !empty($tipo)) {
        // Assume coluna 'data_registro'. Usamos NOW() para ter um timestamp válido na inserção.
        $stmt = $pdo->prepare("INSERT INTO ocorrencias (condominio_id, usuario_id, tipo, descricao, status, data_registro) VALUES (?, ?, ?, ?, 'aberto', NOW())");
        if ($stmt->execute([$condominioId, $usuarioId, $tipo, $descricao])) {
            $mensagemSucesso = "Sua ocorrência foi enviada ao síndico com sucesso!";
        } else {
            $mensagemErro = "Houve um erro ao registrar a ocorrência.";
        }
    } else {
        $mensagemErro = "Por favor, preencha todos os campos.";
    }
}

// Histórico de Ocorrências
$stmt = $pdo->prepare("SELECT * FROM ocorrencias WHERE usuario_id = ? ORDER BY id DESC");
$stmt->execute([$usuarioId]);
$minhas_ocorrencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

function badgeStatus($status) {
    switch($status) {
        case 'aberto': return '<span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full">Aguardando Avaliação</span>';
        case 'em_andamento': return '<span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full">Em Andamento</span>';
        case 'resolvido': return '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full">Resolvido</span>';
        default: return '<span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded">'.$status.'</span>';
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
    <title>Minhas Ocorrências - CondoFácil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        function toggleMobileMenu() {
            var menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
</head>
<body class="bg-slate-50 font-sans min-h-screen flex flex-col md:flex-row">

    <div class="md:hidden bg-green-700 text-white flex justify-between items-center p-4 shadow-md sticky top-0 z-50">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            <span class="font-bold text-lg">Ocorrências</span>
        </div>
        <button onclick="toggleMobileMenu()" class="text-white focus:outline-none p-1">
            <i class="fa-solid fa-bars text-2xl"></i>
        </button>
    </div>

    <!-- Sidebar / Menu -->
    <aside id="mobile-menu" class="hidden md:flex flex-col w-full md:w-64 bg-green-700 text-white shadow-xl h-auto md:h-screen sticky top-0 z-40 md:z-auto transition-all">
        <div class="p-5 border-b border-green-600 font-bold hidden md:flex flex-col items-center justify-center gap-2">
            <i class="fa-solid fa-house-chimney-window text-4xl text-green-300"></i>
            <span class="text-xs uppercase tracking-wider text-green-200 font-semibold">Painel Morador</span>
        </div>
        <nav class="flex-1 p-2 md:p-4 space-y-1 overflow-y-auto">
            <a href="index.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-green-800 text-green-50 font-medium' : 'hover:bg-green-600 text-green-100 hover:text-white' ?> rounded-lg transition-colors">
                <i class="fa-solid fa-house w-6 text-center"></i> Início
            </a>
            <a href="avisos.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'avisos.php' ? 'bg-green-800 text-green-50 font-medium' : 'hover:bg-green-600 text-green-100 hover:text-white' ?> rounded-lg transition-colors">
                <i class="fa-solid fa-bell w-6 text-center"></i> Avisos
            </a>
            <a href="reservas.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'reservas.php' ? 'bg-green-800 text-green-50 font-medium' : 'hover:bg-green-600 text-green-100 hover:text-white' ?> rounded-lg transition-colors">
                <i class="fa-solid fa-calendar-check w-6 text-center"></i> Minhas Reservas
            </a>
            <a href="ocorrencias.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'ocorrencias.php' ? 'bg-green-800 text-green-50 font-medium' : 'hover:bg-green-600 text-green-100 hover:text-white' ?> rounded-lg transition-colors">
                <i class="fa-solid fa-triangle-exclamation w-6 text-center"></i> Ocorrências
            </a>
            <a href="regras.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'regras.php' ? 'bg-green-800 text-green-50 font-medium' : 'hover:bg-green-600 text-green-100 hover:text-white' ?> rounded-lg transition-colors">
                <i class="fa-solid fa-scale-balanced w-6 text-center"></i> Regras
            </a>
            <a href="chat.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'chat.php' ? 'bg-green-800 text-green-50 font-medium' : 'hover:bg-green-600 text-green-100 hover:text-white' ?> rounded-lg transition-colors">
                <i class="fa-solid fa-comments w-6 text-center"></i> Falar com Síndico
            </a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 w-full overflow-y-auto">
        <header class="hidden md:flex bg-white shadow-sm px-8 py-5 border-b border-gray-200 justify-between items-center sticky top-0 z-30">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Minhas Ocorrências</h1>
                <p class="text-sm text-gray-500">Registre reclamações, sugestões ou problemas de manutenção.</p>
            </div>
        </header>

        <div class="p-4 md:p-8 max-w-6xl mx-auto">
            <?php if ($mensagemSucesso): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6"><i class="fa-solid fa-check-circle mr-2"></i><?php echo $mensagemSucesso; ?></div>
            <?php endif; ?>
            <?php if ($mensagemErro): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6"><i class="fa-solid fa-circle-exclamation mr-2"></i><?php echo $mensagemErro; ?></div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
                
                <!-- Nova Ocorrência -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 h-fit">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3 flex items-center gap-2">
                            <i class="fa-solid fa-pen-to-square text-green-600"></i> Abrir Chamado
                        </h2>
                        <form method="POST">
                            <input type="hidden" name="acao" value="nova_ocorrencia">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Qual o motivo?</label>
                                <select name="tipo" class="shadow-sm border border-gray-300 rounded-lg w-full py-2.5 px-3 text-gray-700 bg-white" required>
                                    <option value="" disabled selected>Selecione uma opção...</option>
                                    <option value="Reclamação">Reclamação</option>
                                    <option value="Manutenção">Solicitação de Manutenção</option>
                                    <option value="Barulho">Problema com Barulho</option>
                                    <option value="Sugestão">Sugestão de Melhoria</option>
                                    <option value="Outros">Outros Assuntos</option>
                                </select>
                            </div>
                            <div class="mb-6">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Descreva a situação:</label>
                                <textarea name="descricao" class="shadow-sm border border-gray-300 rounded-lg w-full py-2.5 px-3 text-gray-700 min-h-[120px]" placeholder="Ex: Lâmpada do corredor do 5º andar está piscando e fazendo barulho..." required></textarea>
                            </div>
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl transition shadow-md">
                                Enviar para o Síndico
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Histórico -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-800"><i class="fa-solid fa-list-ul text-blue-600 mr-2"></i> Meus Chamados</h2>
                        </div>
                        <div class="p-6">
                            <?php if (count($minhas_ocorrencias) > 0): ?>
                                <div class="space-y-4">
                                    <?php foreach ($minhas_ocorrencias as $oco): ?>
                                        <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition bg-white relative">
                                            <div class="flex justify-between items-start mb-3">
                                                <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                                                    <?php echo iconeTipo($oco['tipo']); ?>
                                                    <?php echo htmlspecialchars($oco['tipo']); ?>
                                                </h3>
                                                <?php echo badgeStatus($oco['status']); ?>
                                            </div>
                                            <p class="text-gray-600 text-sm leading-relaxed bg-gray-50 p-3 rounded border border-gray-100 whitespace-pre-wrap">
                                                <?php echo htmlspecialchars($oco['descricao']); ?>
                                            </p>
                                            <div class="mt-3 text-xs text-gray-400 font-medium text-right">
                                                <?php echo date('d/m/Y \à\s H:i', strtotime($oco['data_registro'])); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-10">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fa-solid fa-check-double text-2xl text-green-300"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">Você ainda não enviou nenhuma ocorrência.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

</body>
</html>