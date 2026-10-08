<!-- Métricas Rápidas (W3-CSS Grid) -->
<div class="w3-row-padding w3-margin-bottom" style="margin-left:-16px; margin-right:-16px;">
    <!-- Card 1: Análises Pendentes -->
    <div class="w3-third w3-margin-bottom">
        <div class="w3-card-custom w3-white w3-padding-16 w3-container" style="border-left: 5px solid #d8be92;">
            <span class="w3-text-grey w3-small w3-bold">Análises Pendentes</span>
            <h3 class="w3-margin-none w3-text-dark-grey w3-xlarge" style="margin-top: 4px !important;">
                <b><?= count($profissionaisPendentes ?? []) ?></b>
            </h3>
        </div>
    </div>

    <!-- Card 2: Total de Usuários -->
    <div class="w3-third w3-margin-bottom">
        <div class="w3-card-custom w3-white w3-padding-16 w3-container" style="border-left: 5px solid #2196F3;">
            <span class="w3-text-grey w3-small w3-bold">Total de Usuários</span>
            <h3 class="w3-margin-none w3-text-dark-grey w3-xlarge" style="margin-top: 4px !important;">
                <b><?= count($usuarios ?? []) ?></b>
            </h3>
        </div>
    </div>

    <!-- Card 3: Categorias Ativas -->
    <div class="w3-third w3-margin-bottom">
        <div class="w3-card-custom w3-white w3-padding-16 w3-container" style="border-left: 5px solid #4CAF50;">
            <span class="w3-text-grey w3-small w3-bold">Categorias Ativas</span>
            <h3 class="w3-margin-none w3-text-dark-grey w3-xlarge" style="margin-top: 4px !important;">
                <b><?= count($categorias ?? []) ?></b>
            </h3>
        </div>
    </div>
</div>