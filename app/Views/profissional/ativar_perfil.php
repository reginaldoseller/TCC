<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seja um Profissional - GetNinjas</title>
    <!-- W3.CSS e FontAwesome -->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .bg-conecta {
            background-color: #fcf8f2;
        }
    </style>
</head>

<body>

    <!-- Barra de Navegação Superior -->
    <div class="w3-bar w3-dark-grey w3-padding w3-card">
        <div class="w3-content" style="max-width:1200px">
            <a href="<?= base_url('/') ?>" class="w3-bar-item w3-button w3-large w3-bold">GetNinjas</a>
            <div class="w3-right">
                <a href="<?= base_url('cliente/dashboard') ?>" class="w3-bar-item w3-button w3-border w3-border-white w3-round w3-small w3-hover-white">
                    <i class="fa-solid fa-arrow-left w3-margin-right"></i> Voltar ao Painel
                </a>
            </div>
        </div>
    </div>

    <!-- Container do Formulário -->
    <div class="w3-content w3-padding-large" style="max-width:700px; margin-top:30px;">

        <div class="w3-card-4 w3-white w3-round-large w3-padding-24">

            <div class="w3-container w3-center w3-border-bottom w3-padding-16">
                <h2 class="w3-bold w3-text-dark-grey">
                    <i class="fa-solid fa-id-card-clip w3-text-amber"></i> Quero Oferecer Serviços
                </h2>
                <p class="w3-text-gray">Ative seu perfil profissional para começar a receber pedidos de orçamento</p>
            </div>

            <!-- Exibição de Alerta de Erro -->
            <?php if (session()->getFlashdata('erro')) : ?>
                <div class="w3-panel w3-red w3-display-container w3-round w3-margin-top">
                    <span onclick="this.parentElement.style.display='none'" class="w3-button w3-large w3-display-topright">&times;</span>
                    <p><i class="fa-solid fa-circle-exclamation w3-margin-right"></i> <?= session()->getFlashdata('erro') ?></p>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('profissional/processarAtivacao') ?>" method="post" class="w3-container w3-margin-top">
                <?= csrf_field() ?>

                <div class="w3-panel w3-light-grey w3-leftbar w3-border-blue w3-padding-16 w3-margin-bottom">
                    <p class="w3-small w3-margin-none w3-text-dark-grey">
                        <i class="fa-solid fa-circle-info w3-text-blue w3-margin-right"></i>
                        Seus dados de contato e endereço já cadastrados serão utilizados para conectar você aos clientes da sua região.
                    </p>
                </div>

                <!-- Descrição dos Serviços -->
                <div class="w3-row-padding">
                    <div class="w3-col w12 w3-margin-bottom">
                        <label class="w3-text-dark-grey"><b>Apresentação / Sobre seus Serviços *</b></label>
                        <textarea class="w3-input w3-border w3-round" name="descricaoPerfil" rows="5" required placeholder="Escreva um resumo do seu trabalho, tempo de experiência, diferenciais e como costuma atender seus clientes..."><?= old('descricaoPerfil') ?></textarea>
                    </div>
                </div>

                <!-- Raio de Atendimento em KM -->
                <div class="w3-row-padding">
                    <div class="w3-col w12 w3-margin-bottom">
                        <label class="w3-text-dark-grey"><b>Raio de Atendimento (km) *</b></label>
                        <input class="w3-input w3-border w3-round" type="number" name="raio_atendimento_km" value="<?= old('raio_atendimento_km', 15) ?>" min="1" max="500" required placeholder="Ex: 15">
                        <span class="w3-small w3-text-gray">Distância máxima em quilômetros que você aceita se deslocar para atender chamados.</span>
                    </div>
                </div>

                <!-- Categorias de Atuação -->
                <div class="w3-row-padding">
                    <div class="w3-col w12 w3-margin-bottom">
                        <label class="w3-text-dark-grey"><b>Selecione suas Especialidades / Categorias *</b></label>
                        
                        <!-- Campo para pesquisar categoria -->
                        <div class="w3-margin-bottom" style="margin-top: 5px;">
                            <input type="text" id="buscaCategoria" class="w3-input w3-border w3-round w3-white" placeholder="🔍 Digite o nome da categoria para buscar..." onkeyup="filtrarCategorias()">
                        </div>

                        <!-- Lista de Categorias -->
                        <div class="w3-padding w3-border w3-round w3-light-grey" style="max-height: 200px; overflow-y: auto;">
                            <p id="msgInstrucao" class="w3-text-gray w3-small w3-center" style="margin-top: 8px;">Digite no campo acima para exibir as categorias disponíveis.</p>

                            <?php if (!empty($categorias)) : ?>
                                <?php foreach ($categorias as $cat) : ?>
                                    <div class="w3-margin-bottom item-categoria w3-hide">
                                        <input class="w3-check" type="checkbox" name="categorias[]" value="<?= $cat['id'] ?>" id="cat_<?= $cat['id'] ?>">
                                        <label for="cat_<?= $cat['id'] ?>" class="w3-validate nome-categoria" style="cursor:pointer;"> 
                                            <?= esc($cat['categoria']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <p class="w3-text-gray w3-small w3-center">Nenhuma categoria encontrada no banco de dados.</p>
                            <?php endif; ?>

                            <p id="msgSemResultado" class="w3-text-gray w3-small w3-center" style="display: none;">Nenhuma categoria encontrada com esse nome.</p>
                        </div>
                    </div>
                </div>

                <!-- Botão de Ativação -->
                <div class="w3-container w3-margin-top">
                    <button type="submit" class="w3-button w3-amber w3-text-dark-grey w3-block w3-round-large w3-large w3-card w3-hover-orange w3-bold">
                        <i class="fa-solid fa-rocket w3-margin-right"></i> Ativar Perfil Profissional
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Rodapé Grupo ConectaDev -->
    <footer class="w3-container w3-center w3-padding-32 w3-border-top bg-conecta" style="margin-top: 60px;">
        <p class="w3-large w3-margin-bottom">
            <i class="fa-solid fa-code w3-text-amber"></i> Desenvolvido pelo <strong>Grupo ConectaDev</strong>
        </p>
        <p class="w3-small w3-text-gray">&copy; <?= date('Y') ?> GetNinjas - Todos os direitos reservados.</p>
    </footer>

    <!-- Script para Filtrar Categorias em Tempo Real -->
    <script>
        function filtrarCategorias() {
            const input = document.getElementById('buscaCategoria').value.toLowerCase().trim();
            const itens = document.querySelectorAll('.item-categoria');
            const msgSemResultado = document.getElementById('msgSemResultado');
            const msgInstrucao = document.getElementById('msgInstrucao');
            let encontrados = 0;

            if (input === '') {
                itens.forEach(function(item) {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    if (!checkbox.checked) {
                        item.classList.add('w3-hide');
                    } else {
                        encontrados++;
                    }
                });

                if (encontrados === 0) {
                    msgInstrucao.style.display = "block";
                } else {
                    msgInstrucao.style.display = "none";
                }

                msgSemResultado.style.display = "none";
                return;
            }

            msgInstrucao.style.display = "none";

            itens.forEach(function(item) {
                const texto = item.querySelector('.nome-categoria').innerText.toLowerCase();
                const checkbox = item.querySelector('input[type="checkbox"]');

                if (texto.includes(input) || checkbox.checked) {
                    item.classList.remove('w3-hide');
                    encontrados++;
                } else {
                    item.classList.add('w3-hide');
                }
            });

            if (encontrados === 0) {
                msgSemResultado.style.display = "block";
            } else {
                msgSemResultado.style.display = "none";
            }
        }
    </script>

</body>

</html>