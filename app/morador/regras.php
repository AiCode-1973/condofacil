<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_acesso'] !== 'morador') {
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$condominioId = $_SESSION['condominio_id'];

// Buscar regras do condomínio
$stmt = $pdo->prepare("SELECT * FROM regras WHERE condominio_id = ? ORDER BY data_atualizacao DESC");
$stmt->execute([$condominioId]);
$regras = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Nome do condomínio
$condominioNome = $pdo->prepare("SELECT nome FROM condominios WHERE id = ?");
$condominioNome->execute([$condominioId]);
$condominioNome = $condominioNome->fetchColumn() ?: "Meu Condomínio";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Regimento Interno - CondoFácil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        function toggleMobileMenu() {
            var menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
        
        // Função simples para filtrar/buscar regras na tela
        function searchRules() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            let rules = document.getElementsByClassName('rule-card');
            
            for (let i = 0; i < rules.length; i++) {
                let title = rules[i].getElementsByTagName("h3")[0];
                let content = rules[i].getElementsByTagName("div")[0];
                if (title.innerHTML.toLowerCase().indexOf(input) > -1 || content.innerHTML.toLowerCase().indexOf(input) > -1) {
                    rules[i].style.display = "";
                } else {
                    rules[i].style.display = "none";
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 font-sans min-h-screen flex flex-col md:flex-row">

    <!-- Header Mobile -->
    <div class="md:hidden bg-green-700 text-white flex justify-between items-center p-4 shadow-md sticky top-0 z-50">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-book-bookmark text-xl"></i>
            <span class="font-bold text-lg">Regras do Condomínio</span>
        </div>
        <button onclick="toggleMobileMenu()" class="text-white hover:text-green-200 focus:outline-none p-1">
            <i class="fa-solid fa-bars text-2xl"></i>
        </button>
    </div>

    <!-- Sidebar / Menu -->
    <aside id="mobile-menu" class="hidden md:flex flex-col w-full md:w-64 bg-green-700 text-white shadow-xl h-auto md:h-screen sticky top-0 z-40 md:z-auto transition-all">
        <div class="p-5 border-b border-green-600 font-bold hidden md:flex flex-col items-center justify-center gap-2">
            <i class="fa-solid fa-house-chimney-window text-4xl text-green-300"></i>
            <span class="text-xs uppercase tracking-wider text-green-200 font-semibold mb-1">Painel Morador</span>
        </div>
        
        <nav class="flex-1 p-2 md:p-4 space-y-1 overflow-y-auto">
            <a href="index.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-green-600 text-green-100 hover:text-white transition-colors">
                <i class="fa-solid fa-house w-6 text-center"></i> Início
            </a>
            <a href="avisos.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-green-600 text-green-100 hover:text-white transition-colors">
                <i class="fa-solid fa-bell w-6 text-center"></i> Avisos
            </a>
            <a href="reservas.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-green-600 text-green-100 hover:text-white transition-colors">
                <i class="fa-solid fa-calendar-check w-6 text-center"></i> Reservas
            </a>
            <a href="ocorrencias.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-green-600 text-green-100 hover:text-white transition-colors">
                <i class="fa-solid fa-triangle-exclamation w-6 text-center"></i> Ocorrências
            </a>
            <a href="regras.php" class="flex items-center gap-3 p-3 bg-green-800 text-green-50 rounded-lg transition-colors font-medium">
                <i class="fa-solid fa-book w-6 text-center"></i> Regras Internas
            </a>
            <a href="../logout.php" class="flex md:hidden items-center gap-3 p-3 mt-4 text-red-300 hover:bg-green-600 transition rounded-lg">
                <i class="fa-solid fa-right-from-bracket w-6 text-center"></i> Sair
            </a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 w-full overflow-y-auto">
        <header class="hidden md:flex bg-white shadow-sm px-8 py-5 border-b border-gray-200 justify-between items-center sticky top-0 z-30">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Regimento Interno e Normas</h1>
                <p class="text-sm text-gray-500">Conheça as regras para a boa convivência no condomínio.</p>
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" id="desktopSearch" onkeyup="document.getElementById('searchInput').value = this.value; searchRules();" placeholder="Buscar regra..." class="border border-gray-300 rounded-lg py-2 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
            </div>
        </header>

        <div class="p-4 md:p-8 max-w-4xl mx-auto">

            <!-- Mobile Search Bar (Visível apenas no celular) -->
            <div class="md:hidden relative mb-6">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" id="searchInput" onkeyup="searchRules()" placeholder="Pesquisar termo ou regra..." class="w-full border border-gray-300 rounded-xl py-3 pl-10 pr-4 focus:outline-none focus:border-green-500 shadow-sm text-sm">
            </div>
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-slate-800 to-slate-700 p-6 sm:p-8 text-white text-center">
                    <i class="fa-solid fa-scale-balanced text-4xl mb-3 text-slate-300"></i>
                    <h2 class="text-2xl font-bold mb-2">Regimento Interno</h2>
                    <p class="text-slate-300 text-sm sm:text-base max-w-lg mx-auto">
                        Este documento contém as normas fundamentais do <strong><?php echo htmlspecialchars($condominioNome); ?></strong>.
                    </p>
                </div>
                
                <div class="p-4 sm:p-8">
                    <?php if (count($regras) > 0): ?>
                        <div class="space-y-6">
                            <?php foreach ($regras as $index => $regra): ?>
                                <div class="rule-card bg-white border border-gray-100 shadow-sm rounded-xl p-5 sm:p-6 hover:border-gray-300 transition-colors">
                                    <div class="flex items-center gap-3 mb-3 border-b border-gray-100 pb-3">
                                        <div class="bg-indigo-100 text-indigo-700 font-bold rounded-lg w-8 h-8 flex items-center justify-center shrink-0">
                                            <?php echo $index + 1; ?>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($regra['titulo']); ?></h3>
                                    </div>
                                    <div class="text-gray-600 text-sm sm:text-base leading-relaxed whitespace-pre-wrap ml-11">
                                        <?php echo htmlspecialchars($regra['conteudo']); ?>
                                    </div>
                                    <div class="ml-11 mt-4 text-xs text-gray-400">
                                        <em>Atualizado em: <?php echo date('d/m/Y', strtotime($regra['data_atualizacao'])); ?></em>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12 flex flex-col items-center justify-center">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <i class="fa-solid fa-file-contract text-4xl text-gray-300"></i>
                            </div>
                            <h3 class="text-gray-900 font-bold mb-1 text-lg">Sem Normas Publicadas</h3>
                            <p class="text-gray-500 text-sm max-w-sm">O síndico ainda não disponibilizou o regimento interno nesta plataforma.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <p class="text-center text-gray-400 text-xs mt-6 mb-8">O desconhecimento das regras não isenta o morador de suas responsabilidades.</p>

        </div>
    </main>

</body>
</html>