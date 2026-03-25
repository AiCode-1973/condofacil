<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_acesso'] !== 'morador') {
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$condominioId = $_SESSION['condominio_id'];
$usuarioNome = $_SESSION['usuario_nome'];

// Busca todos os avisos do condomínio
$stmt = $pdo->prepare("SELECT * FROM avisos WHERE condominio_id = ? ORDER BY created_at DESC");
$stmt->execute([$condominioId]);
$avisos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Nome do condomínio
$condominioNome = $pdo->prepare("SELECT nome FROM condominios WHERE id = ?");
$condominioNome->execute([$condominioId]);
$condominioNome = $condominioNome->fetchColumn() ?: "Meu Condomínio";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Mural de Avisos - CondoFácil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        function toggleMobileMenu() {
            var menu = document.getElementById('mobile-menu');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
            }
        }
    </script>
</head>
<body class="bg-slate-50 font-sans min-h-screen flex flex-col md:flex-row">

    <!-- Header Mobile (visível apenas em telas pequenas) -->
    <div class="md:hidden bg-green-700 text-white flex justify-between items-center p-4 shadow-md sticky top-0 z-50">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-house-chimney-window text-xl"></i>
            <span class="font-bold text-lg truncate w-40"><?php echo htmlspecialchars($condominioNome); ?></span>
        </div>
        <button onclick="toggleMobileMenu()" class="text-white hover:text-green-200 focus:outline-none p-1 rounded">
            <i class="fa-solid fa-bars text-2xl"></i>
        </button>
    </div>

    <!-- Sidebar / Menu -->
    <aside id="mobile-menu" class="hidden md:flex flex-col w-full md:w-64 bg-green-700 text-white shadow-xl h-auto md:h-screen sticky top-0 z-40 md:z-auto transition-all duration-300">
        <div class="p-5 border-b border-green-600 font-bold hidden md:flex flex-col items-center justify-center gap-2">
            <i class="fa-solid fa-house-chimney-window text-4xl text-green-300"></i>
            <span class="text-xs uppercase tracking-wider text-green-200 font-semibold mb-1">Painel Morador</span>
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

        <div class="p-4 border-t border-green-600 bg-green-900 hidden md:block mt-auto text-center">
             <a href="../logout.php" class="flex items-center gap-2 p-2 w-full justify-center bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors shadow-sm text-sm font-medium">
                 <i class="fa-solid fa-right-from-bracket"></i> Sair da Conta
             </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 w-full overflow-y-auto">
        <header class="hidden md:flex bg-white shadow-sm px-8 py-5 border-b border-gray-200 justify-between items-center sticky top-0 z-30">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Mural de Avisos</h1>
                <p class="text-sm text-gray-500">Comunicados da administração do condomínio.</p>
            </div>
        </header>

        <div class="p-4 md:p-8 max-w-4xl mx-auto">
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-yellow-50/50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-bullhorn text-yellow-600"></i> Últimos Comunicados
                    </h2>
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded-full">Atualizado</span>
                </div>
                
                <div class="p-6">
                    <?php if (count($avisos) > 0): ?>
                        <div class="space-y-6">
                            <?php foreach ($avisos as $aviso): ?>
                                <div class="relative bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition-shadow">
                                    <div class="absolute top-0 right-0 mt-4 mr-4 text-gray-300">
                                        <i class="fa-solid fa-thumbtack"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-800 mb-2 truncate pr-6"><?php echo htmlspecialchars($aviso['titulo']); ?></h3>
                                    <p class="text-gray-600 text-sm md:text-base leading-relaxed whitespace-pre-wrap mb-4"><?php echo htmlspecialchars($aviso['conteudo']); ?></p>
                                    
                                    <div class="flex items-center text-xs text-gray-500 border-t border-gray-100 pt-3">
                                        <div class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded">
                                            <i class="fa-regular fa-calendar"></i>
                                            <span><?php echo date('d/m/Y', strtotime($aviso['created_at'])); ?></span>
                                        </div>
                                        <div class="flex items-center gap-1 ml-3 bg-gray-100 px-2 py-1 rounded">
                                            <i class="fa-regular fa-clock"></i>
                                            <span><?php echo date('H:i', strtotime($aviso['created_at'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-16 flex flex-col items-center justify-center">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <i class="fa-regular fa-folder-open text-4xl text-gray-300"></i>
                            </div>
                            <h3 class="text-gray-900 font-bold mb-1 text-lg">Mural Vazio</h3>
                            <p class="text-gray-500 text-sm max-w-sm">Não há nenhum aviso do síndico ou da administração no momento.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>

</body>
</html>