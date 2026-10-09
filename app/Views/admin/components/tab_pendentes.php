<h5 class="w3-large w3-text-dark-grey w3-bold w3-margin-bottom">Solicitações de Perfil Profissional</h5>

<?php if (empty($profissionaisPendentes)): ?>
    <div class="w3-center w3-padding-32 w3-text-grey">
        <i class="bi bi-check2-circle w3-jumbo"></i>
        <p class="w3-margin-top">Nenhuma solicitação pendente no momento.</p>
    </div>
<?php else: ?>
    <div class="w3-responsive">
        <table class="w3-table-all w3-hoverable w3-centered w3-card">
            <thead>
                <tr class="w3-light-grey">
                    <th>ID Prof.</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Cidade / UF</th>
                    <th>Status</th>
                    <th class="w3-right-align">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($profissionaisPendentes as $prof): ?>
                    <?php 
                        $profId = $prof['usuario_id'] ?? $prof['profissional_id'] ?? $prof['id'] ?? '-'; 
                    ?>
                    <tr>
                        <td>#<?= $profId ?></td>
                        <td><strong><?= esc($prof['nome'] ?? 'Sem nome') ?></strong></td>
                        <td><?= esc($prof['email'] ?? '-') ?></td>
                        <td><?= esc(($prof['cidade'] ?? '-') . ' / ' . ($prof['estado'] ?? '-')) ?></td>
                        <td><?= esc($prof['status'] ?? '-') ?></td>
                        <td class="w3-right-align">
                            <!-- Botão Aprovar -->
                            <a href="<?= site_url('admin/aprovarProfissional/' . $profId) ?>" class="w3-button w3-green w3-round w3-tiny">
                                <i class="bi bi-check-lg"></i> Aprovar
                            </a>

                            <!-- Botão Solicitar Ajustes (Abre Modal W3) -->
                            <button type="button" class="w3-button w3-amber w3-text-dark-grey w3-round w3-tiny" onclick="document.getElementById('modalAjustes<?= $profId ?>').style.display='block'">
                                <i class="bi bi-pencil-square"></i> Solicitar Ajustes
                            </button>

                            <!-- Botão Suspender Adesão -->
                            <a href="<?= site_url('admin/suspenderProfissional/' . $profId) ?>" class="w3-button w3-grey w3-round w3-tiny" onclick="return confirm('Confirma a suspensão temporária da adesão deste perfil?')">
                                <i class="bi bi-slash-circle"></i> Suspender
                            </a>
                        </td>
                    </tr>

                    <!-- Modal W3-CSS para Solicitar Ajustes de Dados -->
                    <div id="modalAjustes<?= $profId ?>" class="w3-modal">
                        <div class="w3-modal-content w3-card-4 w3-animate-top w3-round-large" style="max-width: 500px;">
                            <header class="w3-container w3-amber w3-padding">
                                <span onclick="document.getElementById('modalAjustes<?= $profId ?>').style.display='none'" class="w3-button w3-display-topright w3-round">&times;</span>
                                <h5 class="w3-bold w3-margin-none"><i class="bi bi-pencil-square me-1"></i> Orientação de Ajustes</h5>
                            </header>

                            <form action="<?= site_url('admin/solicitarAjustes/' . $profId) ?>" method="post" class="w3-container w3-padding">
                                <?= csrf_field() ?>
                                
                                <p class="w3-small w3-text-grey w3-left-align w3-margin-top">
                                    Digite abaixo quais dados ou informações o profissional <strong><?= esc($prof['nome'] ?? '') ?></strong> precisa corrigir para regularizar a solicitação:
                                </p>

                                <div class="w3-margin-bottom w3-left-align">
                                    <textarea class="w3-input w3-border w3-round" name="observacao" rows="4" placeholder="Ex: A foto do documento está ilegível ou a descrição do perfil necessita de mais detalhes." required></textarea>
                                </div>

                                <footer class="w3-container w3-light-grey w3-padding w3-right-align w3-margin-top">
                                    <button type="button" class="w3-button w3-white w3-border w3-round w3-small" onclick="document.getElementById('modalAjustes<?= $profId ?>').style.display='none'">Cancelar</button>
                                    <button type="submit" class="w3-button w3-amber w3-round w3-small w3-bold">Enviar Orientação</button>
                                </footer>
                            </form>
                        </div>
                    </div>

                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginação de Pendentes -->
    <?php if (!empty($pager_pendentes)): ?>
        <div class="w3-margin-top w3-center">
            <?= $pager_pendentes->only(['page_pendentes'])->links('pendentes', 'w3_pagination') ?>
        </div>
    <?php endif; ?>
<?php endif; ?>