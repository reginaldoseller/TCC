<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Profissional - GetNinjas</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/fontawesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
    </style>
</head>
<body>

    <!-- Topo -->
    <div class="w3-bar w3-dark-grey w3-padding w3-card">
        <div class="w3-content" style="max-width:1200px">
            <a href="<?= base_url('/') ?>" class="w3-bar-item w3-button w3-large w3-bold">GetNinjas</a>
            <a href="<?= base_url('meu-perfil') ?>" class="w3-bar-item w3-button w3-right"><i class="fa-solid fa-user"></i> Meu Perfil Geral</a>
        </div>
    </div>

    <div class="w3-content w3-padding-large" style="max-width:800px; margin-top:20px;">
        
        <h2 class="w3-bold"><i class="fa-solid fa-briefcase w3-text-blue"></i> Perfil Profissional</h2>
        <p class="w3-text-gray">Configure a sua apresentação comercial para os clientes da plataforma.</p>

        <!-- Mensagens -->
        <?php if (session()->getFlashdata('sucesso')) : ?>
            <div class="w3-panel w3-green w3-display-container w3-round w3-padding">
                <span onclick="this.parentElement.style.display='none'" class="w3-button w3-large w3-display-topright">&times;</span>
                <p class="w3-margin-none"><i class="fa-solid fa-circle-check w3-margin-right"></i> <?= session()->getFlashdata('sucesso') ?></p>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('erro')) : ?>
            <div class="w3-panel w3-red w3-display-container w3-round w3-padding">
                <span onclick="this.parentElement.style.display='none'" class="w3-button w3-large w3-display-topright">&times;</span>
                <p class="w3-margin-none"><i class="fa-solid fa-triangle-exclamation w3-margin-right"></i> <?= session()->getFlashdata('erro') ?></p>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('profissional/atualizar-perfil') ?>" method="post">
            <?= csrf_field() ?>

            <!-- Bloco 1: Informações Comerciais -->
            <div class="w3-card-4 w3-white w3-round-large w3-padding-24 w3-margin-bottom">
                <div class="w3-container">
                    <h4 class="w3-bold w3-border-bottom w3-padding-16"><i class="fa-solid fa-store"></i> Apresentação do Serviço</h4>
                    
                    <p>
                        <label class="w3-text-dark-grey"><b>Descrição dos seus Serviços / Biografia</b></label>
                        <textarea class="w3-input w3-border w3-round" name="descricao" rows="4" placeholder="Descreva a sua experiência, especialidades e diferenciais do seu atendimento..." required><?= old('descricao', $profissional['descricao'] ?? '') ?></textarea>
                    </p>

                    <div class="w3-row-padding">
                        <div class="w3-half w3-margin-top">
                            <label class="w3-text-dark-grey"><b>Raio de Atuação (em km)</b></label>
                            <input class="w3-input w3-border w3-round" type="number" name="raio_atuacao" value="<?= old('raio_atuacao', $profissional['raio_atuacao'] ?? 10) ?>" min="1" max="500" required>
                        </div>
                        <div class="w3-half w3-margin-top">
                            <label class="w3-text-dark-grey"><b>WhatsApp Comercial</b></label>
                            <input class="w3-input w3-border w3-round" type="text" name="whatsapp" value="<?= old('whatsapp', $links['whatsapp'] ?? '') ?>" placeholder="(00) 00000-0000">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bloco 2: Redes Sociais e Portfólio Externo -->
            <div class="w3-card-4 w3-white w3-round-large w3-padding-24 w3-margin-bottom">
                <div class="w3-container">
                    <h4 class="w3-bold w3-border-bottom w3-padding-16"><i class="fa-solid fa-share-nodes"></i> Redes Sociais & Links</h4>

                    <!-- Aviso de Aprovação -->
                    <div class="w3-panel w3-pale-blue w3-leftbar w3-border-blue w3-padding">
                        <p class="w3-small w3-margin-none">
                            <i class="fa-solid fa-circle-info w3-text-blue"></i> <b>Nota:</b> É possível preencher os seus links comerciais abaixo. Por motivos de segurança e qualidade do serviço, a exibição pública destas redes no seu perfil dependerá da validação da equipe de administração.
                        </p>
                    </div>

                    <p>
                        <label class="w3-text-dark-grey"><i class="fa-brands fa-instagram w3-text-purple"></i> <b>Instagram</b> (URL ou @usuario)</label>
                        <input class="w3-input w3-border w3-round" type="text" name="instagram" value="<?= old('instagram', $links['instagram'] ?? '') ?>" placeholder="https://instagram.com/seu_perfil">
                    </p>

                    <p>
                        <label class="w3-text-dark-grey"><i class="fa-brands fa-facebook w3-text-blue"></i> <b>Facebook</b> (URL do perfil/página)</label>
                        <input class="w3-input w3-border w3-round" type="text" name="facebook" value="<?= old('facebook', $links['facebook'] ?? '') ?>" placeholder="https://facebook.com/sua_pagina">
                    </p>

                    <p>
                        <label class="w3-text-dark-grey"><i class="fa-brands fa-linkedin w3-text-indigo"></i> <b>LinkedIn</b></label>
                        <input class="w3-input w3-border w3-round" type="text" name="linkedin" value="<?= old('linkedin', $links['linkedin'] ?? '') ?>" placeholder="https://linkedin.com/in/seu_perfil">
                    </p>
                </div>
            </div>

            <!-- Botão Salvar -->
            <div class="w3-margin-top w3-margin-bottom">
                <button type="submit" class="w3-button w3-blue w3-round-large w3-large w3-card w3-right">
                    <i class="fa-solid fa-floppy-disk w3-margin-right"></i> Salvar Perfil Profissional
                </button>
            </div>
        </form>
    </div>

</body>
</html>