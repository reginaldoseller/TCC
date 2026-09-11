<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Profissional</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
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

        /* Tom ocre suave */
        .nav-pills .nav-link {
            color: #555;
            font-weight: 500;
        }
    </style>
</head>

<body>

    <div class="container py-4">
        <!-- Cabeçalho -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2>Painel do Profissional</h2>
                <p class="text-muted mb-0">Bem-vindo(a), <?= session()->get('nome') ?? 'Profissional' ?>!</p>
            </div>
            
            <!-- ÁREA DOS BOTÕES DE NAVEGAÇÃO -->
            <div class="d-flex gap-2 align-items-center">
                <?php if (session()->get('is_admin')): ?>
                    <a href="<?= site_url('usuario/mudar-admin') ?>" class="btn btn-warning btn-sm fw-bold">
                        <i class="bi bi-shield-lock me-1"></i> Voltar ao Painel Admin
                    </a>
                <?php endif; ?>

                <a href="<?= site_url('usuario/mudar-cliente') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-person me-1"></i> Alternar para Perfil Cliente
                </a>

                <a href="<?= site_url('logout') ?>" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i> Sair
                </a>
            </div>
        </div>

        <!-- Mensagens de Alerta -->
        <?php if (session()->getFlashdata('sucesso')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('sucesso') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('erro')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('erro') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Métrica Rápida -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card card-dash bg-white p-3 border-start border-4 border-warning">
                    <span class="text-muted small">Disponíveis na Região</span>
                    <h3 class="mb-0">0</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-dash bg-white p-3 border-start border-4 border-info">
                    <span class="text-muted small">Em Andamento / Demonstrou Interesse</span>
                    <h3 class="mb-0">0</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-dash bg-white p-3 border-start border-4 border-success">
                    <span class="text-muted small">Orçamentos Concluídos</span>
                    <h3 class="mb-0">0</h3>
                </div>
            </div>
        </div>

        <!-- Navegação por Abas -->
        <div class="card card-dash bg-white p-4">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-regiao-tab" data-bs-toggle="pill" data-bs-target="#pills-regiao" type="button" role="tab" aria-controls="pills-regiao" aria-selected="true">
                        <i class="bi bi-geo-alt me-1"></i> Solicitações na Região
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-interesses-tab" data-bs-toggle="pill" data-bs-target="#pills-interesses" type="button" role="tab" aria-controls="pills-interesses" aria-selected="false">
                        <i class="bi bi-hand-thumbs-up me-1"></i> Tenho Interesse / Em Andamento
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-concluidos-tab" data-bs-toggle="pill" data-bs-target="#pills-concluidos" type="button" role="tab" aria-controls="pills-concluidos" aria-selected="false">
                        <i class="bi bi-check-circle me-1"></i> Concluídos
                    </button>
                </li>
            </ul>

            <div class="tab-content pt-3" id="pills-tabContent">
                <!-- Aba: Região -->
                <div class="tab-pane fade show active" id="pills-regiao" role="tabpanel" aria-labelledby="pills-regiao-tab">
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1"></i>
                        <p class="mt-2">Nenhum orçamento disponível para a sua região no momento.</p>
                    </div>
                </div>

                <!-- Aba: Interessados / Em Andamento -->
                <div class="tab-pane fade" id="pills-interesses" role="tabpanel" aria-labelledby="pills-interesses-tab">
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-hourglass-split fs-1"></i>
                        <p class="mt-2">Você ainda não demonstrou interesse em nenhum pedido de orçamento.</p>
                    </div>
                </div>

                <!-- Aba: Concluídos -->
                <div class="tab-pane fade" id="pills-concluidos" role="tabpanel" aria-labelledby="pills-concluidos-tab">
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-file-earmark-check fs-1"></i>
                        <p class="mt-2">Nenhum pedido concluído até o momento.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>