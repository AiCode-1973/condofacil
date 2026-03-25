<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CondoFácil - Login do Sistema</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 md:p-10 rounded-xl shadow-lg w-full max-w-md border-t-4 border-green-600">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-green-700 flex justify-center items-center gap-2">
                <i class="fa-solid fa-building"></i> CondoFácil
            </h2>
            <p class="text-gray-500 mt-2">Acesso ao Painel do SaaS</p>
        </div>

        <?php 
        session_start();
        if(isset($_SESSION['erro_login'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $_SESSION['erro_login']; ?></span>
            </div>
        <?php 
            unset($_SESSION['erro_login']);
        endif; 
        ?>

        <form action="auth.php" method="POST" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-regular fa-envelope text-gray-400"></i>
                    </div>
                    <input type="email" name="email" id="email" required 
                        class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" 
                        placeholder="seu@email.com">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="senha" class="block text-sm font-medium text-gray-700">Senha</label>
                    <a href="#" class="text-xs text-green-600 hover:text-green-800 transition">Esqueceu a senha?</a>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-lock text-gray-400"></i>
                    </div>
                    <input type="password" name="senha" id="senha" required 
                        class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" 
                        placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition-transform transform hover:-translate-y-1 shadow-md">
                Entrar
            </button>
        </form>
    </div>

</body>
</html>