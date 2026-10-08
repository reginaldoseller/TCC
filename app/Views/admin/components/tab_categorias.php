<div class="w3-row-padding" style="display: flex; flex-wrap: wrap; margin: 0 -8px;">

    <!-- Coluna Esquerda: Formulário de Cadastro -->
    <div class="w3-col l4 m5 s12" style="margin-bottom: 16px;">
        <div class="w3-card-custom w3-light-grey w3-padding-large w3-round-large" style="height: 100%;">
            <h5 class="w3-bold w3-text-dark-grey w3-margin-bottom">Nova Categoria</h5>
            
            <form action="<?= site_url('admin/cadastrarCategoria') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="w3-margin-bottom">
                    <label for="nome_categoria" class="w3-text-grey w3-small"><b>Nome da Categoria</b></label>
                    <input type="text" 
                           id="nome_categoria" 
                           name="nome" 
                           class="w3-input w3-border w3-round w3-white" 
                           placeholder="Ex: Reformas, Assistência Técnica" 
                           required>
                </div>

                <div class="w3-margin-bottom">
                    <label for="descricao_categoria" class="w3-text-grey w3-small"><b>Descrição <span class="w3-normal">(Opcional)</span></b></label>
                    <textarea id="descricao_categoria" 
                              name="descricao" 
                              class="w3-input w3-border w3-round w3-white" 
                              rows="3" 
                              placeholder="Breve resumo sobre os serviços contemplados nesta categoria..." 
                              style="resize: vertical;"></textarea>
                </div>

                <button type="submit" class="w3-button w3-ocre w3-hover-ocre w3-block w3-round w3-bold">
                    <i class="bi bi-plus-circle me-1"></i> Cadastrar Categoria
                </button>
            </form>
        </div>
    </div>

    <!-- Coluna Direita: Busca, Listagem e Paginação -->
    <div class="w3-col l8 m7 s12">
        <h5 class="w3-bold w3-text-dark-grey w3-margin-bottom">Categorias Existentes</h5>

        <!-- Form de Pesquisa -->
        <form method="get" action="<?= site_url('admin/dashboard') ?>#content-categorias" class="w3-margin-bottom">
            <div style="display: flex; gap: 8px;">
                <input type="text" 
                       name="busca_categoria" 
                       class="w3-input w3-border w3-round" 
                       placeholder="Pesquisar categoria..." 
                       value="<?= esc($busca_categoria ?? '') ?>">
                <button type="submit" class="w3-button w3-dark-custom w3-round">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>

        <!-- Lista de Categorias -->
        <ul class="w3-ul w3-card-custom w3-white w3-round-large w3-border">
            <?php if (!empty($categorias)): ?>
                <?php foreach ($categorias as $cat): ?>
                    <?php 
                        $catNome = $cat['categoria'] ?? $cat['nome'] ?? 'Sem nome';
                        $catDesc = $cat['descricao'] ?? '';
                    ?>
                    <li class="w3-padding-16" style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; flex-direction: column; gap: 2px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="bi bi-tag w3-text-grey"></i>
                                <span class="w3-text-dark-grey w3-bold"><?= esc($catNome) ?></span>
                            </div>
                            <?php if (!empty($catDesc)): ?>
                                <small class="w3-text-grey" style="margin-left: 24px; max-width: 400px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= esc($catDesc) ?>">
                                    <?= esc($catDesc) ?>
                                </small>
                            <?php endif; ?>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="w3-tag w3-light-grey w3-text-dark-grey w3-border w3-round w3-small me-2">
                                #<?= $cat['id'] ?? '-' ?>
                            </span>

                            <!-- Botão Editar -->
                            <button type="button" 
                                    class="w3-button w3-light-grey w3-hover-amber w3-round w3-small" 
                                    onclick="abrirModalEditar(<?= $cat['id'] ?>, '<?= esc($catNome, 'js') ?>', '<?= esc($catDesc, 'js') ?>')" 
                                    title="Editar Categoria">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="w3-center w3-text-grey w3-padding-24">Nenhuma categoria encontrada.</li>
            <?php endif; ?>
        </ul>

        <!-- Paginação -->
        <?php if (!empty($pager_categorias)): ?>
            <div class="w3-margin-top w3-center">
                <?= $pager_categorias->only(['busca_categoria', 'page_categorias'])->links('categorias', 'w3_pagination') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- MODAL DE EDIÇÃO -->
<div id="modalEditarCategoria" class="w3-modal">
    <div class="w3-modal-content w3-card-4 w3-animate-top w3-round-large" style="max-width: 500px;">
        <header class="w3-container w3-white w3-border-bottom" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 16px;">
            <h5 class="w3-bold my-0 w3-text-dark-grey"><i class="bi bi-pencil-square me-2"></i>Editar Categoria</h5>
            <span onclick="fecharModalEditar()" class="w3-button w3-transparent w3-hover-light-grey w3-round" style="font-size: 20px; line-height: 1;">&times;</span>
        </header>

        <form action="<?= site_url('admin/atualizarCategoria') ?>" method="post" class="w3-container w3-padding-24">
            <?= csrf_field() ?>
            <input type="hidden" id="edit_id" name="id">

            <div class="w3-margin-bottom">
                <label for="edit_nome" class="w3-text-grey w3-small"><b>Nome da Categoria</b></label>
                <input type="text" class="w3-input w3-border w3-round w3-white" id="edit_nome" name="nome" style="padding: 10px;" required>
            </div>

            <div class="w3-margin-bottom">
                <label for="edit_descricao" class="w3-text-grey w3-small"><b>Descrição</b></label>
                <textarea class="w3-input w3-border w3-round w3-white" id="edit_descricao" name="descricao" rows="3" style="padding: 10px; resize: vertical;"></textarea>
            </div>

            <div class="w3-margin-top" style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="fecharModalEditar()" class="w3-button w3-light-grey w3-round w3-bold">Cancelar</button>
                <button type="submit" class="w3-button w3-ocre w3-hover-ocre w3-round w3-bold">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalEditar(id, nome, descricao) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nome').value = nome;
        document.getElementById('edit_descricao').value = descricao || '';
        document.getElementById('modalEditarCategoria').style.display = 'block';
    }

    function fecharModalEditar() {
        document.getElementById('modalEditarCategoria').style.display = 'none';
    }
</script>