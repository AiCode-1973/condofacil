<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_acesso'] !== 'morador') {
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$condominioId = $_SESSION['condominio_id'];
$moradorId = $_SESSION['usuario_id']; // morador logado

// 1. Obter o Síndico (ou Síndicos, se houver mais de um, simplificamos pegando o primeiro)
$stmt = $pdo->prepare("SELECT id, nome FROM usuarios WHERE condomio_id = ? AND tipo_acesso = 'admin' LIMIT 1");
$stmt->execute([$condominioId]);
$sindico = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sindico) {
    die("Nenhum síndico cadastrado no condomínio para chat.");
}
$sindicoId = $sindico['id'];

// 2. Processar envio de mensagem do morador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'enviar_msg') {
    $mensagem = trim($_POST['mensagem']);
    
    if (!empty($mensagem)) {
        $stmt = $pdo->prepare("INSERT INTO mensagens_chat (condominio_id, remetente_id, receptor_id, mensagem, lida) VALUES (?, ?, ?, ?, 0)");
        $stmt->execute([$condominioId, $moradorId, $sindicoId, $mensagem]);
    }
    header("Location: chat.php");
    exit;
}

// 3. Marca as mensagens RECEBIDAS do síndico como lidas
$stmt = $pdo->prepare("UPDATE mensagens_chat SET lida = 1 WHERE remetente_id = ? AND receptor_id = ?");
$stmt->execute([$sindicoId, $moradorId]);

// 4. Carregar a conversa (Síndico para Morador OR Morador para Síndico)
$stmt = $pdo->prepare("SELECT * FROM mensagens_chat WHERE (remetente_id = ? AND receptor_id = ?) OR (remetente_id = ? AND receptor_id = ?) ORDER BY data_envio ASC");
$stmt->execute([$sindicoId, $moradorId, $moradorId, $sindicoId]);
$mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens Diretas - CondoFácil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .chat-scroll::-webkit-scrollbar { width: 4px; }
        .chat-scroll::-webkit-scrollbar-track { background: transparent; }
        .chat-scroll::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 4px; }
        
        /* Tema Claro/Moderno */
        body { background-color: #fafafa; }
        .bg-chat { background-color: #F0F2F5; }
        
        /* Estilos Balões */
        .msg-remetente { background-color: #d1fae5; color: #065f46; border-radius: 20px 20px 4px 20px; }
        .msg-destinatario { background-color: #ffffff; color: #1e293b; border-radius: 20px 20px 20px 4px; border: 1px solid #e2e8f0; }
    </style>
</head>
<body class="flex flex-col h-screen font-sans">
    
    <!-- Topbar Mobile -->
    <header class="bg-indigo-700 text-white shadow-md z-20 flex items-center px-4 py-3 shrink-0">
        <a href="index.php" class="mr-4 p-2 hover:bg-indigo-600 rounded-full transition"><i class="fa-solid fa-arrow-left"></i></a>
        <div class="flex items-center gap-3 flex-1">
            <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center font-bold shadow-sm">
                S
            </div>
            <div>
                <h1 class="text-lg font-bold leading-tight">Administração</h1>
                <p class="text-xs text-indigo-200">Síndico <?php echo htmlspecialchars($sindico['nome']); ?></p>
            </div>
        </div>
        <button id="menu-btn" class="p-2 hover:bg-indigo-600 rounded-md transition"><i class="fa-solid fa-ellipsis-vertical text-xl"></i></button>
    </header>

    <!-- Overlay/Menu de opções (oculto por padrão) -->
    <div id="mobile-menu" class="hidden absolute top-14 right-4 bg-white shadow-xl rounded-lg py-2 w-48 border border-gray-100 z-50 transition-all origin-top-right">
        <a href="index.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50"><i class="fa-solid fa-house mr-2 w-4"></i> Início</a>
        <a href="avisos.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50"><i class="fa-solid fa-bullhorn mr-2 w-4"></i> Mural</a>
        <div class="border-t my-1"></div>
        <a href="../logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50"><i class="fa-solid fa-right-from-bracket mr-2 w-4"></i> Sair</a>
    </div>

    <!-- Área Principal de Mensagens -->
    <main class="flex-1 overflow-y-auto bg-chat chat-scroll relative p-3 pb-24" id="chatArea">
        <div class="max-w-2xl mx-auto space-y-4 pt-4">
            
            <div class="text-center mb-8">
                <span class="bg-[#dcf8c6] text-xs px-3 py-1 rounded-full text-green-800 shadow-sm border border-green-200 font-medium">As mensagens são end-to-end simuladas para o condomínio. As respostas do síndico podem demorar durante horário comercial.</span>
            </div>

            <?php if(empty($mensagens)): ?>
                <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                    <i class="fa-regular fa-paper-plane text-5xl mb-3 opacity-50"></i>
                    <p class="text-sm">Envie uma mensagem para a administração do seu condomínio</p>
                </div>
            <?php else: ?>
                <?php foreach($mensagens as $msg): ?>
                    <?php $souEu = ($msg['remetente_id'] == $moradorId); ?>
                    
                    <div class="flex <?php echo $souEu ? 'justify-end' : 'justify-start'; ?> group mb-2">
                        <div class="max-w-[85%] sm:max-w-[70%] relative shadow-sm <?php echo $souEu ? 'msg-remetente' : 'msg-destinatario'; ?> p-3.5 flex flex-col gap-1 transition-all">
                            
                            <p class="text-[15px] leading-snug whitespace-pre-wrap break-words"><?php echo htmlspecialchars($msg['mensagem']); ?></p>
                            
                            <!-- Metadados da Mensagem -->
                            <div class="flex items-center gap-1.5 self-end text-[11px] opacity-70 mt-1">
                                <span><?php echo date('H:i', strtotime($msg['data_envio'])); ?></span>
                                <?php if($souEu): ?>
                                    <i class="fa-solid fa-check-double <?php echo $msg['lida'] ? 'text-blue-500 opacity-100' : 'text-gray-400'; ?>"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Campo de Digitação Flexível Fixo (WhatsApp Style) -->
    <div class="fixed bottom-0 left-0 w-full bg-[#f0f2f5] px-2 py-2 sm:px-4 sm:py-3 border-t border-gray-200 z-30">
        <form method="POST" action="" class="max-w-2xl mx-auto flex items-end gap-2 bg-white rounded-3xl p-1.5 shadow-sm border border-gray-300 focus-within:ring-2 focus-within:ring-indigo-400 focus-within:border-indigo-400 transition-all pr-2">
            
            <button type="button" class="text-gray-400 hover:text-indigo-600 p-2 sm:p-2.5 rounded-full mb-0.5 shrink-0 transition" aria-label="Anexos" disabled>
                <i class="fa-solid fa-paperclip text-lg"></i>
            </button>
            
            <input type="hidden" name="acao" value="enviar_msg">
            
            <textarea name="mensagem" id="msgInput" required placeholder="Digite sua mensagem ao síndico..." class="flex-1 bg-transparent border-0 focus:ring-0 text-sm sm:text-[15px] resize-none overflow-y-auto w-full outline-none my-auto py-1.5 sm:py-2 px-1 text-gray-800 chat-scroll max-h-32 min-h-[40px]" rows="1" oninput="ajustarAltura(this)"></textarea>
            
            <button type="submit" class="bg-indigo-600 text-white w-10 h-10 sm:w-11 sm:h-11 rounded-full flex items-center justify-center hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition shadow-sm shrink-0 mb-0.5" aria-label="Enviar">
                <i class="fa-solid fa-paper-plane text-sm ml-0.5"></i>
            </button>
        </form>
    </div>

    <!-- Scripts JavaScript Essenciais -->
    <script>
        // Auto-scroll the chat
        const chatArea = document.getElementById("chatArea");
        chatArea.scrollTop = chatArea.scrollHeight;
        
        // Auto-resize the textarea
        function ajustarAltura(el) {
            el.style.height = 'auto'; // Reseta altura
            el.style.height = (el.scrollHeight) + 'px'; // Define altura baseada no scroll height
            
            // Garantir que a área de chat não seja sobreposta se o textarea crescer
            const inputContainer = el.closest('div.fixed');
            const newPaddingBottom = inputContainer.offsetHeight + 10;
            chatArea.style.paddingBottom = newPaddingBottom + 'px';
            chatArea.scrollTop = chatArea.scrollHeight;
        }

        // Dropdown do header
        const btn = document.getElementById('menu-btn');
        const menu = document.getElementById('mobile-menu');
        
        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
        
        // Fecha o menu ao clicar fora dele
        window.addEventListener('click', (e) => {
            if (!btn.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
        
        // Permite envio via "Enter" no teclado, mas Shift+Enter fará nova linha.  
        document.getElementById('msgInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                // Em dispositivos móveis, manter o enter padrão é melhor, mas para desktop forçamos o post
                if(window.innerWidth > 768) {
                    e.preventDefault(); 
                    if(this.value.trim() !== '') {
                        this.closest('form').submit();
                    }
                }
            }
        });
    </script>
</body>
</html>