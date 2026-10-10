<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Profissional - GetNinjas</title>
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
                <a href="<?= base_url('login') ?>" class="w3-bar-item w3-button w3-border w3-border-white w3-round w3-small w3-hover-white">
                    Já tenho conta (Login)
                </a>
            </div>
        </div>
    </div>

    <!-- Container do Formulário -->
    <div class="w3-content w3-padding-large" style="max-width:750px; margin-top:30px;">

        <div class="w3-card-4 w3-white w3-round-large w3-padding-24">

            <div class="w3-container w3-center w3-border-bottom w3-padding-16">
                <h2 class="w3-bold w3-text-dark-grey"><i class="fa-solid fa-briefcase w3-text-blue"></i> Cadastro de Profissional</h2>
                <p class="w3-text-gray">Crie seu perfil profissional para receber pedidos de orçamento</p>
            </div>

            <!-- Exibição de Alerta de Erro -->
            <?php if (session()->getFlashdata('erro')) : ?>
                <div class="w3-panel w3-red w3-display-container w3-round w3-margin-top">
                    <span onclick="this.parentElement.style.display='none'" class="w3-button w3-large w3-display-topright">&times;</span>
                    <p><i class="fa-solid fa-circle-exclamation w3-margin-right"></i> <?= session()->getFlashdata('erro') ?></p>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('profissional/criar') ?>" method="post" class="w3-container w3-margin-top">
                <?= csrf_field() ?>

                <h4 class="w3-text-blue w3-bold w3-margin-bottom"><i class="fa-solid fa-user"></i> Dados Pessoais</h4>

                <!-- Linha 1: Nome e CPF -->
                <div class="w3-row-padding">
                    <div class="w3-half w3-margin-bottom">
                        <label class="w3-text-dark-grey"><b>Nome Completo *</b></label>
                        <input class="w3-input w3-border w3-round" type="text" name="nome" value="<?= old('nome') ?>" required placeholder="Digite seu nome">
                    </div>
                    <div class="w3-half w3-margin-bottom">
                        <label class="w3-text-dark-grey"><b>CPF *</b></label>
                        <input class="w3-input w3-border w3-round" type="text" id="cpf" name="cpf" value="<?= old('cpf') ?>" required placeholder="000.000.000-00" maxlength="14">
                    </div>
                </div>

                <!-- Linha 2: E-mail e Telefone -->
                <div class="w3-row-padding">
                    <div class="w3-half w3-margin-bottom">
                        <label class="w3-text-dark-grey"><b>E-mail *</b></label>
                        <input class="w3-input w3-border w3-round" type="email" name="email" value="<?= old('email') ?>" required placeholder="seu@email.com">
                    </div>
                    <div class="w3-half w3-margin-bottom">
                        <label class="w3-text-dark-grey"><b>Telefone / WhatsApp *</b></label>
                        <input class="w3-input w3-border w3-round" type="text" id="telefones" name="telefone" value="<?= old('telefone') ?>" required placeholder="(00) 00000-0000" maxlength="15">
                    </div>
                </div>

                <!-- Linha 3: CEP e Bairro -->
                <div class="w3-row-padding">
                    <div class="w3-half w3-margin-bottom">
                        <label class="w3-text-dark-grey"><b>CEP *</b></label>
                        <input class="w3-input w3-border w3-round" type="text" id="cep" name="cep" value="<?= old('cep') ?>" required placeholder="00000-000" maxlength="9">
                    </div>
                    <div class="w3-half w3-margin-bottom">
                        <label class="w3-text-dark-grey"><b>Bairro *</b></label>
                        <input class="w3-input w3-border w3-round" type="text" name="bairro" value="<?= old('bairro') ?>" required placeholder="Seu bairro">
                    </div>
                </div>

                <!-- Linha 4: Cidade e UF -->
                <div class="w3-row-padding">
                    <div class="w3-threequarter w3-margin-bottom">
                        <label class="w3-text-dark-grey"><b>Cidade *</b></label>
                        <input class="w3-input w3-border w3-round" type="text" name="cidade" value="<?= old('cidade') ?>" required placeholder="Sua cidade">
                    </div>
                    <div class="w3-quarter w3-margin-bottom">
                        <label class="w3-text-dark-grey"><b>UF *</b></label>
                        <input class="w3-input w3-border w3-round" type="text" name="estado" value="<?= old('estado') ?>" required placeholder="SP" maxlength="2" style="text-transform: uppercase;">
                    </div>
                </div>

                <!-- Linha 5: Senha e Confirmação de Senha -->
                <div class="w3-row-padding">
                    <div class="w3-half w3-margin-bottom">
                        <label class="w3-text-dark-grey"><b>Senha *</b></label>
                        <input class="w3-input w3-border w3-round" type="password" name="senha" required placeholder="Crie uma senha segura">
                    </div>
                    <div class="w3-half w3-margin-bottom">
                        <label class="w3-text-dark-grey"><b>Confirmar Senha *</b></label>
                        <input class="w3-input w3-border w3-round" type="password" name="confirma_senha" required placeholder="Repita a senha">
                    </div>
                </div>

                <hr class="w3-border-grey" style="margin: 25px 0;">

                <!-- Seção Profissional -->
                <h4 class="w3-text-blue w3-bold w3-margin-bottom"><i class="fa-solid fa-address-card"></i> Perfil Profissional</h4>

                <!-- Descrição dos Serviços -->
                <div class="w3-row-padding">
                    <div class="w3-col w12 w3-margin-bottom">
                        <label class="w3-text-dark-grey"><b>Apresentação / Sobre seus Serviços *</b></label>
                        <textarea class="w3-input w3-border w3-round" name="descricaoPerfil" rows="4" required placeholder="Conte brevemente sobre sua experiência, diferenciais e serviços prestados..."><?= old('descricaoPerfil') ?></textarea>
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

                <!-- Categorias de Atuação (Filtradas por Busca) -->
                <div class="w3-row-padding">
                    <div class="w3-col w12 w3-margin-bottom">
                        <label class="w3-text-dark-grey"><b>Especialidades / Categorias de Atuação *</b></label>
                        
                        <!-- Campo para pesquisar categoria -->
                        <div class="w3-margin-bottom" style="margin-top: 5px;">
                            <input type="text" id="buscaCategoria" class="w3-input w3-border w3-round w3-white" placeholder="🔍 Digite o nome da categoria para buscar..." onkeyup="filtrarCategorias()">
                        </div>

                        <!-- Lista com Scroll -->
                        <div class="w3-padding w3-border w3-round w3-light-grey" style="max-height: 200px; overflow-y: auto;">
                            <p id="msgInstrucao" class="w3-text-gray w3-small w3-center" style="margin-top: 8px;">Digite no campo acima para exibir as categorias disponíveis.</p>
                            
                            <?php if (!empty($categorias)) : ?>
                                <?php foreach ($categorias as $cat) : ?>
                                    <!-- Inicia com w3-hide para ocultar tudo de começo -->
                                    <div class="w3-margin-bottom item-categoria w3-hide">
                                        <input class="w3-check" type="checkbox" name="categorias[]" value="<?= $cat['id'] ?>" id="cat_<?= $cat['id'] ?>">
                                        <label for="cat_<?= $cat['id'] ?>" class="w3-validate nome-categoria" style="cursor:pointer;">
                                            <?= esc($cat['categoria']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <p class="w3-text-gray w3-small w3-center">Nenhuma categoria cadastrada no momento.</p>
                            <?php endif; ?>
                            
                            <p id="msgSemResultado" class="w3-text-gray w3-small w3-center" style="display: none;">Nenhuma categoria encontrada com esse nome.</p>
                        </div>
                    </div>
                </div>

                <!-- Botão de Envio -->
                <div class="w3-container w3-margin-top">
                    <button type="submit" class="w3-button w3-blue w3-block w3-round-large w3-large w3-card w3-hover-dark-blue">
                        <i class="fa-solid fa-briefcase w3-margin-right"></i> Concluir Cadastro Profissional
                    </button>
                </div>
            </form>

            <div class="w3-container w3-center w3-margin-top w3-padding-16">
                <span class="w3-text-gray">Já possui uma conta?</span>
                <a href="<?= base_url('login') ?>" class="w3-text-blue w3-bold" style="text-decoration:none;"> Faça Login</a>
            </div>

        </div>
    </div>

    <!-- Rodapé Grupo ConectaDev -->
    <footer class="w3-container w3-center w3-padding-32 w3-border-top bg-conecta" style="margin-top: 60px;">
        <p class="w3-large w3-margin-bottom">
            <i class="fa-solid fa-code w3-text-amber"></i> Desenvolvido pelo <strong>Grupo ConectaDev</strong>
        </p>
        <p class="w3-small w3-text-gray">&copy; <?= date('Y') ?> GetNinjas - Todos os direitos reservados.</p>
    </footer>

    <!-- Scripts de Máscara e Filtro Otimizado -->
    <script>
        // Função para filtrar categorias conforme a digitação
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

        // CPF
        const inputCPF = document.getElementById('cpf');
        inputCPF.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 11) v = v.slice(0, 11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = v;
        });

        // Telefone / WhatsApp
        const inputTelefone = document.getElementById('telefones');
        inputTelefone.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 11) v = v.slice(0, 11);
            v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
            v = v.replace(/(\d)(\d{4})$/, '$1-$2');
            e.target.value = v;
        });

        // CEP
        const inputCEP = document.getElementById('cep');
        inputCEP.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 8) v = v.slice(0, 8);
            v = v.replace(/^(\d{5})(\d)/, '$1-$2');
            e.target.value = v;
        });
    </script>

</body>

</html>