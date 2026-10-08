<!-- Cabeçalho do Profissional -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2>Painel do Profissional</h2>
        <p class="text-muted mb-0">Bem-vindo(a), <?= session()->get('nome') ?? 'Profissional' ?>!</p>
    </div>
    
    <!-- Botões de Navegação entre Perfis -->
    <div class="d-flex gap-2 align-items-center">
        <?php if (session()->get('is_admin')): ?>
            <a href="<?= site_url('usuario/mudarParaAdmin') ?>" class="btn btn-warning btn-sm fw-bold">
                <i class="bi bi-shield-lock me-1"></i> Voltar ao Painel Admin
            </a>
        <?php endif; ?>

        <a href="<?= site_url('usuario/mudarParaCliente') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-person me-1"></i> Alternar para Perfil Cliente
        </a>

        <a href="<?= site_url('logout') ?>" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-box-arrow-right me-1"></i> Sair
        </a>
    </div>
</div>