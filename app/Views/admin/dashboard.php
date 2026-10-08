<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - GetNinjas</title>

    <!-- W3.CSS Core -->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <!-- Ícones do Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #fcfbfa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .tab-content-item {
            display: none;
        }

        /* Cores do tema customizado */
        .w3-ocre {
            background-color: #d8be92 !important;
            color: #3d3a37 !important;
        }

        .w3-hover-ocre:hover {
            background-color: #c9ad7f !important;
            color: #2b2826 !important;
        }

        .w3-dark-custom {
            background-color: #3d3a37 !important;
            color: #ffffff !important;
        }

        .w3-active-tab {
            background-color: #5c554e !important;
            color: #ffffff !important;
            font-weight: bold;
        }

        .w3-card-custom {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
        }
    </style>
</head>

<body>

    <!-- Header / Navbar Admin -->
    <?= view('components/navbar') ?>

    <div class="w3-container w3-padding-24" style="max-width: 1200px; margin: 0 auto; width: 100%;">

        <!-- Alertas de Feedback -->
        <?= view('components/alerts') ?>

        <!-- Métricas Rápidas -->
        <?= view('admin/components/cards_metricas') ?>

        <!-- Abas Administrativas -->
        <div class="w3-card-custom w3-white w3-padding-large w3-margin-top">

            <!-- Barra de Navegação das Abas -->
            <div class="w3-bar w3-border-bottom w3-margin-bottom">
                <button id="btn-tab-pendentes" class="w3-bar-item w3-button tab-btn w3-active-tab w3-round-large w3-margin-right" onclick="openAdminTab(event, 'content-pendentes', '#content-pendentes')">
                    <i class="bi bi-person-check me-1"></i> Aprov. Profissionais (<?= count($profissionaisPendentes ?? []) ?>)
                </button>
                <button id="btn-tab-usuarios" class="w3-bar-item w3-button tab-btn w3-round-large w3-margin-right" onclick="openAdminTab(event, 'content-usuarios', '#content-usuarios')">
                    <i class="bi bi-people me-1"></i> Usuários
                </button>
                <button id="btn-tab-categorias" class="w3-bar-item w3-button tab-btn w3-round-large" onclick="openAdminTab(event, 'content-categorias', '#content-categorias')">
                    <i class="bi bi-tags me-1"></i> Categorias
                </button>
            </div>

            <!-- Conteúdo das Abas -->
            <div id="admin-tabContent">

                <!-- Aba 1: Profissionais Pendentes -->
                <div id="content-pendentes" class="tab-content-item" style="display: block;">
                    <?= view('admin/components/tab_pendentes') ?>
                </div>

                <!-- Aba 2: Lista de Usuários -->
                <div id="content-usuarios" class="tab-content-item">
                    <?= view('admin/components/tab_usuarios') ?>
                </div>

                <!-- Aba 3: Cadastro de Categorias -->
                <div id="content-categorias" class="tab-content-item">
                    <?= view('admin/components/tab_categorias') ?>
                </div>

            </div>
        </div>
    </div>

    <!-- Rodapé -->
    <?= view('components/footer') ?>

    <script>
        function openAdminTab(evt, tabName, hash) {
            let i, x, tablinks;
            x = document.getElementsByClassName("tab-content-item");
            for (i = 0; i < x.length; i++) {
                x[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tab-btn");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("w3-active-tab");
            }
            document.getElementById(tabName).style.display = "block";

            if (evt && evt.currentTarget) {
                evt.currentTarget.classList.add("w3-active-tab");
            }

            if (hash) {
                history.replaceState(null, null, hash + window.location.search);
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const hash = window.location.hash;

            let targetBtn = null;

            // 1. PRIORIDADE MÁXIMA: Paginação/Filtros explícitos de USUÁRIOS
            if (urlParams.has('page_usuarios') || urlParams.has('busca_nome') || urlParams.has('busca_email') || urlParams.has('busca_status')) {
                targetBtn = document.getElementById('btn-tab-usuarios');
            }
            // 2. Paginação/Filtros explícitos de CATEGORIAS
            else if (urlParams.has('page_categorias') || urlParams.has('busca_categoria')) {
                targetBtn = document.getElementById('btn-tab-categorias');
            }
            // 3. Fallback pela Hash da URL
            else if (hash === '#content-usuarios') {
                targetBtn = document.getElementById('btn-tab-usuarios');
            } else if (hash === '#content-categorias') {
                targetBtn = document.getElementById('btn-tab-categorias');
            }

            // Ativa a aba correta
            if (targetBtn) {
                targetBtn.click();
            }
        });
    </script>
</body>

</html>