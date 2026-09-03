<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GetNinjas</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .bg-conecta { background-color: #fcf8f2; }
    </style>
</head>
<body>

    <!-- Barra Superior -->
    <div class="w3-bar w3-dark-grey w3-padding w3-card">
        <div class="w3-content" style="max-width:1200px">
            <a href="<?= base_url('/') ?>" class="w3-bar-item w3-button w3-large w3-bold">GetNinjas</a>
        </div>
    </div>

    <!-- Formulário de Login -->
    <div class="w3-content w3-padding-large" style="max-width:500px; margin-top:40px;">
        
        <div class="w3-card-4 w3-white w3-round-large w3-padding-32">
            <div class="w3-container w3-center">
                <h2 class="w3-bold">Acessar sua Conta</h2>
                <p class="w3-text-gray">Informe seus dados para continuar</p>
            </div>

            <!-- Exibição de Erro de Login -->
            <?php if (session()->getFlashdata('erro')) : ?>
                <div class="w3-panel w3-red w3-display-container w3-round w3-margin-horizontal">
                    <span onclick="this.parentElement.style.display='none'" class="w3-button w3-large w3-display-topright">&times;</span>
                    <p><i class="fa-solid fa-triangle-exclamation me-2"></i> <?= session()->getFlashdata('erro') ?></p>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('login/autenticar') ?>" method="post" class="w3-container w3-margin-top">
                <?= csrf_field() ?>

                <p>
                    <label class="w3-text-dark-grey"><b>E-mail</b></label>
                    <input class="w3-input w3-border w3-round" type="email" name="email" required placeholder="seu@email.com">
                </p>

                <p>
                    <label class="w3-text-dark-grey"><b>Senha</b></label>
                    <input class="w3-input w3-border w3-round" type="password" name="senha" required placeholder="********">
                </p>

                <p class="w3-margin-top">
                    <button type="submit" class="w3-button w3-blue w3-block w3-round-large w3-large w3-card">
                        Entrar <i class="fa-solid fa-arrow-right-to-bracket w3-margin-left"></i>
                    </button>
                </p>
            </form>

            <!-- Link de Redirecionamento para Cadastro -->
            <div class="w3-container w3-center w3-margin-top w3-border-top w3-padding-16">
                <p class="w3-text-gray">Ainda não tem uma conta?</p>
                <a href="<?= base_url('usuario/cadastrar') ?>" class="w3-button w3-light-grey w3-border w3-round w3-block">
                    Criar nova conta (Cadastre-se)
                </a>
            </div>
        </div>
    </div>

    <!-- Rodapé -->
    <footer class="w3-container w3-center w3-padding-32 w3-border-top bg-conecta" style="margin-top: 60px;">
        <p class="w3-large w3-margin-bottom">
            <i class="fa-solid fa-code w3-text-amber"></i> Desenvolvido pelo <strong>Grupo ConectaDev</strong>
        </p>
        <p class="w3-small w3-text-gray">&copy; <?= date('Y') ?> GetNinjas - Todos os direitos reservados.</p>
    </footer>

</body>
</html>