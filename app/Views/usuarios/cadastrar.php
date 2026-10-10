<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Cliente - GetNinjas</title>
    <!-- W3.CSS e FontAwesome -->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .bg-conecta { background-color: #fcf8f2; }
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
                <h2 class="w3-bold w3-text-dark-grey">Cadastro de Cliente</h2>
                <p class="w3-text-gray">Preencha seus dados para solicitar serviços na plataforma</p>
            </div>

            <!-- Exibição de Alerta de Erro -->
            <?php if (session()->getFlashdata('erro')) : ?>
                <div class="w3-panel w3-red w3-display-container w3-round w3-margin-top">
                    <span onclick="this.parentElement.style.display='none'" class="w3-button w3-large w3-display-topright">&times;</span>
                    <p><i class="fa-solid fa-circle-exclamation w3-margin-right"></i> <?= session()->getFlashdata('erro') ?></p>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('usuario/criar') ?>" method="post" class="w3-container w3-margin-top">
                <?= csrf_field() ?>

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
                        <input class="w3-input w3-border w3-round" type="password" name="senha" required placeholder="Crie uma senha de acesso">
                    </div>
                    <div class="w3-half w3-margin-bottom">
                        <label class="w3-text-dark-grey"><b>Confirmar Senha *</b></label>
                        <input class="w3-input w3-border w3-round" type="password" name="confirma_senha" required placeholder="Repita a senha">
                    </div>
                </div>

                <!-- Botão de Envio -->
                <div class="w3-container w3-margin-top">
                    <button type="submit" class="w3-button w3-blue w3-block w3-round-large w3-large w3-card w3-hover-dark-blue">
                        <i class="fa-solid fa-user-check w3-margin-right"></i> Concluir Cadastro
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

    <!-- Scripts de Máscara (Interação Visual) -->
    <script>
        // Máscara Dinâmica de CPF
        const inputCPF = document.getElementById('cpf');
        inputCPF.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 11) v = v.slice(0, 11);
            
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            
            e.target.value = v;
        });

        // Máscara Dinâmica de Telefone / WhatsApp (8 ou 9 dígitos)
        const inputTelefone = document.getElementById('telefones');
        inputTelefone.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 11) v = v.slice(0, 11);
            
            v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
            v = v.replace(/(\d)(\d{4})$/, '$1-$2');
            
            e.target.value = v;
        });

        // Máscara Dinâmica de CEP
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