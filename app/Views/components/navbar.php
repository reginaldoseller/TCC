<!-- Header Admin (W3-CSS) -->
<header class="w3-card" style="background-color: #3d3a37; color: #ffffff; margin-bottom: 24px; position: relative; z-index: 1000;">
    <div class="w3-content w3-padding" style="max-width: 1200px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
        
        <!-- Brand / Logo -->
        <a href="<?= site_url('admin/dashboard') ?>" class="w3-button w3-hover-none w3-hover-text-light-grey w3-bold w3-large" style="padding-left: 0;">
            <i class="bi bi-shield-lock me-2"></i>Painel Administrativo
        </a>

        <!-- Lado Direito: Ações de Perfil e Logout -->
        <div style="display: flex; align-items: center; gap: 12px; position: relative;">
            
            <!-- Dropdown Alternar Perfil (W3.CSS Nativo) -->
            <div class="w3-dropdown-click" style="background: none;">
                <button type="button" onclick="toggleMenuPerfil()" class="w3-button w3-border w3-border-white w3-round w3-small w3-hover-white w3-hover-text-dark">
                    <i class="bi bi-person-badge me-1"></i> Alternar Perfil <i class="bi bi-caret-down-fill w3-tiny"></i>
                </button>
                
                <div id="dropdownPerfilMenu" class="w3-dropdown-content w3-bar-block w3-card-4 w3-white w3-round-large" style="right: 0; top: 100%; min-width: 210px; z-index: 99999; margin-top: 6px;">
                    <a href="<?= site_url('usuario/mudarParaAdmin') ?>" class="w3-bar-item w3-button w3-padding w3-bold w3-text-dark-grey w3-hover-light-grey" style="text-decoration: none;">
                        <i class="bi bi-shield-lock me-2 w3-text-amber"></i>Modo Administrador
                    </a>
                    <div class="w3-border-bottom" style="margin: 2px 0;"></div>
                    <a href="<?= site_url('usuario/mudarParaCliente') ?>" class="w3-bar-item w3-button w3-padding w3-text-dark-grey w3-hover-light-grey" style="text-decoration: none;">
                        <i class="bi bi-person me-2"></i>Modo Cliente
                    </a>
                    <a href="<?= site_url('usuario/mudarParaProfissional') ?>" class="w3-bar-item w3-button w3-padding w3-text-dark-grey w3-hover-light-grey" style="text-decoration: none;">
                        <i class="bi bi-briefcase me-2"></i>Modo Profissional
                    </a>
                </div>
            </div>

            <!-- Indicador do Perfil Atual -->
            <span class="w3-small w3-text-grey" style="opacity: 0.8;">Administrador</span>

            <!-- Botão Sair -->
            <a href="<?= site_url('logout') ?>" class="w3-button w3-border w3-border-white w3-round w3-small w3-hover-white w3-hover-text-dark" style="text-decoration: none;">
                <i class="bi bi-box-arrow-right"></i> Sair
            </a>
        </div>
    </div>
</header>

<script>
function toggleMenuPerfil() {
    var x = document.getElementById("dropdownPerfilMenu");
    if (x.className.indexOf("w3-show") === -1) {
        x.className += " w3-show";
    } else { 
        x.className = x.className.replace(" w3-show", "");
    }
}

// Fecha o menu caso o utilizador clique fora dele
window.addEventListener('click', function(e) {
    var btn = document.querySelector('.w3-dropdown-click button');
    var drop = document.getElementById("dropdownPerfilMenu");
    if (btn && drop && !btn.contains(e.target) && !drop.contains(e.target)) {
        drop.className = drop.className.replace(" w3-show", "");
    }
});
</script>