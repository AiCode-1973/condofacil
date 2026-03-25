<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_acesso'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

// Busca dados resumo para o condominio do sindico
$condominioId = $_SESSION['condominio_id'];
$totalMoradores = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE condominio_id = ? AND tipo_acesso = 'morador'");
$totalMoradores->execute([$condominioId]);
$totalMoradores = $totalMoradores->fetchColumn();

// Recupera nome do condominio
$condominioNome = $pdo->prepare("SELECT nome FROM condominios WHERE id = ?");
$condominioNome->execute([$condominioId]);
$condominioNome = $condominioNome->fetchColumn() ?: "Seu Condomínio";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Síndico - CondoFácil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex h-screen font-sans">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-800 text-white flex flex-col shadow-lg">
        <div class="p-5 border-b border-slate-700 font-bold flex flex-col items-center justify-center gap-1">
            <i class="fa-solid fa-hotel text-3xl text-green-400"></i>
            <span class="text-sm uppercase tracking-wide text-slate-300">Administração</span>
            <span class="text-lg truncate w-full text-center" title="<?php echo htmlspecialchars($condominioNome); ?>">
                <?php echo htmlspecialchars($condominioNome); ?>
            </span>
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
        <div class="p-4 border-t border-slate-700 bg-slate-900">
            <div class="mb-3 px-2 text-sm text-slate-200 truncate flex items-center gap-2">
                <i class="fa-solid fa-user-tie"></i> 
                <span><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></span>
            </div>
            <a href="../logout.php" class="flex items-center justify-center gap-2 p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition w-full">
                <i class="fa-solid fa-right-from-bracket"></i> Sair
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">
        <header class="bg-white shadow-sm px-8 py-5 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Painel do Síndico</h1>
            <div class="flex gap-4">
                 <a href="avisos.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-plus"></i> Novo Aviso
                </a>
            </div>
        </header>

        <div class="p-8">
            <!-- Widgets -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Widget -->
                <div class="bg-white rounded-xl shadow-sm border-l-4 border-emerald-500 p-6">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Moradores Registrados</p>
                    <div class="flex items-end justify-between mt-3">
                        <p class="text-3xl font-bold text-gray-800"><?php echo $totalMoradores; ?></p>
                        <i class="fa-solid fa-user-group text-2xl text-emerald-200"></i>
                    </div>
                </div>

                <!-- Widget -->
                <div class="bg-white rounded-xl shadow-sm border-l-4 border-yellow-500 p-6">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Ocorrências Pendentes</p>
                    <div class="flex items-end justify-between mt-3">
                        <p class="text-3xl font-bold text-gray-800">0</p>
                        <i class="fa-solid fa-triangle-exclamation text-2xl text-yellow-200"></i>
                    </div>
                </div>

                <!-- Widget -->
                <div class="bg-white rounded-xl shadow-sm border-l-4 border-blue-500 p-6">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Reservas Hoje</p>
                    <div class="flex items-end justify-between mt-3">
                        <p class="text-3xl font-bold text-gray-800">0</p>
                        <i class="fa-solid fa-calendar-day text-2xl text-blue-200"></i>
                    </div>
                </div>
                
                 <!-- Widget -->
                <div class="bg-white rounded-xl shadow-sm border-l-4 border-purple-500 p-6">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Mensagens Chat</p>
                    <div class="flex items-end justify-between mt-3">
                        <p class="text-3xl font-bold text-gray-800">0</p>
                        <i class="fa-solid fa-comments text-2xl text-purple-200"></i>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Atividades Recentes -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800">Últimas Ocorrências</h2>
                    </div>
                    <div class="p-6 text-center text-gray-400 py-10 flex flex-col items-center">
                        <i class="fa-solid fa-clipboard-check text-4xl mb-3 text-gray-200"></i>
                        <p>Nenhuma ocorrência registrada recentemente.</p>
                    </div>
                </div>

                <!-- Avisos Ativos -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800">Mural de Avisos (Ativos)</h2>
                    </div>
                    <div class="p-6 text-center text-gray-400 py-10 flex flex-col items-center">
                         <i class="fa-regular fa-bell-slash text-4xl mb-3 text-gray-200"></i>
                         <p>Nenhum aviso ativo no momento.</p>
                    </div>
                </div>

            </div>
        </div>
    </main>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #475569; /* slate-600 */
            border-radius: 20px;
        }
    </style>
</body>
</html>