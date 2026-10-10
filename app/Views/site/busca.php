<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Busca de Profissionais - GetNinjas</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body class="w3-light-grey">

    <!-- Barra Superior -->
    <div class="w3-bar w3-dark-grey w3-large">
        <span class="w3-bar-item w3-left">GetNinjas - Busca de Profissionais</span>
        <a href="<?= base_url('/') ?>" class="w3-bar-item w3-button w3-right">Home</a>
    </div>

    <div class="w3-container w3-content" style="max-width:1100px; margin-top:40px;">
        
        <!-- Titulo e Filtros -->
        <div class="w3-card w3-white w3-padding w3-margin-bottom">
            <h2>Encontre o profissional ideal</h2>
            <form action="<?= base_url('/busca') ?>" method="get">
                <div class="w3-row-padding">
                    <div class="w3-half">
                        <label>Selecione a Categoria</label>
                        <select name="categoria" class="w3-select w3-border">
                            <option value="">Todas as categorias</option>
                            <?php if (!empty($categorias)): ?>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= $cat['categoria'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="w3-half" style="padding-top:24px;">
                        <button type="submit" class="w3-button w3-blue w3-block">
                            <i class="fa fa-search"></i> Filtrar Profissionais
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Listagem de Profissionais -->
        <div class="w3-row-padding">
            <?php if (!empty($profissionais)): ?>
                <?php foreach ($profissionais as $prof): ?>
                    <div class="w3-third w3-margin-bottom">
                        <div class="w3-card w3-white w3-padding">
                            <h3><?= esc($prof['nome']) ?></h3>
                            <p><i class="fa fa-map-marker w3-text-red"></i> <?= esc($prof['cidade']) ?> - <?= esc($prof['bairro']) ?></p>
                            <p><strong>Sobre:</strong> <?= esc($prof['descricaoPerfil'] ?? 'Nenhuma descricao informada.') ?></p>
                            <p><strong>Raio de Atendimento:</strong> <?= esc($prof['raio_atendimento_km'] ?? '0') ?> km</p>
                            <a href="#" class="w3-button w3-green w3-block">Solicitar Orcamento</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="w3-panel w3-yellow w3-padding">
                    <p>Nenhum profissional encontrado para os filtros selecionados.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>