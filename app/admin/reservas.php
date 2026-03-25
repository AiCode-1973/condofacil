<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_acesso'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$condominioId = $_SESSION['condominio_id'];
$mensagemSucesso = '';

// Atualizar Status da Reserva (Aprovar / Rejeitar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'atualizar_status') {
    $reservaId = $_POST['reserva_id'];
    $novoStatus = $_POST['status']; // 'aprovada' ou 'rejeitada'

    $stmt = $pdo->prepare("UPDATE reservas SET status = ? WHERE id = ? AND condominio_id = ?");
    if ($stmt->execute([$novoStatus, $reservaId, $condominioId])) {
        $mensagemSucesso = "Status da reserva atualizado com sucesso!";
    }
}

// Buscar todas as reservas (com JOIN para pegar nome da área e do morador)
$sql = "
    SELECT r.*, a.nome as area_nome, u.nome as morador_nome, u.unidade 
    FROM reservas r 
    JOIN areas_comuns a ON r.area_id = a.id 
    JOIN usuarios u ON r.usuario_id = u.id 
    WHERE r.condominio_id = ? 
    ORDER BY r.data_reserva DESC, r.horario_inicio ASC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$condominioId]);
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

function badgeStatus($status) {
    switch($status) {
        case 'pendente': return '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full">Pendente</span>';
        case 'aprovada': return '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full">Aprovada</span>';
        case 'rejeitada': return '<span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full">Rejeitada</span>';
        case 'cancelada': return '<span class="px-2 py-1 bg-gray-200 text-gray-800 text-xs font-bold rounded-full">Cancelada (Morador)</span>';
        default: return '<span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-full">'.$status.'</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Reservas - CondoFácil</title>
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
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto custom-scrollbar">
            <a href="index.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-slate-700' : 'hover:bg-slate-700' ?> rounded-lg transition">
                <i class="fa-solid fa-house w-5 text-center"></i> Início
            </a>
            <a href="moradores.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'moradores.php' ? 'bg-slate-700' : 'hover:bg-slate-700' ?> rounded-lg transition">
                <i class="fa-solid fa-users w-5 text-center"></i> Moradores
            </a>
            <a href="avisos.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'avisos.php' ? 'bg-slate-700' : 'hover:bg-slate-700' ?> rounded-lg transition">
                <i class="fa-solid fa-bullhorn w-5 text-center"></i> Avisos
            </a>
            <a href="areas_comuns.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'areas_comuns.php' ? 'bg-slate-700' : 'hover:bg-slate-700' ?> rounded-lg transition">
                <i class="fa-solid fa-swimming-pool w-5 text-center"></i> Áreas Comuns
            </a>
            <a href="reservas.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'reservas.php' ? 'bg-slate-700' : 'hover:bg-slate-700' ?> rounded-lg transition">
                <i class="fa-solid fa-calendar-check w-5 text-center"></i> Reservas Adm
            </a>
            <a href="ocorrencias.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'ocorrencias.php' ? 'bg-slate-700' : 'hover:bg-slate-700' ?> rounded-lg transition">
                <i class="fa-solid fa-clipboard-list w-5 text-center"></i> Ocorrências
            </a>
            <a href="regras.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'regras.php' ? 'bg-slate-700' : 'hover:bg-slate-700' ?> rounded-lg transition">
                <i class="fa-solid fa-scale-balanced w-5 text-center"></i> Regras
            </a>
            <a href="chat.php" class="flex items-center gap-3 p-3 <?= basename($_SERVER['PHP_SELF']) == 'chat.php' ? 'bg-slate-700' : 'hover:bg-slate-700' ?> rounded-lg transition">
                <i class="fa-solid fa-comments w-5 text-center"></i> Chat / Mensagens
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">
        <header class="bg-white shadow-sm px-8 py-5 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Solicitações de Reservas</h1>
        </header>

        <div class="p-8 max-w-6xl mx-auto">
            <?php if ($mensagemSucesso): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo $mensagemSucesso; ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Solicitante</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Área</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Data / Horário</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (count($reservas) > 0): ?>
                                <?php foreach ($reservas as $res): ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900"><?php echo htmlspecialchars($res['morador_nome']); ?></div>
                                            <div class="text-sm text-gray-500">Unidade: <?php echo htmlspecialchars($res['unidade']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($res['area_nome']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><i class="fa-regular fa-calendar text-gray-400 mr-1"></i> <?php echo date('d/m/Y', strtotime($res['data_reserva'])); ?></div>
                                            <div class="text-sm text-gray-500"><i class="fa-regular fa-clock text-gray-400 mr-1"></i> <?php echo substr($res['horario_inicio'], 0, 5); ?> às <?php echo substr($res['horario_fim'], 0, 5); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php echo badgeStatus($res['status']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <?php if ($res['status'] === 'pendente'): ?>
                                                <form method="POST" class="inline-block mr-2">
                                                    <input type="hidden" name="acao" value="atualizar_status">
                                                    <input type="hidden" name="reserva_id" value="<?php echo $res['id']; ?>">
                                                    <input type="hidden" name="status" value="aprovada">
                                                    <button type="submit" class="text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200 p-2 rounded-full transition" title="Aprovar">
                                                        <i class="fa-solid fa-check"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja rejeitar?');">
                                                    <input type="hidden" name="acao" value="atualizar_status">
                                                    <input type="hidden" name="reserva_id" value="<?php echo $res['id']; ?>">
                                                    <input type="hidden" name="status" value="rejeitada">
                                                    <button type="submit" class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 p-2 rounded-full transition" title="Rejeitar">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-gray-400 text-xs italic">Avaliado</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                        <i class="fa-solid fa-folder-open text-3xl mb-2 text-gray-300"></i><br>
                                        Nenhuma solicitação de reserva encontrada.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>