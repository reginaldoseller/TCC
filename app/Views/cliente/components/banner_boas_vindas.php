<!-- Banner de Boas-Vindas e Ações de Perfil -->
<div class="card card-dash bg-white p-4 mb-4 border-ocre">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1">Olá, <?= session()->get('nome') ?? 'Cliente' ?>!</h2>
            <p class="text-muted mb-0">Seja bem-vindo ao seu painel principal.</p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <?php if ($isAdmin): ?>
                <a href="<?= site_url('usuario/mudarParaAdmin') ?>" class="btn btn-warning btn-sm fw-bold">
                    <i class="bi bi-shield-lock me-1"></i> Voltar ao Painel Admin
                </a>
            <?php endif; ?>

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