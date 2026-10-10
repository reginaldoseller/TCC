<h5 class="w3-large w3-text-dark-grey w3-bold w3-margin-bottom">Gestão de Profissionais</h5>

<!-- Filtros de Busca de Profissionais -->
<form method="get" action="<?= site_url('admin/dashboard') ?>#content-profissionais" class="w3-card-custom w3-light-grey w3-padding w3-round-large w3-margin-bottom">
    <div class="w3-row-padding">

        <!-- Input Nome / E-mail -->
        <div class="w3-third w3-margin-bottom">
            <label class="w3-text-grey w3-small"><b>Nome ou E-mail</b></label>
            <input type="text"
                name="busca_prof_nome"
                class="w3-input w3-border w3-round w3-white"
                placeholder="Buscar profissional..."
                value="<?= esc($_GET['busca_prof_nome'] ?? '') ?>">
        </div>

        <!-- Select Categoria -->
        <div class="w3-third w3-margin-bottom">
            <label class="w3-text-grey w3-small"><b>Categoria</b></label>
            <select name="busca_prof_categoria" class="w3-select w3-border w3-round w3-white">
                <option value="">Todas as categorias</option>
                <?php if (!empty($todas_categorias)): ?>
                    <?php foreach ($todas_categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (isset($_GET['busca_prof_categoria']) && $_GET['busca_prof_categoria'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= esc($cat['categoria']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <!-- Select Status da Aprovação/Perfil (Valores ajustados conforme ENUM do banco) -->
        <div class="w3-third w3-margin-bottom">
            <label class="w3-text-grey w3-small"><b>Status do Perfil</b></label>
            <select name="busca_prof_status" class="w3-select w3-border w3-round w3-white">
                <option value="">Todos os status</option>
                <option value="ativo" <?= (isset($_GET['busca_prof_status']) && $_GET['busca_prof_status'] === 'ativo') ? 'selected' : '' ?>>Ativos</option>
                <option value="em_analise" <?= (isset($_GET['busca_prof_status']) && $_GET['busca_prof_status'] === 'em_analise') ? 'selected' : '' ?>>Em Análise</option>
                <option value="pendente" <?= (isset($_GET['busca_prof_status']) && $_GET['busca_prof_status'] === 'pendente') ? 'selected' : '' ?>>Pendente</option>
                <option value="indisponivel" <?= (isset($_GET['busca_prof_status']) && $_GET['busca_prof_status'] === 'indisponivel') ? 'selected' : '' ?>>Indisponível</option>
                <option value="suspenso" <?= (isset($_GET['busca_prof_status']) && $_GET['busca_prof_status'] === 'suspenso') ? 'selected' : '' ?>>Suspenso</option>
            </select>
        </div>

    </div>

    <!-- Botões do Filtro -->
    <div class="w3-container w3-right-align">
        <a href="<?= site_url('admin/dashboard') ?>#content-profissionais" class="w3-button w3-white w3-border w3-round w3-small w3-margin-right">
            <i class="bi bi-x-circle me-1"></i> Limpar Filtros
        </a>
        <button type="submit" class="w3-button w3-ocre w3-hover-ocre w3-round w3-small w3-bold">
            <i class="bi bi-search me-1"></i> Filtrar
        </button>
    </div>
</form>

<!-- Tabela de Profissionais -->
<?php if (empty($profissionais)): ?>
    <div class="w3-center w3-padding-32 w3-text-grey">
        <i class="bi bi-person-badge w3-jumbo"></i>
        <p class="w3-margin-top">Nenhum profissional encontrado com os filtros aplicados.</p>
    </div>
<?php else: ?>
    <div class="w3-responsive">
        <table class="w3-table-all w3-hoverable w3-card-custom w3-round">
            <thead>
                <tr class="w3-light-grey">
                    <th>ID</th>
                    <th>Profissional</th>
                    <th>Categorias Atendidas</th>
                    <th>Status do Perfil</th>
                    <th>Avaliação</th>
                    <th class="w3-right-align">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($profissionais as $prof): ?>
                    <?php
                    // Usando usuario_id como identificador principal
                    $profId = $prof['usuario_id'] ?? '-';
                    $statusPerfil = $prof['status'] ?? 'em_analise';
                    ?>
                    <tr>
                        <td>#<?= $profId ?></td>
                        <td>
                            <strong><?= esc($prof['nome'] ?? 'Sem nome') ?></strong><br>
                            <small class="w3-text-grey"><i class="bi bi-envelope me-1"></i><?= esc($prof['email'] ?? '-') ?></small>
                        </td>
                        <td>
                            <?php if (!empty($prof['categorias_list'])): ?>
                                <?php foreach ($prof['categorias_list'] as $catNome): ?>
                                    <span class="w3-tag w3-light-grey w3-border w3-round w3-tiny me-1">
                                        <?= esc($catNome) ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="w3-text-grey w3-small"><em>Nenhuma cadastrada</em></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($statusPerfil === 'ativo'): ?>
                                <span class="w3-tag w3-green w3-round w3-small">Ativo</span>
                            <?php elseif (in_array($statusPerfil, ['em_analise', 'pendente'])): ?>
                                <span class="w3-tag w3-amber w3-round w3-small">Aguardando Análise</span>
                            <?php elseif ($statusPerfil === 'suspenso'): ?>
                                <span class="w3-tag w3-red w3-round w3-small">Suspenso</span>
                            <?php elseif ($statusPerfil === 'indisponivel'): ?>
                                <span class="w3-tag w3-grey w3-round w3-small">Indisponível</span>
                            <?php else: ?>
                                <span class="w3-tag w3-grey w3-round w3-small"><?= esc(ucfirst($statusPerfil)) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (isset($prof['media_avaliacao']) && $prof['media_avaliacao'] > 0): ?>
                                <span class="w3-text-amber w3-bold">
                                    <i class="bi bi-star-fill"></i> <?= number_format($prof['media_avaliacao'], 1, ',', '.') ?>
                                </span>
                                <small class="w3-text-grey">(<?= $prof['total_avaliacoes'] ?? 0 ?>)</small>
                            <?php else: ?>
                                <small class="w3-text-grey">Sem avaliações</small>
                            <?php endif; ?>
                        </td>
                        <td class="w3-right-align">
                            <!-- Botão Dossiê (Reservado para Implementação Futura) -->
                            <button type="button"
                                class="w3-button w3-light-grey w3-text-grey w3-hover-blue w3-round w3-tiny w3-margin-right"
                                title="Relatório/Dossiê do Profissional"
                                onclick="alert('O módulo de dossiê do profissional estará disponível em breve.')">
                                <i class="bi bi-file-earmark-person"></i> Dossiê
                            </button>

                            <!-- Ações Condicionais por Status -->
                            <?php if (in_array($statusPerfil, ['em_analise', 'pendente'])): ?>
                                <a href="<?= site_url('admin/aprovarProfissional/' . $profId) ?>"
                                    class="w3-button w3-green w3-round w3-tiny"
                                    onclick="return confirm('Aprovar o perfil deste profissional?')">
                                    <i class="bi bi-check-lg"></i> Aprovar
                                </a>
                            <?php else: ?>
                                <button type="button"
                                    class="w3-button w3-amber w3-round w3-tiny"
                                    onclick="alert('Em breve: Moderação/Pausa do Perfil Profissional')">
                                    <i class="bi bi-pause-circle"></i> Moderar
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginação de Profissionais -->
    <?php if (!empty($pager_profissionais)): ?>
        <div class="w3-margin-top w3-center">
            <?= $pager_profissionais->only(['busca_prof_nome', 'busca_prof_categoria', 'busca_prof_status', 'page_profissionais'])->links('profissionais', 'w3_pagination') ?>
        </div>
    <?php endif; ?>
<?php endif; ?>