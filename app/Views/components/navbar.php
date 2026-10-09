<?php
$perfilAtivo = session()->get('perfil_ativo') ?? 'Cliente';
$nomeUsuario = session()->get('nome') ?? 'Usuário';
$isAdmin     = (bool) session()->get('is_admin');
$isLoggedIn  = (bool) session()->get('logged_in');

// Verifica se o usuário tem permissão para alternar perfis
$podeAlternar = $isAdmin || session()->get('tem_perfil_profissional') || session()->get('tipo_perfil') === 'Profissional' || $perfilAtivo === 'Profissional';

// Verifica se o usuário possui cadastro profissional (seja pelo perfil ativo, flag na sessão ou ID profissional)
$temCadastroProfissional = (bool) (
    $perfilAtivo === 'Profissional' ||
    session()->get('tem_perfil_profissional') || 
    session()->get('tipo_perfil') === 'Profissional' || 
    session()->get('profissional_id') || 
    session()->get('status_profissional')
);

$dashboardUrl = site_url('cliente/dashboard');
if ($perfilAtivo === 'Administrador') {
    $dashboardUrl = site_url('admin/dashboard');
} elseif ($perfilAtivo === 'Profissional') {
    $dashboardUrl = site_url('profissional/dashboard');
}
?>

<!-- Header / Navbar Global -->
<header style="background-color: #3d3a37; color: #ffffff; position: relative; z-index: 10000; width: 100%; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
    <div style="max-width: 1200px; margin: 0 auto; padding: 10px 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
        
        <!-- Logótipo -->
        <a href="<?= $dashboardUrl ?>" style="padding: 0; text-decoration: none; color: #ffffff; font-weight: bold; font-size: 1.25rem; display: flex; align-items: center; gap: 8px;">
            <i class="bi bi-shield-lock-fill" style="color: #ffc107; font-size: 1.4rem;"></i>
            <span>GetNinjas</span>
        </a>

        <?php if ($isLoggedIn): ?>
            <!-- Lado Direito: Perfil e Ações -->
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                
                <!-- Nome e Badge -->
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 0.875rem; color: #e0e0e0; display: flex; align-items: center; gap: 4px;">
                        <i class="bi bi-person-circle"></i> <?= esc($nomeUsuario) ?>
                    </span>

                    <span style="background-color: #ffc107; color: #212529; font-size: 0.75rem; font-weight: bold; padding: 3px 10px; border-radius: 12px;">
                        <?= esc($perfilAtivo) ?>
                    </span>
                </div>

                <!-- Dropdown Alternar Perfil (Disponível se for Admin ou tiver múltiplos perfis) -->
                <?php if ($podeAlternar): ?>
                    <div style="position: relative; display: inline-block;">
                        <button type="button" onclick="toggleMenu('dropdownPerfilMenuGlobal', event)" style="background-color: #2b2826; color: #ffffff; border: 1px solid #ffffff; border-radius: 6px; padding: 5px 12px; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                            <i class="bi bi-phone"></i>
                            <span>Alternar Perfil</span>
                            <i class="bi bi-caret-down-fill" style="font-size: 0.75rem;"></i>
                        </button>
                        
                        <!-- Caixa Flutuante do Menu -->
                        <div id="dropdownPerfilMenuGlobal" style="display: none; position: absolute; right: 0; top: 100%; min-width: 210px; background-color: #ffffff; color: #212529; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.25); z-index: 999999; margin-top: 6px; overflow: hidden; border: 1px solid #dee2e6;">
                            
                            <?php if ($isAdmin): ?>
                                <a href="<?= site_url('usuario/mudarParaAdmin') ?>" style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; text-decoration: none; color: #212529; font-size: 0.875rem; font-weight: 600; background-color: <?= $perfilAtivo === 'Administrador' ? '#f8f9fa' : 'transparent' ?>;">
                                    <i class="bi bi-shield-lock" style="color: #d9a74a;"></i> Modo Administrador
                                </a>
                                <div style="border-top: 1px solid #e9ecef; margin: 0;"></div>
                            <?php endif; ?>

                            <a href="<?= site_url('usuario/mudarParaCliente') ?>" style="display: flex; align-items: center; gap: 10px; padding: 10px 16px; text-decoration: none; color: #212529; font-size: 0.875rem; background-color: <?= $perfilAtivo === 'Cliente' ? '#f8f9fa' : 'transparent' ?>;">
                                <i class="bi bi-person"></i> Modo Cliente
                            </a>
                            <a href="<?= site_url('usuario/mudarParaProfissional') ?>" style="display: flex; align-items: center; gap: 10px; padding: 10px 16px; text-decoration: none; color: #212529; font-size: 0.875rem; background-color: <?= $perfilAtivo === 'Profissional' ? '#f8f9fa' : 'transparent' ?>;">
                                <i class="bi bi-briefcase"></i> Modo Profissional
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Dropdown Meu Perfil (Dados Pessoais e Profissionais) -->
                <div style="position: relative; display: inline-block;">
                    <button type="button" onclick="toggleMenu('dropdownMeuPerfilMenu', event)" style="background-color: transparent; color: #ffffff; border: 1px solid #ffffff; border-radius: 6px; padding: 5px 12px; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                        <i class="bi bi-person-gear"></i>
                        <span>Meu Perfil</span>
                        <i class="bi bi-caret-down-fill" style="font-size: 0.75rem;"></i>
                    </button>
                    
                    <!-- Caixa Flutuante do Menu Meu Perfil -->
                    <div id="dropdownMeuPerfilMenu" style="display: none; position: absolute; right: 0; top: 100%; min-width: 220px; background-color: #ffffff; color: #212529; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.25); z-index: 999999; margin-top: 6px; overflow: hidden; border: 1px solid #dee2e6;">
                        
                        <!-- Dados Pessoais -->
                        <a href="<?= site_url('meu-perfil') ?>" style="display: flex; align-items: center; gap: 10px; padding: 10px 16px; text-decoration: none; color: #212529; font-size: 0.875rem;">
                            <i class="bi bi-person-badge" style="color: #6c757d;"></i> Dados Pessoais
                        </a>

                        <div style="border-top: 1px solid #e9ecef; margin: 0;"></div>

                        <!-- Dados Profissionais ou Ativar Perfil -->
                        <?php if ($temCadastroProfissional): ?>
                            <a href="<?= site_url('profissional/editar-perfil') ?>" style="display: flex; align-items: center; gap: 10px; padding: 10px 16px; text-decoration: none; color: #212529; font-size: 0.875rem;">
                                <i class="bi bi-briefcase" style="color: #d9a74a;"></i> Dados Profissionais
                            </a>
                        <?php else: ?>
                            <a href="<?= site_url('profissional/ativar-perfil') ?>" style="display: flex; align-items: center; gap: 10px; padding: 10px 16px; text-decoration: none; color: #d9a74a; font-size: 0.875rem; font-weight: 600;">
                                <i class="bi bi-star"></i> Quero Ser Profissional
                            </a>
                        <?php endif; ?>

                    </div>
                </div>

                <!-- Botão Sair -->
                <a href="<?= site_url('logout') ?>" style="background-color: #dc3545; color: #ffffff; border: none; border-radius: 6px; padding: 5px 12px; font-size: 0.85rem; text-decoration: none; display: flex; align-items: center; gap: 6px;">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Sair</span>
                </a>
            </div>
        <?php endif; ?>
    </div>
</header>

<script>
function toggleMenu(id, e) {
    if (e) e.stopPropagation();
    
    // Fecha todos os dropdowns para evitar sobreposição
    var menus = ['dropdownPerfilMenuGlobal', 'dropdownMeuPerfilMenu'];
    menus.forEach(function(menuId) {
        var el = document.getElementById(menuId);
        if (el && menuId !== id) {
            el.style.display = "none";
        }
    });

    var drop = document.getElementById(id);
    if (drop) {
        drop.style.display = (drop.style.display === "block") ? "none" : "block";
    }
}

// Fecha os menus ao clicar fora
window.addEventListener('click', function() {
    var menus = ['dropdownPerfilMenuGlobal', 'dropdownMeuPerfilMenu'];
    menus.forEach(function(menuId) {
        var el = document.getElementById(menuId);
        if (el) {
            el.style.display = "none";
        }
    });
});
</script>