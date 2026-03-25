<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_acesso'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$condominioId = $_SESSION['condominio_id'];
$sindicoId = $_SESSION['usuario_id']; // admin logado

// 1. Processar Cadastro de Usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    if ($_POST['acao'] === 'cadastrar_morador') {
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $unidade = trim($_POST['unidade']);
        
        // Verifica se e-mail já existe no mesmo condomínio para não duplicar moradores
        $stmtChk = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND condomio_id = ?");
        $stmtChk->execute([$email, $condominioId]);
        
        if ($stmtChk->rowCount() > 0) {
            $msgErro = "E-mail já cadastrado para este condomínio.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO usuarios (condomio_id, nome, email, senha, tipo_acesso, unidade) VALUES (?, ?, ?, ?, 'morador', ?)");
            $stmt->execute([$condominioId, $nome, $email, $senha, $unidade]);
            header("Location: moradores.php?sucesso=1");
            exit;
        }
    }

    // 2. Processar Deleção
    if ($_POST['acao'] === 'deletar_morador') {
        $idDeletar = intval($_POST['id']);
        // Segurança: só apaga seo o morador pertencer a este condomínio
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ? AND condomio_id = ? AND tipo_acesso = 'morador'");
        $stmt->execute([$idDeletar, $condominioId]);
        header("Location: moradores.php?deletado=1");
        exit;
    }
}

// 3. Listar Moradores
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE condomio_id = ? AND tipo_acesso = 'morador' ORDER BY unidade ASC, nome ASC");
$stmt->execute([$condominioId]);
$moradores = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Moradores - CondoFácil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex h-screen font-sans">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-800 text-white flex flex-col shadow-lg z-20">
        <div class="p-5 border-b border-slate-700 font-bold flex flex-col items-center justify-center gap-1">
            <i class="fa-solid fa-hotel text-3xl text-green-400"></i>
            <span class="text-sm uppercase tracking-wide text-slate-300">Administração</span>
        </div>
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto w-full">
            <a href="index.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-700 transition">
                <i class="fa-solid fa-house w-5 text-center"></i> Início
            </a>
            <a href="moradores.php" class="flex items-center gap-3 p-3 bg-slate-700 rounded-lg transition">
                <i class="fa-solid fa-users w-5 text-center"></i> Moradores
            </a>
            <a href="avisos.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-700 transition">
                <i class="fa-solid fa-bullhorn w-5 text-center"></i> Avisos
            </a>
            <a href="regras.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-700 transition">
                <i class="fa-solid fa-scale-balanced w-5 text-center"></i> Regras
            </a>
            <a href="../logout.php" class="flex items-center gap-3 p-3 mt-4 text-red-400 hover:bg-slate-700 transition rounded-lg">
                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> Sair
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-8">
        <div class="max-w-6xl mx-auto">
            
            <header class="flex justify-between items-end mb-8 border-b pb-4 border-gray-300">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Gestão de Moradores</h1>
                    <p class="text-gray-500 mt-1">Cadastre, edite ou remova acessos dos condôminos.</p>
                </div>
                <!-- Botão Modal -->
                <button onclick="document.getElementById('modalCadastro').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg shadow transition flex items-center gap-2 font-medium">
                    <i class="fa-solid fa-user-plus"></i> Novo Morador
                </button>
            </header>

            <?php if(isset($_GET['sucesso'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">Morador cadastrado com sucesso! As credenciais já estão ativas.</div>
            <?php endif; ?>
            
            <?php if(isset($msgErro)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm"><?php echo htmlspecialchars($msgErro); ?></div>
            <?php endif; ?>

            <!-- Tabela de Moradores -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Unidade/Apto</th>
                            <th scope="col" class="px-6 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Nome do Morador</th>
                            <th scope="col" class="px-6 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">E-mail de Acesso</th>
                            <th scope="col" class="px-6 py-3 text-left font-bold text-gray-500 uppercase tracking-wider w-24">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if(count($moradores) === 0): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500 font-medium">Nenhum morador cadastrado neste condomínio. <br><span class="text-xs opacity-70">Utilize o botão "Novo Morador" para adicionar.</span></td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach($moradores as $m): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-bold"><?php echo htmlspecialchars($m['unidade']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0">
                                            <?php echo substr($m['nome'], 0, 1); ?>
                                        </div>
                                        <?php echo htmlspecialchars($m['nome']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600"><?php echo htmlspecialchars($m['email']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <!-- Formulário de Exclusão Simples -->
                                    <form method="POST" onsubmit="return confirm('ATENÇÃO: Deseja realmente excluir este morador e remover seu acesso ao sistema?');" class="inline">
                                        <input type="hidden" name="acao" value="deletar_morador">
                                        <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded-md transition" title="Excluir Acesso"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                    <a href="chat.php?morador=<?php echo $m['id']; ?>" class="text-indigo-500 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-md transition ml-1" title="Enviar Mensagem Direta"><i class="fa-solid fa-comment-dots"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>

    <!-- Modal de Cadastro -->
    <div id="modalCadastro" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden animate-[fadeIn_0.2s_ease-out]">
            <div class="bg-slate-800 p-4 text-white flex justify-between items-center">
                <h3 class="font-bold text-lg"><i class="fa-solid fa-user-plus mr-2"></i> Criar Acesso de Morador</h3>
                <button onclick="document.getElementById('modalCadastro').classList.add('hidden')" class="text-gray-300 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            
            <form method="POST" action="" class="p-6">
                <input type="hidden" name="acao" value="cadastrar_morador">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-gray-700">Nome Completo</label>
                        <input type="text" name="nome" required placeholder="Ex: João da Silva" class="w-full border-gray-300 rounded-lg p-3 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition border">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-1">
                            <label class="block text-sm font-semibold mb-1 text-gray-700">Apto / Bloco / Lote</label>
                            <input type="text" name="unidade" required placeholder="Ex: 101 Bl A" class="w-full border-gray-300 rounded-lg p-3 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition border">
                        </div>
                        <div class="col-span-1">
                            <!-- Helper vázio para o grid não quebrar, mas vamos usar o grid 100% no campo de senha -->
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-gray-700">E-mail de Login</label>
                        <input type="email" name="email" required placeholder="Ex: morador@email.com" class="w-full border-gray-300 rounded-lg p-3 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition border">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1 text-gray-700">Senha Inicial</label>
                        <div class="relative">
                            <input type="password" name="senha" id="inputSenha" required placeholder="Pelo menos 6 caracteres" class="w-full border-gray-300 rounded-lg p-3 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition border pr-10">
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-indigo-600">
                                <i class="fa-regular fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">O morador usará e-mail e esta senha para acessar o painel CondoFácil.</p>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modalCadastro').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition font-medium">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition font-medium shadow-sm">Cadastrar e Conceder Acesso</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script para Toggle de Senha no Modal -->
    <script>
        function togglePassword() {
            const input = document.getElementById('inputSenha');
            const icon = document.getElementById('eyeIcon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>