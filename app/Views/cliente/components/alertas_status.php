<!-- Alerta Crítico: Usuário Geral Suspenso -->
<?php if ($statusUsuario === 'suspenso'): ?>
    <div class="alert alert-danger border-start border-4 border-danger shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-start gap-3">
            <i class="bi bi-exclamation-octagon-fill fs-3 text-danger"></i>
            <div>
                <h5 class="alert-heading fw-bold mb-1">Sua conta está temporariamente suspensa</h5>
                <p class="mb-1">O acesso às ações da sua conta de usuário foi restrito pela administração.</p>
                <?php if (!empty($motivoBloqueio)): ?>
                    <p class="mb-1 small"><strong>Motivo do Bloqueio:</strong> <?= esc($motivoBloqueio) ?></p>
                <?php endif; ?>
                <?php if (!empty($usuarioBloqueadoAte)): ?>
                    <small class="text-dark fw-semibold">
                        <i class="bi bi-calendar-event me-1"></i> Previsão de liberação da conta: 
                        <strong><?= date('d/m/Y H:i', strtotime($usuarioBloqueadoAte)) ?></strong>
                    </small>
                <?php endif; ?>
            </div>
        </div>
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

<!-- Alerta 3: Perfil Profissional Indisponível -->
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

                <button class="btn btn-outline-secondary btn-sm fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#detalhesIndisponivel" aria-expanded="false" aria-controls="detalhesIndisponivel">
                    <i class="bi bi-chevron-down me-1"></i> Ver Detalhes
                </button>
            </div>

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