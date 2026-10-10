<!-- Cabeçalho de Boas-Vindas -->
<div class="card card-dash bg-white p-4 mb-4 border-ocre">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1">Olá, <?= esc(session()->get('nome') ?? 'Cliente') ?>!</h2>
            <p class="text-muted mb-0">Seja bem-vindo ao seu painel principal.</p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <?php if ($ehProfissionalEmAnalise): ?>
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
            <?php endif; ?>
        </div>
    </div>
</div>