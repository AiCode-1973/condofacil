<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_acesso'] !== 'superadmin') {
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

// Busca dados resumo
$totalCondominios = $pdo->query("SELECT COUNT(*) FROM condominios")->fetchColumn();
$totalUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Super Admin - CondoFácil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex h-screen font-sans">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-green-800 text-white flex flex-col shadow-lg">
        <div class="p-5 border-b border-green-700 text-2xl font-bold flex items-center justify-center gap-3">
            <i class="fa-solid fa-server text-green-300"></i>
            <span>SaaS Core</span>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="index.php" class="flex items-center gap-3 p-3 bg-green-700 rounded-lg transition">
                <i class="fa-solid fa-chart-pie w-5 text-center"></i> Dashboard
            </a>
            <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-green-700 transition">
                <i class="fa-solid fa-building w-5 text-center"></i> Condomínios
            </a>
            <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-green-700 transition">
                <i class="fa-solid fa-users-gear w-5 text-center"></i> Síndicos / Admins
            </a>
        </nav>
        <div class="p-4 border-t border-green-700 bg-green-900">
            <div class="mb-3 px-2 text-sm text-green-200 truncate">
                <i class="fa-solid fa-user-circle mr-1"></i> <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
            </div>
            <a href="../logout.php" class="flex items-center justify-center gap-2 p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition w-full">
                <i class="fa-solid fa-power-off"></i> Sair do Sistema
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">
        <header class="bg-white shadow-sm px-8 py-5 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-800">Visão Geral do SaaS</h1>
        </header>

        <div class="p-8">
            <!-- Widgets -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Widget 1 -->
                <div class="bg-white rounded-xl shadow-sm border-l-4 border-green-500 p-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Condomínios Ativos</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $totalCondominios; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-xl">
                        <i class="fa-solid fa-city"></i>
                    </div>
                </div>

                <!-- Widget 2 -->
                <div class="bg-white rounded-xl shadow-sm border-l-4 border-blue-500 p-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Usuários Totais</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $totalUsuarios; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xl">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>

                <!-- Widget 3 -->
                <div class="bg-white rounded-xl shadow-sm border-l-4 border-yellow-500 p-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Módulos</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">100%</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600 text-xl">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                </div>
            </div>

            <!-- Content Area Placeholder -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-800">Últimos Condomínios Registrados</h2>
                    <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-medium transition">
                        + Novo Condomínio
                    </button>
                </div>
                <div class="p-6 text-center text-gray-500 py-12">
                    <i class="fa-regular fa-folder-open text-4xl mb-3 text-gray-300"></i>
                    <p>Nenhum condomínio cadastrado além do ambiente de teste.</p>
                </div>
            </div>
        </div>
    </main>

</body>
</html>