<!-- Métricas Rápidas (W3-CSS Grid Alinhado em 4 Colunas) -->
<div class="w3-row-padding w3-margin-bottom" style="margin-left:-12px; margin-right:-12px;">
    
    <!-- Card 1: Análises Pendentes -->
    <div class="w3-quarter w3-margin-bottom">
        <div class="w3-card w3-white w3-round-large w3-padding-16 w3-container" style="border-left: 6px solid #ff9800; border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span class="w3-text-grey w3-small" style="text-transform: uppercase; letter-spacing: 0.5px;"><b>Análises Pendentes</b></span>
                    <h3 class="w3-margin-none w3-text-dark-grey w3-xxlarge" style="margin-top: 6px !important;">
                        <b><?= $totalPendentes ?? 0 ?></b>
                    </h3>
                </div>
                <div class="w3-text-orange w3-large" style="background: #fff8e1; padding: 12px; border-radius: 50%;">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Total de Profissionais -->
    <div class="w3-quarter w3-margin-bottom">
        <div class="w3-card w3-white w3-round-large w3-padding-16 w3-container" style="border-left: 6px solid #2196F3; border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span class="w3-text-grey w3-small" style="text-transform: uppercase; letter-spacing: 0.5px;"><b>Profissionais</b></span>
                    <h3 class="w3-margin-none w3-text-dark-grey w3-xxlarge" style="margin-top: 6px !important;">
                        <b><?= $totalProfissionais ?? 0 ?></b>
                    </h3>
                </div>
                <div class="w3-text-blue w3-large" style="background: #e3f2fd; padding: 12px; border-radius: 50%;">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Total de Usuários -->
    <div class="w3-quarter w3-margin-bottom">
        <div class="w3-card w3-white w3-round-large w3-padding-16 w3-container" style="border-left: 6px solid #00bcd4; border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span class="w3-text-grey w3-small" style="text-transform: uppercase; letter-spacing: 0.5px;"><b>Total de Usuários</b></span>
                    <h3 class="w3-margin-none w3-text-dark-grey w3-xxlarge" style="margin-top: 6px !important;">
                        <b><?= $totalUsuarios ?? 0 ?></b>
                    </h3>
                </div>
                <div class="w3-text-cyan w3-large" style="background: #e0f7fa; padding: 12px; border-radius: 50%;">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Categorias Ativas -->
    <div class="w3-quarter w3-margin-bottom">
        <div class="w3-card w3-white w3-round-large w3-padding-16 w3-container" style="border-left: 6px solid #4CAF50; border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span class="w3-text-grey w3-small" style="text-transform: uppercase; letter-spacing: 0.5px;"><b>Categorias Ativas</b></span>
                    <h3 class="w3-margin-none w3-text-dark-grey w3-xxlarge" style="margin-top: 6px !important;">
                        <b><?= $totalCategorias ?? 0 ?></b>
                    </h3>
                </div>
                <div class="w3-text-green w3-large" style="background: #e8f5e9; padding: 12px; border-radius: 50%;">
                    <i class="fa-solid fa-tags"></i>
                </div>
            </div>
        </div>
    </div>

</div>