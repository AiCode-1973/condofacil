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

// Processa o formulário de criação e exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao']) && $_POST['acao'] === 'criar') {
        $autorId = $_SESSION['usuario_id'];
        $titulo = trim($_POST['titulo']);
        $mensagem = trim($_POST['mensagem']);

        if (!empty($titulo) && !empty($mensagem)) {
            $stmt = $pdo->prepare("INSERT INTO avisos (condominio_id, autor_id, titulo, conteudo) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$condominioId, $autorId, $titulo, $mensagem])) {
                $mensagemSucesso = "Aviso publicado com sucesso!";
            } else {
                $mensagemErro = "Erro ao publicar o aviso.";
            }
        } else {
            $mensagemErro = "Preencha todos os campos.";
        }
    } elseif (isset($_POST['acao']) && $_POST['acao'] === 'excluir') {
        $id = $_POST['aviso_id'];
        $stmt = $pdo->prepare("DELETE FROM avisos WHERE id = ? AND condominio_id = ?");
        if ($stmt->execute([$id, $condominioId])) {
            $mensagemSucesso = "Aviso removido com sucesso!";
        } else {
            $mensagemErro = "Erro ao remover o aviso.";
        }
    }
}

// Busca todos os avisos do condomínio
$stmt = $pdo->prepare("SELECT * FROM avisos WHERE condominio_id = ? ORDER BY created_at DESC");
$stmt->execute([$condominioId]);
$avisos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Avisos - CondoFácil</title>
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
            <a href="avisos.php" class="flex items-center gap-3 p-3 bg-slate-700 rounded-lg transition">
                <i class="fa-solid fa-bullhorn w-5 text-center"></i> Avisos
            </a>
            <a href="../logout.php" class="flex items-center gap-3 p-3 mt-4 text-red-400 hover:bg-slate-700 transition rounded-lg">
                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> Sair
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">
        <header class="bg-white shadow-sm px-8 py-5 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Gerenciar Avisos</h1>
        </header>

        <div class="p-8 max-w-5xl mx-auto">
            
            <?php if ($mensagemSucesso): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <?php echo $mensagemSucesso; ?>
                </div>
            <?php endif; ?>
            <?php if ($mensagemErro): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <?php echo $mensagemErro; ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Formulário de Novo Aviso -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-plus text-green-600 mr-2"></i> Novo Aviso</h2>
                        <form method="POST" action="">
                            <input type="hidden" name="acao" value="criar">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="titulo">Título</label>
                                <input class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500" id="titulo" name="titulo" type="text" placeholder="Ex: Manutenção da Piscina" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="mensagem">Mensagem</label>
                                <textarea class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 min-h-[120px]" id="mensagem" name="mensagem" placeholder="Detalhes do aviso..." required></textarea>
                            </div>
                            <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded w-full transition" type="submit">
                                Publicar Aviso
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Lista de Avisos Cadastrados -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800"><i class="fa-solid fa-list-ul text-blue-600 mr-2"></i> Avisos Publicados</h2>
                        </div>
                        <div class="p-6">
                            <?php if (count($avisos) > 0): ?>
                                <div class="space-y-4">
                                    <?php foreach ($avisos as $aviso): ?>
                                        <div class="border border-gray-100 rounded-lg p-4 hover:shadow-md transition">
                                            <div class="flex justify-between items-start mb-2">
                                                <h3 class="font-bold text-lg text-gray-800"><?php echo htmlspecialchars($aviso['titulo']); ?></h3>
                                                <form method="POST" action="" onsubmit="return confirm('Tem certeza que deseja apagar este aviso?');">
                                                    <input type="hidden" name="acao" value="excluir">
                                                    <input type="hidden" name="aviso_id" value="<?php echo $aviso['id']; ?>">
                                                    <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Excluir Aviso">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <p class="text-gray-600 text-sm whitespace-pre-wrap"><?php echo htmlspecialchars($aviso['conteudo']); ?></p>
                                            <div class="mt-3 text-xs text-gray-400 flex items-center gap-1">
                                                <i class="fa-regular fa-clock"></i> Publicado em: <?php echo date('d/m/Y H:i', strtotime($aviso['created_at'])); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center text-gray-400 py-10">
                                    <i class="fa-regular fa-bell-slash text-4xl mb-3 text-gray-200"></i>
                                    <p>Nenhum aviso publicado no momento.</p>
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