<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CondoFácil – O sistema completo para administração de condomínios</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb', // Blue-600
                        secondary: '#1e40af', // Blue-800
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        html { scroll-behavior: smooth; }
        /* Transitions for FAQ */
        details > summary {
            list-style: none;
        }
        details > summary::-webkit-details-marker {
            display: none;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">

    <!-- Navbar -->
    <nav class="bg-white shadow-sm fixed w-full z-10 top-0 left-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0 flex items-center">
                    <span class="text-2xl font-bold text-primary"><i class="fa-solid fa-building text-secondary mr-2"></i>CondoFácil</span>
                </div>
                <div>
                    <a href="#planos" class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-md font-medium transition">Teste Grátis</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- 1. Hero (topo da página) -->
    <section class="pt-32 pb-20 bg-gradient-to-br from-blue-50 to-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight mb-4">
                CondoFácil – O sistema completo para <span class="text-primary">administração de condomínios</span>
            </h1>
            <p class="mt-4 text-xl text-slate-600 max-w-3xl mx-auto mb-8">
                Centralize reservas de áreas, avisos, ocorrências, regras e chat com a administração em um único lugar, simples e fácil de usar.
            </p>
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4 mb-8">
                <a href="#planos" class="w-full sm:w-auto bg-primary hover:bg-secondary text-white px-8 py-4 rounded-lg text-lg font-semibold shadow-lg transition transform hover:-translate-y-1">
                    Teste grátis o CondoFácil
                </a>
                <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                    <a href="#demo" class="w-full sm:w-auto text-primary hover:text-secondary font-medium px-4 py-3 sm:py-2 flex justify-center items-center gap-2 border border-primary sm:border-transparent rounded-lg sm:rounded-none">
                        <i class="fa-solid fa-circle-play"></i> Ver demo em vídeo
                    </a>
                    <a href="#planos" class="w-full sm:w-auto text-slate-600 hover:text-slate-900 font-medium px-4 py-3 sm:py-2 text-center bg-slate-100 sm:bg-transparent rounded-lg sm:rounded-none">
                        Falar com o suporte
                    </a>
                </div>
            </div>
            
            <div class="flex flex-col md:flex-row justify-center items-center gap-4 md:gap-6 text-sm text-slate-500 font-medium text-left md:text-center w-full max-w-md md:max-w-none mx-auto">
                <div class="flex items-center gap-2 w-full md:w-auto"><i class="fa-solid fa-check text-green-500 flex-shrink-0"></i> Acesso para síndicos, administradoras e moradores</div>
                <div class="flex items-center gap-2 w-full md:w-auto"><i class="fa-solid fa-check text-green-500 flex-shrink-0"></i> 100% online, modelo SaaS</div>
                <div class="flex items-center gap-2 w-full md:w-auto"><i class="fa-solid fa-check text-green-500 flex-shrink-0"></i> Sem instalação, funciona direto no navegador</div>
            </div>
        </div>
    </section>

    <!-- 2. Seção “Para quem é o CondoFácil” -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-slate-900">Feito para síndicos, administradoras e condomínios que precisam de organização</h2>
                <p class="mt-4 text-lg text-slate-600 max-w-3xl mx-auto">
                    O CondoFácil foi criado para simplificar o dia a dia de condomínios residenciais e comerciais. Em vez de depender de planilhas, grupos de WhatsApp e avisos no elevador, você concentra tudo em uma plataforma única, acessível de qualquer lugar.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                <div class="bg-slate-50 p-8 rounded-xl border border-slate-100 shadow-sm text-center">
                    <div class="w-16 h-16 bg-blue-100 text-primary rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Síndicos</h3>
                    <p class="text-slate-600">Que querem reduzir conflitos, evitar o WhatsApp pessoal e ter tudo registrado formalmente.</p>
                </div>
                <div class="bg-slate-50 p-8 rounded-xl border border-slate-100 shadow-sm text-center">
                    <div class="w-16 h-16 bg-blue-100 text-primary rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fa-solid fa-city"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Administradoras</h3>
                    <p class="text-slate-600">Que precisam padronizar a gestão de vários condomínios e facilitar o atendimento em uma só tela.</p>
                </div>
                <div class="bg-slate-50 p-8 rounded-xl border border-slate-100 shadow-sm text-center">
                    <div class="w-16 h-16 bg-blue-100 text-primary rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Condomínios</h3>
                    <p class="text-slate-600">Que querem profissionalizar a comunicação com os moradores e garantir transparência.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Seção “Funcionalidades principais” -->
    <section class="py-20 bg-slate-50" id="funcionalidades">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-slate-900">Tudo o que o seu condomínio precisa em um só sistema</h2>
                <p class="mt-4 text-lg text-slate-600">O CondoFácil reduz ruídos de comunicação, evita conflitos e dá transparência para a gestão do condomínio.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200 hover:shadow-md transition">
                    <div class="text-primary text-3xl mb-4"><i class="fa-regular fa-calendar-check"></i></div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Reserva de áreas comuns</h3>
                    <p class="text-slate-600">Agenda de salão de festas, churrasqueira e academia com controle de horários, regras e aprovação pela administração.</p>
                </div>
                <!-- Card 2 -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200 hover:shadow-md transition">
                    <div class="text-primary text-3xl mb-4"><i class="fa-solid fa-bullhorn"></i></div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Mural de avisos digital</h3>
                    <p class="text-slate-600">Publique comunicados oficiais, avisos importantes e informativos para todos os moradores em poucos cliques.</p>
                </div>
                <!-- Card 3 -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200 hover:shadow-md transition">
                    <div class="text-primary text-3xl mb-4"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Registro de ocorrências</h3>
                    <p class="text-slate-600">Receba e acompanhe reclamações, sugestões e incidentes com histórico organizado e respostas da administração.</p>
                </div>
                <!-- Card 4 -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200 hover:shadow-md transition">
                    <div class="text-primary text-3xl mb-4"><i class="fa-solid fa-file-contract"></i></div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Regras e documentos</h3>
                    <p class="text-slate-600">Centralize regulamentos internos, convenções e comunicados em um local único, sempre disponível para consulta.</p>
                </div>
                <!-- Card 5 -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200 hover:shadow-md transition">
                    <div class="text-primary text-3xl mb-4"><i class="fa-regular fa-comments"></i></div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Chat com a administração</h3>
                    <p class="text-slate-600">Canal direto entre moradores, síndico e administradora, substituindo conversas desencontradas em vários apps.</p>
                </div>
                <!-- Card 6 -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200 hover:shadow-md transition">
                    <div class="text-primary text-3xl mb-4"><i class="fa-solid fa-cloud"></i></div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Acesso web (SaaS)</h3>
                    <p class="text-slate-600">Sistema em nuvem, sem necessidade de instalação. Basta login e senha para começar a usar de qualquer dispositivo.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Seção “Benefícios” -->
    <section class="py-20 bg-primary text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold">Benefícios reais para o dia a dia do condomínio</h2>
                <p class="mt-4 text-xl text-blue-100">Mais organização, menos confusão, todo mundo sabendo o que está acontecendo.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mt-12">
                <div class="bg-secondary bg-opacity-50 p-8 rounded-xl">
                    <h3 class="text-2xl font-bold mb-6 flex items-center gap-3"><i class="fa-solid fa-briefcase"></i>Para o Síndico / Administradora</h3>
                    <ul class="space-y-4 text-blue-50 text-lg">
                        <li class="flex items-start gap-3"><i class="fa-solid fa-check-circle mt-1 text-blue-300"></i> Menos ligações e mensagens soltas no WhatsApp pessoal</li>
                        <li class="flex items-start gap-3"><i class="fa-solid fa-check-circle mt-1 text-blue-300"></i> Histórico organizado e formal de tudo o que acontece no condomínio</li>
                        <li class="flex items-start gap-3"><i class="fa-solid fa-check-circle mt-1 text-blue-300"></i> Facilidade e transparência para prestar contas aos moradores</li>
                    </ul>
                </div>
                <div class="bg-secondary bg-opacity-50 p-8 rounded-xl">
                    <h3 class="text-2xl font-bold mb-6 flex items-center gap-3"><i class="fa-solid fa-house-chimney-user"></i>Para os Moradores</h3>
                    <ul class="space-y-4 text-blue-50 text-lg">
                        <li class="flex items-start gap-3"><i class="fa-solid fa-check-circle mt-1 text-blue-300"></i> Comunicação clara e oficial com a administração</li>
                        <li class="flex items-start gap-3"><i class="fa-solid fa-check-circle mt-1 text-blue-300"></i> Facilidade e autonomia para reservar áreas comuns pelo celular ou PC</li>
                        <li class="flex items-start gap-3"><i class="fa-solid fa-check-circle mt-1 text-blue-300"></i> Acesso rápido às regras do condomínio e aos avisos importantes</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Seção “Como funciona na prática” -->
    <section class="py-20 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-slate-900">Como o CondoFácil funciona em 3 passos</h2>
                <p class="mt-4 text-lg text-slate-600">A partir daí, reservas, avisos, ocorrências e chats passam a ficar centralizados dentro do CondoFácil.</p>
            </div>

            <div class="flex flex-col md:flex-row justify-center items-center md:items-start gap-12 md:gap-8 relative">
                <!-- Step 1 -->
                <div class="flex-1 text-center relative z-10 bg-white pt-6 w-full max-w-sm">
                    <div class="w-16 h-16 bg-primary text-white font-bold text-2xl rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-white shadow-md">1</div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Cadastre seu condomínio</h3>
                    <p class="text-slate-600">Informe os dados básicos, cadastre blocos, unidades e usuários rapidamente.</p>
                </div>
                <!-- Step 2 -->
                <div class="flex-1 text-center relative z-10 bg-white pt-6 w-full max-w-sm">
                    <div class="w-16 h-16 bg-primary text-white font-bold text-2xl rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-white shadow-md">2</div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Configure áreas e regras</h3>
                    <p class="text-slate-600">Defina horários, limites e regras de uso das áreas compartilhadas para os moradores.</p>
                </div>
                <!-- Step 3 -->
                <div class="flex-1 text-center relative z-10 bg-white pt-6 w-full max-w-sm">
                    <div class="w-16 h-16 bg-primary text-white font-bold text-2xl rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-white shadow-md">3</div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Convide síndicos e moradores</h3>
                    <p class="text-slate-600">Cada um recebe acesso personalizado e começa a usar o sistema no dia a dia.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 6 & 7. Seção “Modelo SaaS e acesso” & "Prova social" -->
    <section class="py-20 bg-slate-50 border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 mb-4">100% online, sempre atualizado</h2>
                    <p class="text-lg text-slate-600 mb-6">
                        O CondoFácil é um sistema SaaS (Software como Serviço): você acessa pelo navegador, sem precisar instalar nada no computador.
                        Todas as atualizações de melhoria e correções são feitas automaticamente, sem interrupção para o condomínio.
                    </p>
                    <ul class="space-y-2 text-slate-700 font-medium">
                        <li><i class="fa-solid fa-lock text-primary mr-2"></i> Acesso seguro por login e senha</li>
                        <li><i class="fa-solid fa-sliders text-primary mr-2"></i> Diferentes níveis de permissão (síndico, administradora, morador)</li>
                    </ul>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 mb-4">Transparência e organização na gestão</h2>
                    <p class="text-lg text-slate-600 mb-6">
                        O CondoFácil foi pensado para dar mais segurança, rastreabilidade e clareza a tudo o que acontece no condomínio. Comunicação, reservas e ocorrências deixam de ser algo solto e passam a ser registrados em um sistema único.
                    </p>
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-100 italic text-slate-600">
                        "O sistema ideal que estávamos procurando para tirar os problemas do WhatsApp e trazer profissionalismo para nossa gestão."
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. Seção “Planos / Chamada para teste” -->
    <section id="planos" class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-slate-900">Comece hoje mesmo a organizar o seu condomínio</h2>
                <p class="mt-4 text-lg text-slate-600">Entre em contato para conhecer os planos do CondoFácil e ver como o sistema pode se adaptar à realidade do seu condomínio.</p>
            </div>

            <!-- Formulário PHP + Contato -->
            <div class="bg-white p-8 rounded-xl shadow-lg border border-slate-100">
                <?php if (isset($_GET['sucesso'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 text-center">
                        Sua mensagem foi enviada com sucesso! Entraremos em contato em breve.
                    </div>
                <?php endif; ?>

                <form action="processa_contato.php" method="POST" class="space-y-4 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nome completo</label>
                            <input type="text" name="nome" required class="w-full px-4 py-2 border border-slate-300 rounded-md focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">E-mail corporativo / pessoal</label>
                            <input type="email" name="email" required class="w-full px-4 py-2 border border-slate-300 rounded-md focus:ring-primary focus:border-primary">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Telefone / WhatsApp</label>
                            <input type="tel" name="telefone" required class="w-full px-4 py-2 border border-slate-300 rounded-md focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tipo de condomínio</label>
                            <select name="tipo_condominio" class="w-full px-4 py-2 border border-slate-300 rounded-md focus:ring-primary focus:border-primary">
                                <option value="Residencial">Residencial</option>
                                <option value="Comercial">Comercial</option>
                                <option value="Misto">Misto</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Mensagem (Opcional)</label>
                        <textarea name="mensagem" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-md focus:ring-primary focus:border-primary" placeholder="Quantas unidades? Alguma dúvida específica?"></textarea>
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="w-full bg-primary hover:bg-secondary text-white font-bold py-3 px-4 rounded-md transition text-lg shadow-md">
                            Quero testar o CondoFácil
                        </button>
                    </div>
                </form>

                <div class="relative flex py-5 items-center">
                    <div class="flex-grow border-t border-slate-300"></div>
                    <span class="flex-shrink-0 mx-4 text-slate-400">ou fale agora mesmo</span>
                    <div class="flex-grow border-t border-slate-300"></div>
                </div>

                <div class="text-center">
                    <a href="https://wa.me/5511999999999?text=Olá,%20gostaria%20de%20solicitar%20uma%20demonstração%20do%20CondoFácil" target="_blank" class="inline-flex justify-center items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-md transition text-lg shadow-md">
                        <i class="fa-brands fa-whatsapp text-2xl"></i> Solicitar uma demonstração pelo WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 9. Seção “FAQ – Perguntas frequentes” -->
    <section class="py-20 bg-slate-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-slate-900 text-center mb-10">Perguntas frequentes</h2>
            
            <div class="space-y-4">
                <!-- FAQ Item 1 -->
                <details class="group bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden" open>
                    <summary class="flex justify-between items-center font-medium cursor-pointer list-none p-5 text-lg text-slate-900 hover:bg-slate-50 transition">
                        <span>O CondoFácil precisa ser instalado?</span>
                        <span class="transition group-open:rotate-180">
                            <i class="fa-solid fa-chevron-down text-primary"></i>
                        </span>
                    </summary>
                    <div class="p-5 pt-0 text-slate-600 border-t border-slate-100 mt-2">
                        Não. O CondoFácil é 100% online (SaaS). Basta acessar pelo navegador (no computador ou celular) com seu login e senha, sem precisar baixar nada.
                    </div>
                </details>

                <!-- FAQ Item 2 -->
                <details class="group bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                    <summary class="flex justify-between items-center font-medium cursor-pointer list-none p-5 text-lg text-slate-900 hover:bg-slate-50 transition">
                        <span>Quem pode usar o sistema?</span>
                        <span class="transition group-open:rotate-180">
                            <i class="fa-solid fa-chevron-down text-primary"></i>
                        </span>
                    </summary>
                    <div class="p-5 pt-0 text-slate-600 border-t border-slate-100 mt-2">
                        Síndicos, administradoras e moradores, todos com diferentes níveis de acesso definidos pela administração para garantir segurança e organização.
                    </div>
                </details>

                <!-- FAQ Item 3 -->
                <details class="group bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                    <summary class="flex justify-between items-center font-medium cursor-pointer list-none p-5 text-lg text-slate-900 hover:bg-slate-50 transition">
                        <span>O sistema funciona para mais de um condomínio?</span>
                        <span class="transition group-open:rotate-180">
                            <i class="fa-solid fa-chevron-down text-primary"></i>
                        </span>
                    </summary>
                    <div class="p-5 pt-0 text-slate-600 border-t border-slate-100 mt-2">
                        Sim, é perfeitamente possível cadastrar e administrar vários condomínios na mesma conta (ideal para administradoras e síndicos profissionais).
                    </div>
                </details>

                <!-- FAQ Item 4 -->
                <details class="group bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                    <summary class="flex justify-between items-center font-medium cursor-pointer list-none p-5 text-lg text-slate-900 hover:bg-slate-50 transition">
                        <span>Os dados do condomínio ficam seguros?</span>
                        <span class="transition group-open:rotate-180">
                            <i class="fa-solid fa-chevron-down text-primary"></i>
                        </span>
                    </summary>
                    <div class="p-5 pt-0 text-slate-600 border-t border-slate-100 mt-2">
                        Sim! Os dados são armazenados em ambiente em nuvem seguro, com backups automáticos, e só são acessíveis por usuários autorizados do próprio condomínio.
                    </div>
                </details>
            </div>
        </div>
    </section>

    <!-- 10. Rodapé -->
    <footer class="bg-slate-900 text-slate-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8 border-b border-slate-800 pb-8">
                <!-- Col 1 -->
                <div>
                    <span class="text-2xl font-bold text-white flex items-center mb-4"><i class="fa-solid fa-building text-primary mr-2"></i>CondoFácil</span>
                    <p class="text-sm text-slate-400">
                        CondoFácil é um sistema online para gestão e comunicação de condomínios. Reservas, avisos, ocorrências, regras e chat em um só lugar.
                    </p>
                </div>
                <!-- Col 2 -->
                <div>
                    <h3 class="text-white font-semibold mb-4 text-lg">Menu</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#funcionalidades" class="hover:text-white transition">Funcionalidades</a></li>
                        <li><a href="#planos" class="hover:text-white transition">Planos</a></li>
                        <li><a href="#planos" class="hover:text-white transition">Contato</a></li>
                    </ul>
                </div>
                <!-- Col 3 -->
                <div>
                    <h3 class="text-white font-semibold mb-4 text-lg">Legal</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Termos de Uso</a></li>
                        <li><a href="#" class="hover:text-white transition">Política de Privacidade</a></li>
                    </ul>
                </div>
            </div>
            <div class="flex flex-col md:flex-row justify-between items-center text-sm text-slate-500">
                <p>&copy; <?php echo date('Y'); ?> CondoFácil. Todos os direitos reservados.</p>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="hover:text-white transition"><i class="fa-brands fa-instagram text-xl"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="fa-brands fa-linkedin text-xl"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="fa-brands fa-facebook text-xl"></i></a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>