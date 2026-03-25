<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_acesso'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$condominioId = $_SESSION['condominio_id'];
$mensagemSucesso = '';
$mensagemErro = '';

// Adicionar ou Excluir Regra
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao']) && $_POST['acao'] === 'criar') {
        $titulo = trim($_POST['titulo']);
        $conteudo = trim($_POST['conteudo']);

        if (!empty($titulo) && !empty($conteudo)) {
            // Usa ON DUPLICATE KEY UPDATE ou um simples INSERT já que são várias regras?
            // Dependendo do banco pode ser um texto único, mas vamos fazer como "Artigos/Tópicos" de regra.
            $stmt = $pdo->prepare("INSERT INTO regras (condominio_id, titulo, conteudo, data_atualizacao) VALUES (?, ?, ?, NOW())");
            if ($stmt->execute([$condominioId, $titulo, $conteudo])) {
                $mensagemSucesso = "Regra/Documento adicionado com sucesso!";
            } else {
                $mensagemErro = "Erro ao adicionar a regra.";
            }
        } else {
            $mensagemErro = "Preencha título e conteúdo.";
        }
    } elseif (isset($_POST['acao']) && $_POST['acao'] === 'excluir') {
        $regra_id = $_POST['regra_id'];
        $stmt = $pdo->prepare("DELETE FROM regras WHERE id = ? AND condominio_id = ?");
        if ($stmt->execute([$regra_id, $condominioId])) {
            $mensagemSucesso = "Regra removida do sistema.";
        } else {
            $mensagemErro = "Erro ao deletar regra.";
        }
    }
}

// Buscar regras do condomínio
$stmt = $pdo->prepare("SELECT * FROM regras WHERE condominio_id = ? ORDER BY data_atualizacao DESC");
$stmt->execute([$condominioId]);
$regras = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Regimento e Regras - CondoFácil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex h-screen font-sans">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-800 text-white flex flex-col shadow-lg overflow-y-auto">
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
            <a href="areas_comuns.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-700 transition">
                <i class="fa-solid fa-swimming-pool w-5 text-center"></i> Áreas Comuns
            </a>
            <a href="reservas.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-700 transition">
                <i class="fa-solid fa-calendar-check w-5 text-center"></i> Reservas
            </a>
            <a href="ocorrencias.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-700 transition">
                <i class="fa-solid fa-clipboard-list w-5 text-center"></i> Ocorrências
            </a>
            <a href="regras.php" class="flex items-center gap-3 p-3 bg-slate-700 rounded-lg transition">
                <i class="fa-solid fa-book w-5 text-center"></i> Regras Internas
            </a>
            <a href="../logout.php" class="flex items-center gap-3 p-3 mt-4 text-red-400 hover:bg-slate-700 transition rounded-lg">
                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> Sair
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">
        <header class="bg-white shadow-sm px-8 py-5 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Regimento Interno</h1>
        </header>

        <div class="p-8 max-w-6xl mx-auto">
            <?php if ($mensagemSucesso): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><i class="fa-solid fa-check mr-2"></i> <?php echo $mensagemSucesso; ?></div>
            <?php endif; ?>
            <?php if ($mensagemErro): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><i class="fa-solid fa-circle-exclamation mr-2"></i> <?php echo $mensagemErro; ?></div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Cadastro de Regras -->
                <div class="md:col-span-1 bg-white p-6 rounded-xl shadow-sm border border-gray-200 h-fit">
                    <h2 class="text-lg font-bold mb-4 border-b pb-2"><i class="fa-solid fa-plus text-green-600 mr-2"></i> Novo Artigo/Regra</h2>
                    <form method="POST">
                        <input type="hidden" name="acao" value="criar">
                        <div class="mb-3">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Título / Tópico</label>
                            <input class="border rounded w-full py-2 px-3 text-gray-700 focus:ring-2 focus:ring-green-500 outline-none" name="titulo" type="text" placeholder="Ex: Horário de Mudanças" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Descrição da Regra</label>
                            <textarea class="border rounded w-full py-2 px-3 text-gray-700 text-sm min-h-[150px] focus:ring-2 focus:ring-green-500 outline-none" name="conteudo" placeholder="Descreva os detalhes e permissões aqui..." required></textarea>
                        </div>
                        <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 w-full rounded transition" type="submit">Adicionar ao Regimento</button>
                    </form>
                </div>

                <!-- Lista de Regras -->
                <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800"><i class="fa-solid fa-scale-balanced text-indigo-600 mr-2"></i> Regras e Tópicos Publicados</h2>
                    </div>
                    <div class="p-6">
                        <?php if (count($regras) > 0): ?>
                            <div class="space-y-4">
                                <?php foreach ($regras as $regra): ?>
                                    <div class="border border-gray-200 rounded-lg p-5 bg-gray-50 relative group">
                                        <div class="flex justify-between items-start">
                                            <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                                                <i class="fa-solid fa-file-contract text-gray-500"></i>
                                                <?php echo htmlspecialchars($regra['titulo']); ?>
                                            </h3>
                                            <form method="POST" onsubmit="return confirm('Deseja excluir esta regra do regimento?');">
                                                <input type="hidden" name="acao" value="excluir">
                                                <input type="hidden" name="regra_id" value="<?php echo $regra['id']; ?>">
                                                <button type="submit" class="text-red-400 hover:text-red-600 transition" title="Remover Regra"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                        <div class="mt-3 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">
                                            <?php echo htmlspecialchars($regra['conteudo']); ?>
                                        </div>
                                        <div class="mt-4 pt-3 border-t border-gray-200 text-xs text-gray-400 flex items-center gap-1">
                                            <i class="fa-regular fa-clock"></i> Última atualização: <?php echo date('d/m/Y H:i', strtotime($regra['data_atualizacao'])); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-10">
                                <i class="fa-solid fa-book-open text-4xl mb-3 text-gray-300"></i>
                                <p class="text-gray-500">Nenhum regimento cadastrado. Os moradores verão uma tela vazia.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>