<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Cliente - GetNinjas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f6f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-custom {
            background-color: #4a4a4a;
        }

        .card-dash {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease;
        }

        .card-dash:hover {
            transform: translateY(-3px);
        }

        .footer-custom {
            margin-top: auto;
            background-color: #fbf9f5;
            border-top: 1px solid #eae5d9;
        }
    </style>
</head>

<body>

    <?php
    // Identifica o perfil de visualização ativo e o status geral do perfil
    $perfilAtivo  = session()->get('perfil_ativo') ?? 'Cliente';
    $tipoPerfil   = session()->get('tipo_perfil') ?? 'Cliente';
    ?>

    <!-- Navbar Principal -->
    <!-- Navbar Principal com alinhamento centralizado -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom px-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">GetNinjas</a>

            <div class="d-flex align-items-center gap-3">
                <span class="text-white">
                    <i class="bi bi-person-circle me-1"></i> <?= session()->get('nome') ?? 'Usuário' ?>
                </span>

                <!-- Badge do Perfil Ativo -->
                <span class="badge <?= ($perfilAtivo ?? 'Cliente') === 'Profissional' ? 'bg-primary' : 'bg-info text-dark' ?>">
                    <?= $perfilAtivo ?? 'Cliente' ?>
                </span>

                <a href="<?= site_url('logout') ?>" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">

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

        <!-- Banner de Boas-Vindas e Status do Perfil -->
        <div class="card card-dash bg-white p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold mb-1">Olá, <?= session()->get('nome') ?? 'Cliente' ?>!</h2>
                    <p class="text-muted mb-0">Seja bem-vindo ao seu painel principal.</p>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-light border rounded text-secondary">
                        <i class="bi bi-journal-bookmark me-1"></i> Perfil: <strong><?= $perfilAtivo ?></strong>
                    </div>

                    <!-- Ações para alternar ou ativar o perfil profissional -->
                    <?php if ($tipoPerfil === 'Profissional'): ?>
                        <a href="<?= site_url('usuario/mudar-profissional') ?>" class="btn btn-outline-primary">
                            <i class="bi bi-briefcase me-1"></i> Ir para Painel Profissional
                        </a>
                    <?php else: ?>
                        <a href="<?= site_url('profissional/ativar-perfil') ?>" class="btn btn-warning text-dark fw-semibold">
                            <i class="bi bi-star me-1"></i> Quero Ser Profissional
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Opções da Área do Cliente -->
        <div class="row g-4">
            <!-- Card 1: Solicitar Orçamento -->
            <div class="col-md-4">
                <div class="card card-dash bg-white text-center p-4 h-100 border-start border-4 border-primary">
                    <div class="mb-3">
                        <span class="bg-primary text-white p-3 rounded-circle d-inline-block">
                            <i class="bi bi-plus-lg fs-3"></i>
                        </span>
                    </div>
                    <h5 class="fw-bold">Solicitar Orçamento</h5>
                    <p class="text-muted small">Precisa de um serviço? Descreva o que precisa e receba propostas de profissionais.</p>
                    <div class="mt-auto">
                        <button class="btn btn-primary opacity-50 text-white w-100" disabled>Em breve</button>
                    </div>
                </div>
            </div>

            <!-- Card 2: Orçamentos em Aberto -->
            <div class="col-md-4">
                <div class="card card-dash bg-white text-center p-4 h-100 border-start border-4 border-warning">
                    <div class="mb-3">
                        <span class="bg-warning text-white p-3 rounded-circle d-inline-block">
                            <i class="bi bi-arrow-counterclockwise fs-3"></i>
                        </span>
                    </div>
                    <h5 class="fw-bold">Orçamentos em Aberto</h5>
                    <p class="text-muted small">Acompanhe e responda às propostas enviadas pelos profissionais qualificados.</p>
                    <div class="mt-auto">
                        <button class="btn btn-warning opacity-50 text-white w-100" disabled>Em breve</button>
                    </div>
                </div>
            </div>

            <!-- Card 3: Serviços Concluídos -->
            <div class="col-md-4">
                <div class="card card-dash bg-white text-center p-4 h-100 border-start border-4 border-success">
                    <div class="mb-3">
                        <span class="bg-success text-white p-3 rounded-circle d-inline-block">
                            <i class="bi bi-list-check fs-3"></i>
                        </span>
                    </div>
                    <h5 class="fw-bold">Serviços Concluídos</h5>
                    <p class="text-muted small">Consulte o histórico dos seus serviços finalizados e avaliações feitas.</p>
                    <div class="mt-auto">
                        <button class="btn btn-success opacity-50 text-white w-100" disabled>Em breve</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Rodapé -->
    <footer class="footer-custom py-3 text-center">
        <div class="container">
            <p class="mb-0 text-muted small">
                <span class="text-warning fw-bold">&lt;/&gt;</span> Desenvolvido pelo <strong>Grupo ConectaDev</strong>
            </p>
            <p class="mb-0 text-muted extra-small" style="font-size: 0.8rem;">
                &copy; <?= date('Y') ?> GetNinjas - Todos os direitos reservados.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>