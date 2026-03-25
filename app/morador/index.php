<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_acesso'] !== 'morador') {
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$condominioId = $_SESSION['condominio_id'];
$usuarioId = $_SESSION['usuario_id'];

// Recupera informações básicas
$usuarioNome = $_SESSION['usuario_nome'] ?? 'Morador';
$unidade = $_SESSION['unidade'] ?? 'N/D';

// Nome do condomínio
$condominioNome = $pdo->prepare("SELECT nome FROM condominios WHERE id = ?");
$condominioNome->execute([$condominioId]);
$condominioNome = $condominioNome->fetchColumn() ?: "Meu Condomínio";

$moradorId = $_SESSION['usuario_id'];

// Total de Avisos
$totalAvisos = $pdo->prepare("SELECT COUNT(*) FROM avisos WHERE condominio_id = ?");
$totalAvisos->execute([$condominioId]);
$totalAvisos = $totalAvisos->fetchColumn();

// Minhas Reservas
$minhasReservas = $pdo->prepare("SELECT COUNT(*) FROM reservas WHERE condominio_id = ? AND usuario_id = ?");
$minhasReservas->execute([$condominioId, $moradorId]);
$minhasReservas = $minhasReservas->fetchColumn();

// Mensagens não lidas
$mensagensNaoLidas = $pdo->prepare("SELECT COUNT(*) FROM mensagens_chat WHERE condominio_id = ? AND destinatario_id = ? AND lida = 0");
$mensagensNaoLidas->execute([$condominioId, $moradorId]);
$mensagensNaoLidas = $mensagensNaoLidas->fetchColumn();

// Minhas Ocorrências
$minhasOcorrencias = $pdo->prepare("SELECT COUNT(*) FROM ocorrencias WHERE condominio_id = ? AND usuario_id = ?");
$minhasOcorrencias->execute([$condominioId, $moradorId]);
$minhasOcorrencias = $minhasOcorrencias->fetchColumn();

// Últimos Avisos
$ultimosAvisos = $pdo->prepare("SELECT * FROM avisos WHERE condominio_id = ? ORDER BY fixado DESC, created_at DESC LIMIT 3");
$ultimosAvisos->execute([$condominioId]);
$ultimosAvisos = $ultimosAvisos->fetchAll(PDO::FETCH_ASSOC);

function badgeFixado($fixado) {
    if ($fixado) {
        return '<span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-[10px] uppercase font-bold rounded shadow-sm"><i class="fa-solid fa-thumbtack mr-1"></i> Fixado</span>';
    }
    return '<span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[10px] uppercase font-bold rounded shadow-sm">Aviso</span>';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Morador - CondoFácil</title>
    <!-- Adicionando meta viewport para responsividade mobile -->
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
        <button onclick="toggleMobileMenu()" class="text-white hover:text-green-200 focus:outline-none focus:ring-2 focus:ring-green-400 p-1 rounded">
            <i class="fa-solid fa-bars text-2xl"></i>
        </button>
    </div>

    <!-- Sidebar / Menu Mobile -->
    <aside id="mobile-menu" class="hidden md:flex flex-col w-full md:w-64 bg-green-700 text-white shadow-xl h-auto md:h-screen sticky top-0 z-40 md:z-auto transition-all duration-300">
        <div class="p-5 border-b border-green-600 font-bold hidden md:flex flex-col items-center justify-center gap-2">
            <i class="fa-solid fa-house-chimney-window text-4xl text-green-300"></i>
            <span class="text-xs uppercase tracking-wider text-green-200 font-semibold mb-1">Painel Morador</span>
            <span class="text-xl text-center w-full truncate leading-tight" title="<?php echo htmlspecialchars($condominioNome); ?>">
                <?php echo htmlspecialchars($condominioNome); ?>
            </span>
        </div>
        
        <div class="p-4 bg-green-800 md:hidden flex items-center gap-3 border-b border-green-600">
             <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center text-lg font-bold">
                 <?php echo substr($usuarioNome, 0, 1); ?>
             </div>
             <div class="flex flex-col">
                 <span class="font-bold text-sm truncate w-48"><?php echo htmlspecialchars($usuarioNome); ?></span>
                 <span class="text-xs text-green-300">Unidade: <?php echo htmlspecialchars($unidade); ?></span>
             </div>
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

        <div class="p-4 border-t border-green-600 bg-green-900 hidden md:block mt-auto">
             <div class="flex flex-col mb-4">
                 <span class="text-sm font-semibold truncate text-white" title="<?php echo htmlspecialchars($usuarioNome); ?>">
                     <?php echo htmlspecialchars($usuarioNome); ?>
                 </span>
                 <span class="text-xs text-green-300 font-medium tracking-wide">
                     Unidade: <?php echo htmlspecialchars($unidade); ?>
                 </span>
             </div>
             <a href="../logout.php" class="flex items-center gap-2 p-2 w-full justify-center bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors shadow-sm text-sm font-medium">
                 <i class="fa-solid fa-right-from-bracket"></i> Sair da Conta
             </a>
        </div>
        
        <!-- Botão Sair Mobile (aparece apenas no menu aberto do mobile) -->
         <div class="p-4 md:hidden">
             <a href="../logout.php" class="flex items-center gap-2 p-3 w-full justify-center bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold transition-colors shadow shadow-red-500/30">
                 <i class="fa-solid fa-right-from-bracket text-lg"></i> Sair do Sistema
             </a>
         </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 w-full overflow-y-auto">
        <!-- Header Desktop -->
        <header class="hidden md:flex bg-white shadow-sm px-8 py-5 border-b border-gray-200 justify-between items-center sticky top-0 z-30">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Olá, <?php echo htmlspecialchars(explode(' ', $usuarioNome)[0]); ?>!</h1>
                <p class="text-sm text-gray-500">Veja o que está acontecendo no seu condomínio.</p>
            </div>
            
             <a href="ocorrencias.php" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition flex items-center gap-2 shadow-md shadow-green-500/20">
                <i class="fa-solid fa-ticket"></i> Nova Ocorrência
            </a>
        </header>

        <!-- Container Principal de Conteúdo -->
        <div class="p-4 md:p-8 max-w-7xl mx-auto">
            
            <!-- Cards Resumo Mobile & Desktop -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-8">
                
                <!-- Card -->
                <a href="avisos.php" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-5 hover:shadow-md transition-shadow flex flex-col relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fa-solid fa-bell text-5xl text-yellow-500"></i>
                    </div>
                    <div class="flex items-center justify-between mb-2 md:mb-4">
                        <div class="w-10 h-10 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-bell"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded-full">Atualizado</span>
                    </div>
                      <p class="text-2xl md:text-3xl font-bold text-gray-800"><?php echo $totalAvisos; ?></p>
                    <p class="text-xs md:text-sm font-medium text-gray-500 mt-1">Avisos Recentes</p>
                </a>

                <!-- Card -->
                <a href="reservas.php" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-5 hover:shadow-md transition-shadow flex flex-col relative overflow-hidden group">
                    <!-- ... repetido padrão de design ... -->
                    <div class="flex items-center justify-between mb-2 md:mb-4">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                    </div>
                      <p class="text-2xl md:text-3xl font-bold text-gray-800"><?php echo $minhasReservas; ?></p>
                    <p class="text-xs md:text-sm font-medium text-gray-500 mt-1">Minhas Reservas</p>
                </a>

                 <!-- Card -->
                <a href="chat.php" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-5 hover:shadow-md transition-shadow flex flex-col relative overflow-hidden">
                    <div class="flex items-center justify-between mb-2 md:mb-4">
                        <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-comments"></i>
                        </div>
                    </div>
                      <p class="text-2xl md:text-3xl font-bold text-gray-800"><?php echo $mensagensNaoLidas; ?></p>
                    <p class="text-xs md:text-sm font-medium text-gray-500 mt-1">Chat / Mensagens</p>
                </a>
                
                 <!-- Card -->
                <a href="ocorrencias.php" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-5 hover:shadow-md transition-shadow flex flex-col relative overflow-hidden">
                    <div class="flex items-center justify-between mb-2 md:mb-4">
                        <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                    </div>
                      <p class="text-2xl md:text-3xl font-bold text-gray-800"><?php echo $minhasOcorrencias; ?></p>
                    <p class="text-xs md:text-sm font-medium text-gray-500 mt-1">Minhas Ocorrências</p>
                </a>

            </div>

            <!-- Colunas de Conteúdo Principal -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
                
                <!-- Área Principal: Mural e Atividades (ocupa 2 colunas no desktop) -->
                <div class="lg:col-span-2 space-y-6 md:space-y-8">
                    
                    <!-- Último Aviso Importante em Destaque Mobile/Desktop -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                    <i class="fa-solid fa-bullhorn text-yellow-500"></i> Mural de Avisos
                                </h2>
                                <a href="avisos.php" class="text-sm font-medium text-green-600 hover:text-green-700">Ver todos</a>
                            </div>
                            <div class="divide-y divide-gray-100">
                                <?php if(empty($ultimosAvisos)): ?>
                                    <div class="p-8 text-center bg-white flex flex-col items-center justify-center min-h-[200px]">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <i class="fa-regular fa-folder-open text-3xl text-gray-300"></i>
                                        </div>
                                        <h3 class="text-gray-900 font-semibold mb-1">Caixa Vazia</h3>
                                        <p class="text-gray-500 text-sm max-w-xs">Não há nenhum aviso do síndico ou administração no momento.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach($ultimosAvisos as $aviso): ?>
                                        <div class="p-5 hover:bg-gray-50 transition-colors">
                                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-3 mb-2">
                                                        <?php echo badgeFixado($aviso['fixado']); ?>
                                                        <span class="text-xs font-semibold text-gray-400 flex items-center gap-1">
                                                            <i class="fa-regular fa-clock"></i> 
                                                            <?php echo date('d/m/Y', strtotime($aviso['created_at'])); ?> 
                                                            às <?php echo date('H:i', strtotime($aviso['created_at'])); ?>
                                                        </span>
                                                    </div>
                                                    <h3 class="text-base font-bold text-gray-900 mb-1"><?php echo htmlspecialchars($aviso['titulo']); ?></h3>
                                                    <p class="text-sm text-gray-600 leading-relaxed"><?php echo nl2br(htmlspecialchars($aviso['conteudo'])); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <!-- Botões de Ação Rápida (visíveis com mais ênfase no mobile) -->
                    <div class="md:hidden grid grid-cols-2 gap-4">
                        <button class="bg-green-600 text-white p-4 rounded-xl font-bold shadow-md flex flex-col items-center gap-2">
                             <i class="fa-solid fa-calendar-plus text-2xl"></i> Reservar
                        </button>
                        <button class="bg-blue-600 text-white p-4 rounded-xl font-bold shadow-md flex flex-col items-center gap-2">
                             <i class="fa-solid fa-comments text-2xl"></i> Falar Síndico
                        </button>
                    </div>

                </div>

                <!-- Barra Lateral Direita: Atalhos e Regras (1 coluna no desktop) -->
                <div class="space-y-6 md:space-y-8">
                    
                    <!-- Atalhos Rápidos Dashboard -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h2 class="text-base font-bold text-gray-800">Acesso Rápido</h2>
                        </div>
                        <div class="p-2">
                            <a href="#" class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-lg transition-colors group border-b border-gray-50 last:border-0">
                                <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center group-hover:bg-green-600 group-hover:text-white transition-colors">
                                    <i class="fa-solid fa-swimming-pool"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 group-hover:text-green-700">Reservar Área Comum</p>
                                    <p class="text-xs text-gray-500">Churrasqueira, Salão...</p>
                                </div>
                                <i class="fa-solid fa-chevron-right ml-auto text-gray-300"></i>
                            </a>
                            <a href="#" class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-lg transition-colors group border-b border-gray-50 last:border-0">
                                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                    <i class="fa-solid fa-file-invoice-dollar"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 group-hover:text-blue-700">Segunda Via Boleto</p>
                                    <p class="text-xs text-gray-500">Taxa Condominial</p>
                                </div>
                                <i class="fa-solid fa-chevron-right ml-auto text-gray-300"></i>
                            </a>
                            <a href="#" class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-lg transition-colors group">
                                <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                    <i class="fa-solid fa-book"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 group-hover:text-indigo-700">Regimento Interno</p>
                                    <p class="text-xs text-gray-500">Normas do condomínio</p>
                                </div>
                                <i class="fa-solid fa-chevron-right ml-auto text-gray-300"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
            
             <!-- Footer do Painel Mobile -->
            <footer class="mt-8 pt-4 border-t border-gray-200 text-center text-xs text-gray-400 pb-16 md:pb-4">
                &copy; <?php echo date('Y'); ?> CondoFácil - Plataforma Inteligente
            </footer>

        </div>
    </main>

</body>
</html>