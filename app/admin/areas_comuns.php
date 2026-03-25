<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_acesso'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$condominioId = $_SESSION['condominio_id'];
$mensagemErro = '';
$mensagemSucesso = '';

// Processa formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao']) && $_POST['acao'] === 'criar') {
        $nome = trim($_POST['nome']);
        $capacidade = intval($_POST['capacidade']);
        $regras = trim($_POST['regras']);

        if (!empty($nome)) {
            $stmt = $pdo->prepare("INSERT INTO areas_comuns (condominio_id, nome, capacidade, regras) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$condominioId, $nome, $capacidade, $regras])) {
                $mensagemSucesso = "Área Comum cadastrada com sucesso!";
            } else {
                $mensagemErro = "Erro ao cadastrar a área.";
            }
        } else {
            $mensagemErro = "O nome da área é obrigatório.";
        }
    } elseif (isset($_POST['acao']) && $_POST['acao'] === 'excluir') {
        $id = $_POST['area_id'];
        $stmt = $pdo->prepare("DELETE FROM areas_comuns WHERE id = ? AND condominio_id = ?");
        if ($stmt->execute([$id, $condominioId])) {
            $mensagemSucesso = "Área removida com sucesso!";
        } else {
            $mensagemErro = "Erro ao remover (pode haver reservas vinculadas).";
        }
    }
}

// Busca áreas do condomínio
$stmt = $pdo->prepare("SELECT * FROM areas_comuns WHERE condominio_id = ? ORDER BY nome ASC");
$stmt->execute([$condominioId]);
$areas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Áreas Comuns - CondoFácil</title>
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
        <nav class="flex-1 p-4 space-y-1">
            <a href="index.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-700 transition">
                <i class="fa-solid fa-house w-5 text-center"></i> Início
            </a>
            <a href="avisos.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-700 transition">
                <i class="fa-solid fa-bullhorn w-5 text-center"></i> Avisos
            </a>
            <a href="areas_comuns.php" class="flex items-center gap-3 p-3 bg-slate-700 rounded-lg transition">
                <i class="fa-solid fa-swimming-pool w-5 text-center"></i> Áreas Comuns
            </a>
            <a href="reservas.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-700 transition">
                <i class="fa-solid fa-calendar-check w-5 text-center"></i> Gestão de Reservas
            </a>
            <a href="../logout.php" class="flex items-center gap-3 p-3 mt-4 text-red-400 hover:bg-slate-700 transition rounded-lg">
                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> Sair
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">
        <header class="bg-white shadow-sm px-8 py-5 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Áreas Comuns</h1>
        </header>

        <div class="p-8 max-w-5xl mx-auto">
            <?php if ($mensagemSucesso): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo $mensagemSucesso; ?></div>
            <?php endif; ?>
            <?php if ($mensagemErro): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo $mensagemErro; ?></div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Cadastro -->
                <div class="md:col-span-1 bg-white p-6 rounded-xl shadow-sm border border-gray-200 h-fit">
                    <h2 class="text-lg font-bold mb-4 border-b pb-2"><i class="fa-solid fa-plus text-green-600 mr-2"></i> Nova Área</h2>
                    <form method="POST">
                        <input type="hidden" name="acao" value="criar">
                        <div class="mb-3">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nome do Espaço</label>
                            <input class="border rounded w-full py-2 px-3 text-gray-700" name="nome" type="text" placeholder="Ex: Salão de Festas" required>
                        </div>
                        <div class="mb-3">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Capacidade (Pessoas)</label>
                            <input class="border rounded w-full py-2 px-3 text-gray-700" name="capacidade" type="number" min="1" placeholder="Ex: 50">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Regras / Observações</label>
                            <textarea class="border rounded w-full py-2 px-3 text-gray-700 text-sm h-24" name="regras" placeholder="Proibido som alto após as 22h..."></textarea>
                        </div>
                        <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 w-full rounded transition" type="submit">Cadastrar Espaço</button>
                    </form>
                </div>

                <!-- Lista -->
                <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800"><i class="fa-solid fa-list text-blue-600 mr-2"></i> Espaços Existentes</h2>
                    </div>
                    <div class="p-6">
                        <?php if (count($areas) > 0): ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <?php foreach ($areas as $area): ?>
                                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 relative group">
                                        <div class="flex justify-between items-start">
                                            <h3 class="font-bold text-gray-800 text-lg"><?php echo htmlspecialchars($area['nome']); ?></h3>
                                            <form method="POST" onsubmit="return confirm('Deseja excluir esta área?');">
                                                <input type="hidden" name="acao" value="excluir">
                                                <input type="hidden" name="area_id" value="<?php echo $area['id']; ?>">
                                                <button type="submit" class="text-red-400 hover:text-red-600"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1"><i class="fa-solid fa-users text-gray-400"></i> Cap: <?php echo $area['capacidade'] ?: 'Não definida'; ?> pessoas</p>
                                        <p class="text-xs text-gray-500 mt-2 italic truncate" title="<?php echo htmlspecialchars($area['regras']); ?>">
                                            Regras: <?php echo $area['regras'] ? htmlspecialchars($area['regras']) : 'Nenhuma regra específica.'; ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-center text-gray-500 py-4">Nenhuma área comum cadastrada.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>