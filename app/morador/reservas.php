<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_acesso'] !== 'morador') {
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$condominioId = $_SESSION['condominio_id'];
$usuarioId = $_SESSION['usuario_id'];
$condominioNome = $pdo->prepare("SELECT nome FROM condominios WHERE id = ?");
$condominioNome->execute([$condominioId]);
$condominioNome = $condominioNome->fetchColumn() ?: "Meu Condomínio";

$mensagemSucesso = '';
$mensagemErro = '';

// Processar Nova Reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    if ($_POST['acao'] === 'reservar') {
        $area_id = $_POST['area_id'];
        $data_reserva = $_POST['data_reserva'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fim = $_POST['hora_fim'];

        // Regra simples: impedir data passada
        if (strtotime($data_reserva) < strtotime(date('Y-m-d'))) {
             $mensagemErro = "Você não pode reservar em uma data que já passou.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO reservas (condominio_id, usuario_id, area_id, data_reserva, horario_inicio, horario_fim, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pendente', NOW())");
            if ($stmt->execute([$condominioId, $usuarioId, $area_id, $data_reserva, $hora_inicio, $hora_fim])) {
                $mensagemSucesso = "Solicitação de reserva enviada! Aguardando aprovação do síndico.";
            } else {
                $mensagemErro = "Erro ao solicitar reserva.";
            }
        }
    } elseif ($_POST['acao'] === 'cancelar') {
        $reserva_id = $_POST['reserva_id'];
        $stmt = $pdo->prepare("UPDATE reservas SET status = 'cancelada' WHERE id = ? AND usuario_id = ? AND status = 'pendente'");
        if ($stmt->execute([$reserva_id, $usuarioId])) {
            $mensagemSucesso = "Reserva pendente cancelada.";
        }
    }
}

// Buscar áreas para o formulário
$stmt = $pdo->prepare("SELECT * FROM areas_comuns WHERE condominio_id = ? AND status = 'ativo'");
$stmt->execute([$condominioId]);
$areas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar histórico de reservas do morador
$sql = "SELECT r.*, a.nome as area_nome FROM reservas r JOIN areas_comuns a ON r.area_id = a.id WHERE r.usuario_id = ? ORDER BY r.data_reserva DESC, r.horario_inicio ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$usuarioId]);
$minhas_reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

function badgeStatus($status) {
    switch($status) {
        case 'pendente': return '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full border border-yellow-200">Aguardando Avaliação</span>';
        case 'aprovada': return '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full border border-green-200">Aprovada</span>';
        case 'rejeitada': return '<span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full border border-red-200">Rejeitada</span>';
        case 'cancelada': return '<span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-full border border-gray-200">Você Cancelou</span>';
        default: return '<span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded">'.$status.'</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minhas Reservas - CondoFácil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        function toggleMobileMenu() {
            var menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
</head>
<body class="bg-slate-50 font-sans min-h-screen flex flex-col md:flex-row">

    <!-- Header Mobile -->
    <div class="md:hidden bg-green-700 text-white flex justify-between items-center p-4 shadow-md sticky top-0 z-50">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-calendar-check text-xl"></i>
            <span class="font-bold text-lg">Reservas</span>
        </div>
        <button onclick="toggleMobileMenu()" class="text-white hover:text-green-200 focus:outline-none p-1">
            <i class="fa-solid fa-bars text-2xl"></i>
        </button>
    </div>

    <!-- Sidebar / Menu -->
    <aside id="mobile-menu" class="hidden md:flex flex-col w-full md:w-64 bg-green-700 text-white shadow-xl h-auto md:h-screen sticky top-0 z-40 md:z-auto transition-all">
        <div class="p-5 border-b border-green-600 font-bold hidden md:flex flex-col items-center justify-center gap-2">
            <i class="fa-solid fa-house-chimney-window text-4xl text-green-300"></i>
            <span class="text-xs uppercase tracking-wider text-green-200 font-semibold">Painel Morador</span>
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
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 w-full overflow-y-auto">
        <header class="hidden md:flex bg-white shadow-sm px-8 py-5 border-b border-gray-200 justify-between items-center sticky top-0 z-30">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Reservar Áreas Comuns</h1>
                <p class="text-sm text-gray-500">Agende a churrasqueira, salão de festas ou quadra.</p>
            </div>
        </header>

        <div class="p-4 md:p-8 max-w-6xl mx-auto">
            <?php if ($mensagemSucesso): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 shadow-sm"><i class="fa-solid fa-check-circle mr-2"></i><?php echo $mensagemSucesso; ?></div>
            <?php endif; ?>
            <?php if ($mensagemErro): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 shadow-sm"><i class="fa-solid fa-circle-exclamation mr-2"></i><?php echo $mensagemErro; ?></div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
                
                <!-- Coluna de Solicitação -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 h-fit">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3 flex items-center gap-2">
                            <i class="fa-solid fa-calendar-plus text-green-600"></i> Fazer Solicitação
                        </h2>
                        <?php if (count($areas) > 0): ?>
                            <form method="POST">
                                <input type="hidden" name="acao" value="reservar">
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Área Desejada:</label>
                                    <select name="area_id" class="shadow-sm border rounded-lg w-full py-2.5 px-3 text-gray-700 bg-white" required>
                                        <option value="">Selecione um espaço...</option>
                                        <?php foreach ($areas as $area): ?>
                                            <option value="<?php echo $area['id']; ?>"><?php echo htmlspecialchars($area['nome']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Data da Reserva:</label>
                                    <input type="date" name="data_reserva" class="shadow-sm border rounded-lg w-full py-2.5 px-3 text-gray-700" min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Início:</label>
                                        <input type="time" name="hora_inicio" class="shadow-sm border rounded-lg w-full py-2.5 px-3 text-gray-700" required>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Término:</label>
                                        <input type="time" name="hora_fim" class="shadow-sm border rounded-lg w-full py-2.5 px-3 text-gray-700" required>
                                    </div>
                                </div>
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl transition shadow-md">
                                    Enviar Solicitação
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="text-center py-6">
                                <i class="fa-solid fa-triangle-exclamation text-yellow-500 text-3xl mb-2"></i>
                                <p class="text-gray-500 text-sm">O condomínio ainda não cadastrou nenhuma área comum disponível para reserva.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Histórico de Reservas -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-800"><i class="fa-solid fa-clock-rotate-left text-blue-600 mr-2"></i> Meu Histórico</h2>
                        </div>
                        <div class="p-6">
                            <?php if (count($minhas_reservas) > 0): ?>
                                <div class="space-y-4">
                                    <?php foreach ($minhas_reservas as $res): ?>
                                        <div class="border border-gray-100 rounded-xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between hover:shadow-md transition bg-gray-50/50">
                                            <div class="mb-3 sm:mb-0">
                                                <h3 class="font-bold text-gray-800 text-lg"><?php echo htmlspecialchars($res['area_nome']); ?></h3>
                                                <p class="text-gray-500 text-sm mt-1">
                                                    <i class="fa-regular fa-calendar mr-1"></i> Data: <strong class="text-gray-700"><?php echo date('d/m/Y', strtotime($res['data_reserva'])); ?></strong><br>
                                                    <i class="fa-regular fa-clock mr-1"></i> Horário: <strong class="text-gray-700"><?php echo substr($res['horario_inicio'], 0, 5); ?> às <?php echo substr($res['horario_fim'], 0, 5); ?></strong>
                                                </p>
                                            </div>
                                            <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-2">
                                                <?php echo badgeStatus($res['status']); ?>
                                                <?php if ($res['status'] === 'pendente'): ?>
                                                    <form method="POST" onsubmit="return confirm('Deseja desistir desta reserva?');">
                                                        <input type="hidden" name="acao" value="cancelar">
                                                        <input type="hidden" name="reserva_id" value="<?php echo $res['id']; ?>">
                                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-bold underline mt-1"><i class="fa-solid fa-xmark mr-1"></i> Cancelar</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-10">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fa-regular fa-folder-open text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">Você ainda não solicitou nenhuma reserva.</p>
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