<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - GetNinjas</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background-color: #f8f9fa; 
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .bg-conecta { background-color: #fcf8f2; }
        .main-content { flex: 1; }
    </style>
</head>
<body>

    <!-- Barra Superior -->
    <div class="w3-bar w3-dark-grey w3-padding w3-card">
        <div class="w3-content" style="max-width:1200px">
            <a href="<?= base_url('/') ?>" class="w3-bar-item w3-button w3-large w3-bold">GetNinjas</a>
        </div>
    </div>

    <!-- Conteúdo Principal -->
    <div class="main-content">
        <div class="w3-content w3-padding-large" style="max-width:500px; margin-top:40px;">
            
            <div class="w3-card-4 w3-white w3-round-large w3-padding-24">
                <div class="w3-container w3-center">
                    <h2 class="w3-bold">Recuperar Senha</h2>
                    <p class="w3-text-gray">Informe o seu e-mail cadastrado para receber as instruções de redefinição.</p>
                </div>

                <!-- Exibição de Mensagens de Sucesso -->
                <?php if (session()->getFlashdata('sucesso')) : ?>
                    <div class="w3-panel w3-green w3-display-container w3-round w3-margin-horizontal w3-padding">
                        <span onclick="this.parentElement.style.display='none'" class="w3-button w3-large w3-display-topright">&times;</span>
                        <p class="w3-margin-none"><i class="fa-solid fa-circle-check w3-margin-right"></i> <?= session()->getFlashdata('sucesso') ?></p>
                    </div>
                <?php endif; ?>

                <!-- Exibição de Erros -->
                <?php if (session()->getFlashdata('erro')) : ?>
                    <div class="w3-panel w3-red w3-display-container w3-round w3-margin-horizontal w3-padding">
                        <span onclick="this.parentElement.style.display='none'" class="w3-button w3-large w3-display-topright">&times;</span>
                        <p class="w3-margin-none"><i class="fa-solid fa-triangle-exclamation w3-margin-right"></i> <?= session()->getFlashdata('erro') ?></p>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('esqueci-senha/enviar') ?>" method="post" class="w3-container w3-margin-top">
                    <?= csrf_field() ?>

                    <!-- Campo de E-mail -->
                    <p>
                        <label class="w3-text-dark-grey"><b>E-mail cadastrado</b></label>
                        <input class="w3-input w3-border w3-round" type="email" name="email" required placeholder="seu@email.com" value="<?= old('email') ?>">
                    </p>

                    <!-- Botão de Enviar Instructions -->
                    <p class="w3-margin-top">
                        <button type="submit" class="w3-button w3-blue w3-block w3-round-large w3-large w3-card">
                            Enviar Link de Recuperação <i class="fa-solid fa-paper-plane w3-margin-left"></i>
                        </button>
                    </p>
                </form>

                <!-- Link de Retorno ao Login -->
                <div class="w3-container w3-center w3-margin-top w3-border-top w3-padding-16">
                    <a href="<?= base_url('login') ?>" class="w3-button w3-light-grey w3-border w3-round w3-block">
                        <i class="fa-solid fa-arrow-left w3-margin-right"></i> Voltar para a tela de Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Rodapé -->
    <footer class="w3-container w3-center w3-padding-24 w3-border-top bg-conecta" style="margin-top: 40px;">
        <p class="w3-large w3-margin-bottom">
            <i class="fa-solid fa-code w3-text-amber"></i> Desenvolvido pelo <strong>Grupo ConectaDev</strong>
        </p>
        <p class="w3-small w3-text-gray">&copy; <?= date('Y') ?> GetNinjas - Todos os direitos reservados.</p>
    </footer>

</body>
</html>