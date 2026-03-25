<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_acesso'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$condominioId = $_SESSION['condominio_id'];
$sindicoId = $_SESSION['usuario_id']; // admin logado

// 1. Processar envio de mensagem do síndico
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'enviar_msg') {
    $morador_id = $_POST['receptor_id'];
    $mensagem = trim($_POST['mensagem']);
    
    if (!empty($mensagem) && !empty($morador_id)) {
        $stmt = $pdo->prepare("INSERT INTO mensagens_chat (condominio_id, remetente_id, receptor_id, mensagem, lida) VALUES (?, ?, ?, ?, 0)");
        $stmt->execute([$condominioId, $sindicoId, $morador_id, $mensagem]);
    }
    // O POST fará reload na mesma página (se houver chat aberto, o JS precisaria de AJAX, mas vamos mantê-lo procedural simples)
    header("Location: chat.php?morador=" . $morador_id);
    exit;
}

// 2. Obter lista de moradores do condomínio para abrir o chat
$stmt = $pdo->prepare("SELECT id, nome, unidade FROM usuarios WHERE condominio_id = ? AND tipo_acesso = 'morador' ORDER BY nome ASC");
$stmt->execute([$condominioId]);
$moradores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Carregar Conversa Específica se houver um id na URL
$chatAtivo = isset($_GET['morador']) ? intval($_GET['morador']) : null;
$mensagens = [];
$moradorAtivo = null;

if ($chatAtivo) {
    // Pegar detalhes do morador
    $stmt = $pdo->prepare("SELECT id, nome, unidade FROM usuarios WHERE id = ? AND condominio_id = ?");
    $stmt->execute([$chatAtivo, $condominioId]);
    $moradorAtivo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($moradorAtivo) {
        // Marca as mensagens RECEBIDAS do morador como lidass
        $stmt = $pdo->prepare("UPDATE mensagens_chat SET lida = 1 WHERE remetente_id = ? AND receptor_id = ?");
        $stmt->execute([$chatAtivo, $sindicoId]);

        // Carregar a conversa (Síndico enviou para Morador OR Morador enviou para Síndico)
        $stmt = $pdo->prepare("SELECT * FROM mensagens_chat WHERE (remetente_id = ? AND receptor_id = ?) OR (remetente_id = ? AND receptor_id = ?) ORDER BY data_envio ASC");
        $stmt->execute([$sindicoId, $chatAtivo, $chatAtivo, $sindicoId]);
        $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Chat com Moradores - CondoFácil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Ajuste do scroll para suavizar o chat */
        .chat-scroll::-webkit-scrollbar { width: 6px; }
        .chat-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
        .chat-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>
</head>
<body class="bg-gray-100 flex h-screen font-sans overflow-hidden">
    
    <!-- Sidebar Principal -->
    <aside class="w-64 bg-slate-800 text-white flex flex-col shadow-lg z-20 hidden md:flex">
        <div class="p-5 border-b border-slate-700 font-bold flex flex-col items-center justify-center gap-1">
            <i class="fa-solid fa-hotel text-3xl text-green-400"></i>
            <span class="text-sm uppercase tracking-wide text-slate-300">Administração</span>
        </div>
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <a href="index.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-700 transition">
                <i class="fa-solid fa-house w-5 text-center"></i> Início
            </a>
            <a href="chat.php" class="flex items-center gap-3 p-3 bg-slate-700 rounded-lg transition">
                <i class="fa-regular fa-comments w-5 text-center"></i> Mensagens
            </a>
            <a href="ocorrencias.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-700 transition">
                <i class="fa-solid fa-clipboard-list w-5 text-center"></i> Ocorrências
            </a>
            <a href="../logout.php" class="flex items-center gap-3 p-3 mt-4 text-red-400 hover:bg-slate-700 transition rounded-lg">
                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> Sair
            </a>
        </nav>
    </aside>

    <!-- Estrutura do Chat -->
    <main class="flex-1 flex bg-white border-l border-gray-200">
        
        <!-- Contatos (Lista Lateral do Chat) -->
        <div class="w-full md:w-80 border-r border-gray-200 bg-gray-50 flex flex-col h-full <?php echo $chatAtivo ? 'hidden md:flex' : 'flex'; ?>">
             <div class="p-4 border-b border-gray-200 bg-white">
                 <h2 class="text-lg font-bold text-gray-800">Canais Diretos</h2>
                 <p class="text-xs text-gray-500">Selecione um lote/morador</p>
             </div>
             
             <div class="flex-1 overflow-y-auto p-2 space-y-1">
                 <?php foreach ($moradores as $morador): ?>
                     <a href="chat.php?morador=<?php echo $morador['id']; ?>" class="flex items-center gap-3 p-3 rounded-lg cursor-pointer transition-colors block <?php echo ($chatAtivo == $morador['id']) ? 'bg-indigo-100 border border-indigo-200' : 'hover:bg-gray-100 bg-white border border-transparent'; ?>">
                         <div class="w-10 h-10 rounded-full bg-indigo-200 text-indigo-700 flex items-center justify-center font-bold">
                             <?php echo substr($morador['nome'], 0, 1); ?>
                         </div>
                         <div class="flex-1 min-w-0">
                             <div class="flex justify-between items-center mb-0.5">
                                 <h3 class="text-sm font-bold text-gray-900 truncate"><?php echo htmlspecialchars($morador['nome']); ?></h3>
                             </div>
                             <p class="text-xs text-gray-500 truncate">Apto: <?php echo htmlspecialchars($morador['unidade']); ?></p>
                         </div>
                     </a>
                 <?php endforeach; ?>
             </div>
        </div>

        <!-- Área de Conversa -->
        <div class="flex-1 flex flex-col h-full bg-[#e5ddd5] relative <?php echo !$chatAtivo ? 'hidden md:flex' : 'flex'; ?>">
            <?php if ($chatAtivo && $moradorAtivo): ?>
                
                <!-- Header da Conversa -->
                <header class="bg-white border-b border-gray-200 p-3 sm:p-4 flex items-center gap-4 shadow-sm z-10">
                    <a href="chat.php" class="md:hidden text-gray-500 hover:text-indigo-600"><i class="fa-solid fa-arrow-left text-lg"></i></a>
                    <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold shadow-sm">
                        <?php echo substr($moradorAtivo['nome'], 0, 1); ?>
                    </div>
                    <div>
                        <h2 class="font-bold text-gray-800 text-sm sm:text-base"><?php echo htmlspecialchars($moradorAtivo['nome']); ?></h2>
                        <p class="text-xs text-gray-500">Apto/Lote: <?php echo htmlspecialchars($moradorAtivo['unidade']); ?></p>
                    </div>
                </header>

                <!-- Mensagens -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-6 chat-scroll" id="chatContainer">
                    <div class="space-y-4 max-w-4xl mx-auto">
                        <div class="text-center font-bold text-xs text-gray-400 bg-white/60 py-1 px-3 rounded-full w-max mx-auto shadow-sm backdrop-blur-sm">
                            Este é o início da conversa com a Unidade <?php echo htmlspecialchars($moradorAtivo['unidade']); ?>
                        </div>
                        
                        <?php foreach($mensagens as $msg): ?>
                            <?php $isMyMessage = ($msg['remetente_id'] == $sindicoId); ?>
                            
                            <div class="flex w-full <?php echo $isMyMessage ? 'justify-end' : 'justify-start'; ?>">
                                <div class="max-w-[85%] sm:max-w-[75%] rounded-2xl p-3 sm:p-4 relative shadow-sm <?php echo $isMyMessage ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-white text-gray-800 rounded-tl-none'; ?>">
                                    <p class="text-sm leading-relaxed whitespace-pre-wrap"><?php echo htmlspecialchars($msg['mensagem']); ?></p>
                                    <div class="text-[10px] sm:text-xs text-right mt-1.5 opacity-80 flex items-center justify-end gap-1">
                                        <?php echo date('H:i', strtotime($msg['data_envio'])); ?>
                                        <?php if($isMyMessage): ?>
                                            <i class="fa-solid fa-check-double <?php echo $msg['lida'] ? 'text-blue-300' : 'text-gray-300'; ?>"></i>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Input Zone -->
                <div class="bg-gray-100 p-3 sm:p-4 border-t border-gray-300 z-10">
                    <form method="POST" action="" class="max-w-4xl mx-auto flex items-end gap-2 sm:gap-3 bg-white rounded-2xl p-1 shadow-sm border border-gray-300 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 transition-all">
                        <input type="hidden" name="acao" value="enviar_msg">
                        <input type="hidden" name="receptor_id" value="<?php echo $moradorAtivo['id']; ?>">
                        
                        <textarea name="mensagem" required placeholder="Digite uma mensagem para o morador..." class="flex-1 bg-transparent border-0 focus:ring-0 p-3 resize-none max-h-32 min-h-[44px] text-sm sm:text-base outline-none chat-scroll" rows="1" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
                        
                        <button type="submit" class="bg-indigo-600 text-white w-10 h-10 sm:w-11 sm:h-11 rounded-xl flex items-center justify-center hover:bg-indigo-700 transition shrink-0 m-1">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
                
                <!-- Script para rolar para a última mensagem -->
                <script>
                    var chatContainer = document.getElementById("chatContainer");
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                </script>

            <?php else: ?>
                <!-- Estado Vazio -->
                <div class="w-full h-full flex flex-col items-center justify-center text-center p-8 bg-gray-50 bg-opacity-90">
                    <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-200 mb-6 text-gray-300">
                        <i class="fa-regular fa-comments text-5xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Central de Mensagens</h2>
                    <p class="text-gray-500 max-w-md">Selecione um morador na barra lateral para iniciar uma conversa direta e privativa.</p>
                </div>
            <?php endif; ?>
        </div>

    </main>
</body>
</html>