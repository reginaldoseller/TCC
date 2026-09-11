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
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-custom {
            background-color: #343a40;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        .card-dash {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-dash:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.08);
        }

        /* Destaque em tom ocre suave */
        .border-ocre {
            border-left: 4px solid #d9a74a !important;
        }

        .bg-ocre-soft {
            background-color: #fdfaf3;
            color: #8c6b23;
        }

        .footer-custom {
            margin-top: auto;
            background-color: #ffffff;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>

<body>

    <?php
    // Identifica o perfil de visualização ativo e consulta o status na tabela 'profissional'
    $perfilAtivo = session()->get('perfil_ativo') ?? 'Cliente';
    $usuarioId   = session()->get('id');
    $isAdmin     = (bool) session()->get('is_admin');

    $db = \Config\Database::connect();
    $dadosProfissional = $db->table('profissional')
        ->where('usuario_id', $usuarioId)
        ->get()
        ->getRowArray();

    // Validação estrita de status
    $temCadastroProfissional = !empty($dadosProfissional);
    $statusProfissional      = $temCadastroProfissional ? ($dadosProfissional['status'] ?? '') : '';

    // Considera profissional ativo se o cadastro no banco estiver 'ativo' OU se for Administrador do sistema
    $ehProfissionalAtivo     = ($temCadastroProfissional && $statusProfissional === 'ativo') || $isAdmin;
    $ehProfissionalEmAnalise = $temCadastroProfissional && ($statusProfissional === 'em_analise');
    ?>

    <!-- Navbar Principal -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom px-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="bi bi-person-workspace me-1"></i> GetNinjas
            </a>

            <div class="d-flex align-items-center gap-3">
                <span class="text-white small">
                    <i class="bi bi-person-circle me-1"></i> <?= session()->get('nome') ?? 'Usuário' ?>
                </span>

                <!-- Badge do Perfil Ativo -->
                <span class="badge rounded-pill bg-light text-dark fw-normal">
                    Perfil: <strong><?= $perfilAtivo ?></strong>
                </span>

                <!-- Botão de Sair -->
                <a href="<?= site_url('logout') ?>" class="btn btn-outline-light btn-sm ms-2">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">

        <!-- Mensagens de Alerta -->
        <?php if (session()->getFlashdata('sucesso')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('sucesso') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('aviso')): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('aviso') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('erro')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-x-circle-fill me-2"></i><?= session()->getFlashdata('erro') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Banner de Boas-Vindas e Ações de Perfil -->
        <div class="card card-dash bg-white p-4 mb-4 border-ocre">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold mb-1">Olá, <?= session()->get('nome') ?? 'Cliente' ?>!</h2>
                    <p class="text-muted mb-0">Seja bem-vindo ao seu painel principal.</p>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- Botão para Administradores -->
                    <?php if ($isAdmin): ?>
                        <a href="<?= site_url('usuario/mudar-admin') ?>" class="btn btn-warning btn-sm fw-bold">
                            <i class="bi bi-shield-lock me-1"></i> Voltar ao Painel Admin
                        </a>
                    <?php endif; ?>

                    <!-- Ações de Perfil Profissional -->
                    <?php if ($ehProfissionalAtivo): ?>
                        <!-- Redireciona diretamente para a Dashboard do Profissional -->
                        <a href="<?= site_url('profissional/dashboard') ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-briefcase me-1"></i> Alternar para Perfil Profissional
                        </a>
                    <?php elseif ($ehProfissionalEmAnalise): ?>
                        <span class="badge bg-warning text-dark p-2 border border-warning" style="font-size: 0.85rem;">
                            <i class="bi bi-hourglass-split me-1"></i> Perfil Profissional em Análise
                        </span>
                    <?php else: ?>
                        <!-- Só exibe para quem NÃO possui cadastro algum de profissional e não é Admin -->
                        <a href="<?= site_url('profissional/ativar-perfil') ?>" class="btn btn-outline-warning text-dark fw-semibold btn-sm">
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
                        <span class="bg-primary-subtle text-primary p-3 rounded-circle d-inline-block">
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
                        <span class="bg-warning-subtle text-warning p-3 rounded-circle d-inline-block">
                            <i class="bi bi-clock-history fs-3"></i>
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
                        <span class="bg-success-subtle text-success p-3 rounded-circle d-inline-block">
                            <i class="bi bi-check2-circle fs-3"></i>
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