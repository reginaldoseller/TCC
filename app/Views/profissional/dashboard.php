<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Profissional - GetNinjas</title>

    <!-- Bootstrap 5 & Ícones -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        /* Container central flexível que empurra o rodapé para a base */
        .main-content {
            flex: 1 0 auto;
        }

        .card-dash {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .nav-pills .nav-link.active {
            background-color: #d9a74a;
            color: #fff;
        }

        .nav-pills .nav-link {
            color: #555;
            font-weight: 500;
        }
    </style>
</head>

<body>

    <!-- Header / Navbar Global Reutilizada -->
    <?= view('components/navbar') ?>

    <!-- Conteúdo Principal -->
    <div class="main-content container py-4">

        <!-- Cabeçalho com saudações e ações de perfil -->
        <?= view('profissional/components/cabecalho') ?>

        <!-- Alertas Globais (Flashdata) -->
        <?= view('components/alerts') ?>

        <!-- Cards de Métricas -->
        <?= view('profissional/components/cards_metricas') ?>

        <!-- Navegação por Abas -->
        <div class="card card-dash bg-white p-4 mb-5">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-regiao-tab" data-bs-toggle="pill" data-bs-target="#pills-regiao" type="button" role="tab">
                        <i class="bi bi-geo-alt me-1"></i> Solicitações na Região
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-interesses-tab" data-bs-toggle="pill" data-bs-target="#pills-interesses" type="button" role="tab">
                        <i class="bi bi-hand-thumbs-up me-1"></i> Tenho Interesse / Em Andamento
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-concluidos-tab" data-bs-toggle="pill" data-bs-target="#pills-concluidos" type="button" role="tab">
                        <i class="bi bi-check-circle me-1"></i> Concluídos
                    </button>
                </li>
            </ul>

            <div class="tab-content pt-3" id="pills-tabContent">
                <!-- Aba 1: Região -->
                <div class="tab-pane fade show active" id="pills-regiao" role="tabpanel">
                    <?= view('profissional/components/tab_solicitacoes_regiao') ?>
                </div>

                <!-- Aba 2: Interessados / Em Andamento -->
                <div class="tab-pane fade" id="pills-interesses" role="tabpanel">
                    <?= view('profissional/components/tab_interesses') ?>
                </div>

                <!-- Aba 3: Concluídos -->
                <div class="tab-pane fade" id="pills-concluidos" role="tabpanel">
                    <?= view('profissional/components/tab_concluidos') ?>
                </div>
            </div>
        </div>

    </div>

    <!-- Rodapé Global Reutilizado -->
    <?= view('components/footer') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>