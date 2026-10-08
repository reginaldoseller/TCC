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
                <button class="btn btn-primary opacity-50 text-white w-100" <?= ($statusUsuario === 'suspenso') ? 'disabled' : '' ?>>
                    <?= ($statusUsuario === 'suspenso') ? 'Bloqueado' : 'Em breve' ?>
                </button>
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
                <button class="btn btn-warning opacity-50 text-white w-100" <?= ($statusUsuario === 'suspenso') ? 'disabled' : '' ?>>
                    <?= ($statusUsuario === 'suspenso') ? 'Bloqueado' : 'Em breve' ?>
                </button>
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
                <button class="btn btn-success opacity-50 text-white w-100" <?= ($statusUsuario === 'suspenso') ? 'disabled' : '' ?>>
                    <?= ($statusUsuario === 'suspenso') ? 'Bloqueado' : 'Em breve' ?>
                </button>
            </div>
        </div>
    </div>
</div>