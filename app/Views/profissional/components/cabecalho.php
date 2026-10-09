<!-- Cabeçalho do Profissional -->
<div class="p-4 mb-4 rounded-3 shadow-sm" style="background-color: #fcf8f2; border-left: 5px solid #ffc107;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1" style="color: #3d3a37;">
                <i class="bi bi-briefcase-fill text-warning me-2"></i> Painel do Profissional
            </h2>
            <p class="text-muted mb-0">Bem-vindo(a), <?= esc(session()->get('nome') ?? 'Profissional') ?>! Gerencie seus atendimentos e serviços por aqui.</p>
        </div>
    </div>
</div>