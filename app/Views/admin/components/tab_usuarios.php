<h5 class="w3-large w3-text-dark-grey w3-bold w3-margin-bottom">Usuários Cadastrados</h5>

<!-- Filtros de Busca (W3-CSS Grid) -->
<form method="get" action="<?= site_url('admin/dashboard') ?>#content-usuarios" class="w3-card-custom w3-light-grey w3-padding w3-round-large w3-margin-bottom">
    <div class="w3-row-padding">

        <!-- Input Nome -->
        <div class="w3-third w3-margin-bottom">
            <label class="w3-text-grey w3-small"><b>Nome</b></label>
            <div class="w3-display-container">
                <input type="text" name="busca_nome" class="w3-input w3-border w3-round w3-white" placeholder="Buscar por nome..." value="<?= esc($_GET['busca_nome'] ?? '') ?>">
            </div>
        </div>

        <!-- Input E-mail -->
        <div class="w3-third w3-margin-bottom">
            <label class="w3-text-grey w3-small"><b>E-mail</b></label>
            <div class="w3-display-container">
                <input type="text" name="busca_email" class="w3-input w3-border w3-round w3-white" placeholder="Buscar por e-mail..." value="<?= esc($_GET['busca_email'] ?? '') ?>">
            </div>
        </div>

        <!-- Select Status -->
        <div class="w3-third w3-margin-bottom">
            <label class="w3-text-grey w3-small"><b>Status</b></label>
            <select name="busca_status" class="w3-select w3-border w3-round w3-white">
                <option value="">Todos os status</option>
                <option value="ativo" <?= (isset($_GET['busca_status']) && $_GET['busca_status'] === 'ativo') ? 'selected' : '' ?>>Ativos</option>
                <option value="suspenso" <?= (isset($_GET['busca_status']) && $_GET['busca_status'] === 'suspenso') ? 'selected' : '' ?>>Suspensos</option>
            </select>
        </div>

    </div>

    <!-- Botões do Filtro -->
    <div class="w3-container w3-right-align">
        <a href="<?= site_url('admin/dashboard') ?>#content-usuarios" class="w3-button w3-white w3-border w3-round w3-small w3-margin-right">
            <i class="bi bi-x-circle me-1"></i> Limpar Filtros
        </a>
        <button type="submit" class="w3-button w3-ocre w3-hover-ocre w3-round w3-small w3-bold">
            <i class="bi bi-search me-1"></i> Filtrar
        </button>
    </div>
</form>

<!-- Tabela de Usuários -->
<?php if (empty($usuarios)): ?>
    <div class="w3-center w3-padding-32 w3-text-grey">
        <i class="bi bi-people w3-jumbo"></i>
        <p class="w3-margin-top">Nenhum usuário encontrado com os filtros aplicados.</p>
    </div>
<?php else: ?>
    <?php
    // ID do utilizador com sessão ativa
    $idLogado = session()->get('usuario.id') ?? session()->get('id') ?? session()->get('id_usuario');
    ?>
    <div class="w3-responsive">
        <table class="w3-table-all w3-hoverable w3-card-custom w3-round">
            <thead>
                <tr class="w3-light-grey">
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Perfil</th>
                    <th>Status</th>
                    <th class="w3-right-align">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usr): ?>
                    <?php
                    $usrId = $usr['id'] ?? '-';
                    $isSuspenso = isset($usr['status']) && $usr['status'] === 'suspenso';
                    $isEuMesmo = ($usrId == $idLogado);
                    ?>
                    <tr>
                        <td>#<?= $usrId ?></td>
                        <td>
                            <strong><?= esc($usr['nome'] ?? 'Sem nome') ?></strong>
                            <?php if ($isEuMesmo): ?>
                                <span class="w3-tag w3-amber w3-round w3-tiny w3-margin-left">Você</span>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($usr['email'] ?? '-') ?></td>
                        <td>
                            <span class="w3-tag w3-light-grey w3-border w3-round w3-small">
                                <?= esc(ucfirst($usr['tipo'] ?? 'Cliente')) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($isSuspenso): ?>
                                <span class="w3-tag w3-red w3-round w3-small">Suspenso</span>
                            <?php else: ?>
                                <span class="w3-tag w3-green w3-round w3-small">Ativo</span>
                            <?php endif; ?>
                        </td>
                        <td class="w3-right-align">
                            <?php if ($isEuMesmo): ?>
                                <!-- Proteção contra auto-suspensão -->
                                <button type="button" class="w3-button w3-grey w3-round w3-tiny" disabled title="Não é possível suspender a própria conta logada.">
                                    <i class="bi bi-slash-circle"></i> Suspender
                                </button>
                            <?php elseif ($isSuspenso): ?>
                                <!-- Botão Reativar Conta -->
                                <a href="<?= site_url('admin/reativarUsuario/' . $usrId) ?>" class="w3-button w3-green w3-round w3-tiny" onclick="return confirm('Confirma a reativação da conta deste usuário?')">
                                    <i class="bi bi-check-circle"></i> Reativar
                                </a>
                            <?php else: ?>
                                <!-- Botão Suspender (Abre Modal via JS) -->
                                <button type="button" class="w3-button w3-red w3-round w3-tiny" onclick="abrirModalSuspender(<?= $usrId ?>, '<?= esc($usr['nome'], 'js') ?>')">
                                    <i class="bi bi-slash-circle"></i> Suspender
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($pager_usuarios)): ?>
        <div class="w3-margin-top w3-center">
            <?= $pager_usuarios->only(['busca_nome', 'busca_email', 'busca_status', 'page_usuarios'])->links('usuarios', 'w3_pagination') ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- MODAL ÚNICO DE SUSPENSÃO DE USUÁRIO (Fora da tabela/loop) -->
<div id="modalSuspenderUsuario" class="w3-modal" style="padding-top: 50px;">
    <div class="w3-modal-content w3-card-4 w3-animate-top w3-round-large" style="max-width: 520px; overflow: hidden; border: none;">

        <!-- Cabeçalho Moderno com Tag de Alerta -->
        <header class="w3-container w3-white w3-border-bottom" style="padding: 16px 24px; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="background-color: #fee2e2; color: #dc2626; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h5 class="w3-bold my-0 w3-text-dark-grey" style="font-size: 1.1rem;">Suspender Conta</h5>
                    <span class="w3-small w3-text-grey">Ação de moderação de usuário</span>
                </div>
            </div>
            <span onclick="fecharModalSuspender()" class="w3-button w3-transparent w3-hover-light-grey w3-round-circle" style="font-size: 20px; line-height: 1; padding: 6px 12px;">&times;</span>
        </header>

        <!-- Form e Conteúdo -->
        <form id="formSuspenderUsuario" action="" method="post" class="w3-container" style="padding: 24px;">
            <?= csrf_field() ?>

            <!-- Caixinha com aviso/info do usuário selecionado -->
            <div class="w3-padding w3-round-large w3-margin-bottom" style="background-color: #f8fafc; border: 1px solid #e2e8f0; font-size: 0.9rem; color: #475569;">
                A suspensão impedirá o usuário <strong id="modal_usuario_nome" class="w3-text-dark-grey">...</strong> de realizar novas solicitações no sistema.
            </div>

            <!-- Campo Motivo -->
            <div class="w3-margin-bottom">
                <label for="motivo_bloqueio" class="w3-text-dark-grey w3-small w3-bold" style="display: block; margin-bottom: 6px;">
                    Motivo da Suspensão <span style="color: #dc2626;">*</span>
                </label>
                <textarea class="w3-input w3-border w3-round-large w3-white"
                          id="motivo_bloqueio"
                          name="motivo_bloqueio"
                          rows="3"
                          style="padding: 10px; resize: vertical; font-size: 0.95rem;"
                          placeholder="Ex: Violação dos termos de uso ou denúncia de comportamento inadequado."
                          required></textarea>
            </div>

            <!-- Campo Data/Hora de Bloqueio -->
            <div class="w3-margin-bottom">
                <label for="bloqueado_ate" class="w3-text-dark-grey w3-small w3-bold" style="display: block; margin-bottom: 6px;">
                    Bloquear Até <span class="w3-text-grey w3-normal">(Opcional)</span>
                </label>
                <input type="datetime-local"
                       class="w3-input w3-border w3-round-large w3-white"
                       id="bloqueado_ate"
                       name="bloqueado_ate"
                       style="padding: 10px; font-size: 0.95rem; color: #334155;">
                <span class="w3-tiny w3-text-grey" style="display: block; margin-top: 4px;">Deixe em branco para aplicar bloqueio por tempo indeterminado.</span>
            </div>

            <!-- Rodapé e Botões de Ação -->
            <div class="w3-margin-top" style="display: flex; justify-content: flex-end; gap: 10px; padding-top: 12px; border-top: 1px solid #f1f5f9;">
                <button type="button" onclick="fecharModalSuspender()" class="w3-button w3-light-grey w3-hover-grey w3-round-large w3-bold" style="padding: 10px 20px;">
                    Cancelar
                </button>
                <button type="submit" class="w3-button w3-round-large w3-bold" style="background-color: #dc2626; color: white; padding: 10px 20px;">
                    <i class="bi bi-slash-circle me-1"></i> Confirmar Suspensão
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalSuspender(id, nome) {
        // Define dinamicamente o action do formulário apontando para a rota de suspensão
        const form = document.getElementById('formSuspenderUsuario');
        form.action = "<?= site_url('admin/suspenderUsuario') ?>/" + id;

        // Atualiza o nome do usuário no alerta do modal
        document.getElementById('modal_usuario_nome').innerText = nome;

        // Limpa formulário
        document.getElementById('motivo_bloqueio').value = '';
        document.getElementById('bloqueado_ate').value = '';

        // Exibe o modal
        document.getElementById('modalSuspenderUsuario').style.display = 'block';
    }

    function fecharModalSuspender() {
        document.getElementById('modalSuspenderUsuario').style.display = 'none';
    }
</script>