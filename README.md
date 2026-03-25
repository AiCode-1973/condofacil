# 🏢 CondoFácil - SaaS Web para Gestão Condominial

O **CondoFácil** é uma plataforma SaaS (Software as a Service) multi-tenant desenvolvida para facilitar a gestão e a comunicação em condomínios. Desenvolvida em PHP puro, a aplicação conecta Síndicos e Moradores em um ambiente centralizado, moderno e responsivo (Mobile-First para moradores).

---

## 🚀 Tecnologias Utilizadas
* **Linguagem Principal:** PHP 8+ (Procedural robusto orientado a Sessões)
* **Banco de Dados:** MySQL com `PDO` e prepared statements contra SQL Injection.
* **Frontend:** HTML5, Vanilla JavaScript.
* **Estilização UI/UX:** Tailwind CSS (via CDN).
* **Ícones:** FontAwesome 6+.
* **Arquitetura de Dados:** Multi-tenant lógico (todo os dados isolados e acessados mediante o `condominio_id` validado na sessão do PHP).

---

## ✨ Funcionalidades Implementadas

A plataforma possui um sistema forte de autorização e rotas divididas em três níveis: **Superadmin** (Dono da plataforma que gerencia diferentes condomínios), **Administrador/Síndico** (Gestor do próprio condomínio) e **Morador**.

### 1. 🔐 Core e Autenticação
* Landing page moderna de apresentação (`index.php`).
* Fluxo de Login único que faz o roteamento automático dependendo do `tipo_acesso` do usuário.
* Instalação automatizada do Banco de Dados via script utilitário (`app/instalar.php`).
* Senhas irrevogavelmente seguras tratadas com `password_hash()` nativo do PHP.

### 2. 📊 Dashboards Pessoais
* **Superadmin:** Visão da base SaaS, com estatísticas de condomínios utilizando a ferramenta.
* **Admin (Síndico):** Resumo ágil com quantidade de moradores, ocorrências em aberto, reservas a analisar e menu de módulos.
* **Moradores:** Interface App-like focado em mobile, com botões centrais rápidos e fluidos.

### 3. 📢 Mural de Avisos
* **Admin:** Gestor de publicação e exclusão de comunicados com "labels" visuais de nível de prioridade (Informativo, Alerta, Urgente).
* **Morador:** Timeline em estilo "feed" para leitura simples de avisos globais.

### 4. 📅 Reservas de Áreas Comuns
* **Admin:** 
  * Cadastro de espaços físicos específicos do condomínio (Piscina, Churrasqueira, Salão com limites de vagas).
  * Aprovação ou rejeição tática das reservas feitas.
* **Morador:** Calendário e formulário limpo para agendar dias e turnos em espaços, impossibilitando retroatividade (datas passadas).

### 5. 🛠️ Ocorrências e Helpdesk
* **Morador:** Abertura de "tickets" para comunicar lâmpadas queimadas, queixas ou falhas estruturais. Mantém histórico de todas submissões.
* **Admin:** Interface de atendimento ("Kanban/Helpdesk style") que permite visualizar demandas e alterar status (Pendente -> Em andamento -> Finalizado).

### 6. ⚖️ Regimento e Regras
* **Admin:** Editor amigável de artigos e regras de convivência.
* **Morador:** Documento estilo "Wiki" com motor nativo de **pesquisa ultra-rápida (Busca em Tempo Real via JS)** pelas regras inseridas.

### 7. 💬 Chat Privado (Mensagens Diretas)
* **Admin:** Painel dividido (Two-pane layout) para ler contatos listados da esquerda e responder rapidamente à direita no formato de balões.
* **Morador:** Layout totalmente imersivo como WhatsApp. Textarea fixo com auto-resize na base e visualização clara de mensagens lidas.

### 8. 👥 Gestão de Moradores
* **Admin:** Relatório de aptos/unidades. Modal que cria on-the-fly as credenciais de usuários e deleta de forma simplificada, limitando-se unicamente àquele bloco multi-tenant.

---

## 🔑 Logins de Teste

Utilize os seguintes acessos que já estão pré-configurados no banco de dados para explorar os diferentes níveis de permissão da plataforma:

**Acesso do Superadmin (Dono do SaaS)**
* E-mail: `super@condofacil.com`
* Senha: `senha123`

**Acesso do Síndico / Administração**
* E-mail: `admin@condominio.exemplo`
* Senha: `123456`

**Acesso do Morador Comum**
* E-mail: `morador.apto01@exemplo.com`
* Senha: `123456`

> **Nota:** Como os dados já foram inseridos, você pode começar os testes imediatamente usando as credenciais acima. Caso em algum momento queira zerar todo o banco de dados e as tabelas, basta rodar o script `app/instalar.php`.