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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
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
    $observacaoAdmin        = $temCadastroProfissional ? ($dadosProfissional['observacao_admin'] ?? '') : '';
    $bloqueadoAte           = $temCadastroProfissional ? ($dadosProfissional['bloqueado_ate'] ?? '') : '';

    // Mapeamento dos cenários
    $ehProfissionalAtivo        = ($temCadastroProfissional && $statusProfissional === 'ativo') || $isAdmin;
    $ehProfissionalEmAnalise    = $temCadastroProfissional && ($statusProfissional === 'em_analise' || $statusProfissional === 'pendente');
    $ehProfissionalAjustes      = $temCadastroProfissional && ($statusProfissional === 'ajustes_solicitados');
    $ehProfissionalIndisponivel = $temCadastroProfissional && ($statusProfissional === 'indisponivel' || $statusProfissional === 'suspenso');
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

        <!-- Mensagens de Alerta Flashdata -->
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

        <!-- Alerta 1: Perfil em Análise -->
        <?php if ($ehProfissionalEmAnalise): ?>
            <div class="alert alert-warning alert-dismissible fade show border-start border-4 border-warning shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-hourglass-split fs-4"></i>
                    <div>
                        <strong>Perfil em Análise:</strong> Seu perfil profissional está sob análise da nossa equipe. Você pode navegar como cliente normalmente até a aprovação.
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Alerta 2: Orientação de Ajustes -->
        <?php if ($ehProfissionalAjustes): ?>
            <div class="alert alert-warning border-start border-4 border-warning shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-pencil-square fs-3 text-warning"></i>
                    <div class="w-100">
                        <h5 class="alert-heading fw-bold mb-1">Ajustes Necessários no Perfil Profissional</h5>
                        <p class="mb-2">A equipe de análise identificou inconsistências no seu cadastro que impedem a aprovação imediata.</p>
                        <?php if (!empty($observacaoAdmin)): ?>
                            <div class="bg-white p-3 rounded border text-dark mb-3">
                                <strong>Orientação da Administração:</strong>
                                <p class="mb-0 text-secondary mt-1"><?= nl2br(esc($observacaoAdmin)) ?></p>
                            </div>
                        <?php endif; ?>
                        <a href="<?= site_url('profissional/editar-perfil') ?>" class="btn btn-warning btn-sm text-dark fw-bold">
                            <i class="bi bi-pencil me-1"></i> Corrigir Dados do Perfil
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Alerta 3: Perfil Profissional Indisponível (Com Detalhes Clicáveis) -->
        <?php if ($ehProfissionalIndisponivel): ?>
            <div class="card border-0 border-start border-4 border-secondary shadow-sm mb-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-slash-circle fs-3 text-secondary"></i>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Status da Fila: Seu Perfil Profissional está Temporariamente Indisponível</h6>
                                <small class="text-muted">Clique em "Ver Detalhes" para entender o motivo e a previsão de reabertura de vagas.</small>
                            </div>
                        </div>

                        <!-- Botão para Expandir/Recolher o Texto -->
                        <button class="btn btn-outline-secondary btn-sm fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#detalhesIndisponivel" aria-expanded="false" aria-controls="detalhesIndisponivel">
                            <i class="bi bi-chevron-down me-1"></i> Ver Detalhes
                        </button>
                    </div>

                    <!-- Conteúdo Oculto/Expandível -->
                    <div class="collapse mt-3" id="detalhesIndisponivel">
                        <div class="p-3 bg-light rounded border text-secondary small">
                            <p class="mb-2">
                                <strong>Motivo:</strong> As adesões de novos prestadores de serviço para a sua categoria ou região geográfica foram suspensas temporariamente para equilibrar a oferta e demanda da plataforma.
                            </p>
                            <p class="mb-0">A navegação como <strong>Cliente</strong> para contratar serviços continua liberada sem restrições.</p>

                            <?php if (!empty($bloqueadoAte)): ?>
                                <div class="mt-2 pt-2 border-top text-dark fw-bold">
                                    <i class="bi bi-calendar-check me-1 text-primary"></i> Previsão para reabertura de novas vagas profissionais:
                                    <span class="badge bg-secondary"><?= date('d/m/Y', strtotime($bloqueadoAte)) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
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
                        <a href="<?= site_url('usuario/mudarParaAdmin') ?>" class="btn btn-warning btn-sm fw-bold">
                            <i class="bi bi-shield-lock me-1"></i> Voltar ao Painel Admin
                        </a>
                    <?php endif; ?>

                    <!-- Ações de Perfil Profissional conforme Status -->
                    <?php if ($ehProfissionalAtivo): ?>
                        <a href="<?= site_url('usuario/mudarParaProfissional') ?>" class="btn btn-secondary">
                            Alternar para Perfil Profissional
                        </a>
                    <?php elseif ($ehProfissionalEmAnalise): ?>
                        <span class="badge bg-warning text-dark p-2 border border-warning" style="font-size: 0.85rem;">
                            <i class="bi bi-hourglass-split me-1"></i> Perfil Profissional em Análise
                        </span>
                    <?php elseif ($ehProfissionalAjustes): ?>
                        <a href="<?= site_url('profissional/editar-perfil') ?>" class="btn btn-warning text-dark fw-bold btn-sm">
                            <i class="bi bi-pencil-square me-1"></i> Corrigir Perfil Profissional
                        </a>
                    <?php elseif ($ehProfissionalIndisponivel): ?>
                        <span class="badge bg-secondary text-white p-2" style="font-size: 0.85rem;">
                            <i class="bi bi-slash-circle me-1"></i> Adesão Temporariamente Indisponível
                        </span>
                    <?php else: ?>
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