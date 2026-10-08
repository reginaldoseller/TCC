<!-- Alertas de Feedback (W3-CSS) -->
<?php if (session()->getFlashdata('sucesso')): ?>
    <div class="w3-panel w3-green w3-display-container w3-round-large w3-card">
        <span onclick="this.parentElement.style.display='none'" class="w3-button w3-large w3-display-topright w3-round">&times;</span>
        <p><i class="bi bi-check-circle me-1"></i> <?= session()->getFlashdata('sucesso') ?></p>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('erro')): ?>
    <div class="w3-panel w3-red w3-display-container w3-round-large w3-card">
        <span onclick="this.parentElement.style.display='none'" class="w3-button w3-large w3-display-topright w3-round">&times;</span>
        <p><i class="bi bi-exclamation-triangle me-1"></i> <?= session()->getFlashdata('erro') ?></p>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('aviso')): ?>
    <div class="w3-panel w3-amber w3-display-container w3-round-large w3-card">
        <span onclick="this.parentElement.style.display='none'" class="w3-button w3-large w3-display-topright w3-round">&times;</span>
        <p><i class="bi bi-info-circle me-1"></i> <?= session()->getFlashdata('aviso') ?></p>
    </div>
<?php endif; ?>