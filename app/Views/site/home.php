<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GetNinjas - Encontre os melhores profissionais</title>
    <!-- Importação do W3.CSS e FontAwesome para ícones -->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .bg-conecta {
            background-color: #fcf8f2;
        }

        .hero-section {
            padding: 60px 16px;
        }
    </style>
</head>

<body>

    <!-- Barra de Navegação Superior (W3-Bar) -->
    <div class="w3-bar w3-dark-grey w3-padding w3-card">
        <div class="w3-content" style="max-width:1200px">
            <a href="<?= base_url('/') ?>" class="w3-bar-item w3-button w3-large w3-bold w3-hover-none w3-text-white">GetNinjas</a>
            <div class="w3-right">
                <a href="<?= base_url('login') ?>" class="w3-button w3-blue w3-round-large w3-card w3-hover-dark-blue">
                    <i class="fa-solid fa-right-to-bracket w3-margin-right"></i> Entrar
                </a>
            </div>
        </div>
    </div>

    <!-- Conteúdo Principal / Hero Banner -->
    <div class="w3-content hero-section" style="max-width:1100px;">

        <!-- Alerta de Sucesso -->
        <?php if (session()->getFlashdata('sucesso')) : ?>
            <div class="w3-panel w3-green w3-display-container w3-round w3-card w3-margin-bottom w3-left-align">
                <span onclick="this.parentElement.style.display='none'" class="w3-button w3-large w3-display-topright">&times;</span>
                <p><i class="fa-solid fa-circle-check w3-margin-right"></i> <?= session()->getFlashdata('sucesso') ?></p>
            </div>
        <?php endif; ?>

        <!-- Layout em Colunas: Texto na esquerda e Lista de Serviços na direita -->
        <div class="w3-row-padding w3-margin-top" style="display: flex; align-items: center; flex-wrap: wrap;">
            
            <!-- Coluna de Chamada (Esquerda) -->
            <div class="w3-half w3-container w3-left-align w3-margin-bottom">
                <h1 class="w3-xxxlarge w3-bold w3-text-dark-grey" style="line-height: 1.2;">
                    Encontre os melhores profissionais aqui
                </h1>
                <p class="w3-large w3-text-gray w3-margin-top">
                    Conectamos você a profissionais qualificados para qualquer serviço, com rapidez, segurança e facilidade.
                </p>

                <!-- Botões de Ação Separados -->
                <div class="w3-margin-top" style="padding-top: 15px;">
                    <a href="<?= base_url('usuario/cadastrar') ?>" class="w3-button w3-blue w3-padding-large w3-round-large w3-large w3-card w3-hover-dark-blue w3-margin-bottom">
                        <i class="fa-solid fa-user-plus w3-margin-right"></i> Quero Contratar (Cliente)
                    </a>
                    <a href="<?= base_url('profissional/cadastrar') ?>" class="w3-button w3-amber w3-text-dark-grey w3-padding-large w3-round-large w3-large w3-card w3-hover-orange">
                        <i class="fa-solid fa-briefcase w3-margin-right"></i> Quero Trabalhar (Profissional)
                    </a>
                </div>
            </div>

            <!-- Coluna de Serviços Mais Procurados (Direita) -->
            <div class="w3-half w3-container">
                <div class="w3-card-4 w3-white w3-round-large w3-padding-24 w3-border w3-border-light-gray">
                    
                    <div class="w3-container w3-border-bottom w3-padding-bottom w3-margin-bottom">
                        <h3 class="w3-bold w3-text-dark-grey w3-margin-none">
                            <i class="fa-solid fa-fire w3-text-orange w3-margin-right"></i>Serviços mais procurados
                        </h3>
                        <small class="w3-text-gray">Principais categorias atendidas na plataforma</small>
                    </div>

                    <div class="w3-row-padding">
                        <!-- Categoria 1 -->
                        <div class="w3-half w3-margin-bottom">
                            <div class="w3-padding w3-round bg-conecta w3-border">
                                <i class="fa-solid fa-bolt w3-text-amber w3-large w3-margin-right"></i>
                                <strong>Eletricista</strong>
                            </div>
                        </div>

                        <!-- Categoria 2 -->
                        <div class="w3-half w3-margin-bottom">
                            <div class="w3-padding w3-round bg-conecta w3-border">
                                <i class="fa-solid fa-faucet-drip w3-text-blue w3-large w3-margin-right"></i>
                                <strong>Encanador</strong>
                            </div>
                        </div>

                        <!-- Categoria 3 -->
                        <div class="w3-half w3-margin-bottom">
                            <div class="w3-padding w3-round bg-conecta w3-border">
                                <i class="fa-solid fa-paint-roller w3-text-purple w3-large w3-margin-right"></i>
                                <strong>Pintor</strong>
                            </div>
                        </div>

                        <!-- Categoria 4 -->
                        <div class="w3-half w3-margin-bottom">
                            <div class="w3-padding w3-round bg-conecta w3-border">
                                <i class="fa-solid fa-broom w3-text-teal w3-large w3-margin-right"></i>
                                <strong>Diarista / Limpeza</strong>
                            </div>
                        </div>

                        <!-- Categoria 5 -->
                        <div class="w3-half w3-margin-bottom">
                            <div class="w3-padding w3-round bg-conecta w3-border">
                                <i class="fa-solid fa-laptop-code w3-text-indigo w3-large w3-margin-right"></i>
                                <strong>Assistência Técnica</strong>
                            </div>
                        </div>

                        <!-- Categoria 6 -->
                        <div class="w3-half w3-margin-bottom">
                            <div class="w3-padding w3-round bg-conecta w3-border">
                                <i class="fa-solid fa-screwdriver-wrench w3-text-gray w3-large w3-margin-right"></i>
                                <strong>Marido de Aluguel</strong>
                            </div>
                        </div>
                    </div>

                    <div class="w3-container w3-center w3-margin-top-large">
                        <span class="w3-tag w3-light-grey w3-text-gray w3-round">
                            <i class="fa-solid fa-plus w3-margin-right"></i> E muitos outros serviços
                        </span>
                    </div>

                </div>
            </div>

        </div>

    </div>

    <!-- Rodapé Grupo ConectaDev -->
    <footer class="w3-container w3-center w3-padding-32 w3-border-top bg-conecta" style="margin-top: 80px;">
        <p class="w3-large w3-margin-bottom">
            <i class="fa-solid fa-code w3-text-amber"></i> Desenvolvido pelo <strong>Grupo ConectaDev</strong>
        </p>
        <p class="w3-small w3-text-gray">&copy; <?= date('Y') ?> GetNinjas - Todos os direitos reservados.</p>
    </footer>

</body>

</html>