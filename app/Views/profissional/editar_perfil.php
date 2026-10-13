<?php
$perfilAtivo = session()->get('perfil_ativo') ?? 'Cliente';
$isAdmin     = (bool) session()->get('is_admin');

// Define dinamicamente para qual painel o botão "Voltar" deve apontar
$dashboardUrl = site_url('cliente/dashboard');
if ($perfilAtivo === 'Administrador' || ($isAdmin && $perfilAtivo === 'Administrador')) {
    $dashboardUrl = site_url('admin/dashboard');
} elseif ($perfilAtivo === 'Profissional' || session()->get('tipo_perfil') === 'Profissional') {
    $dashboardUrl = site_url('profissional/dashboard');
}
?>
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
        <div class="w3-content" style="max-width:1200px; display: flex; align-items: center; justify-content: space-between;">
            <a href="<?= $dashboardUrl ?>" class="w3-bar-item w3-button w3-large w3-bold" style="text-decoration: none;">GetNinjas</a>
            <div style="display: flex; gap: 10px; align-items: center;">
                <a href="<?= $dashboardUrl ?>" class="w3-button w3-light-grey w3-round w3-small" style="text-decoration: none;">
                    <i class="fa-solid fa-arrow-left"></i> Voltar ao Painel
                </a>
                <a href="<?= base_url('meu-perfil') ?>" class="w3-button w3-dark-grey w3-border w3-border-white w3-round w3-small" style="text-decoration: none;">
                    <i class="fa-solid fa-user"></i> Meu Perfil Geral
                </a>
            </div>
        </div>
    </div>

    <div class="w3-content w3-padding-large" style="max-width:800px; margin-top:20px;">
        
        <h2 class="w3-bold"><i class="fa-solid fa-briefcase w3-text-blue"></i> Perfil Profissional</h2>
        <p class="w3-text-gray">Configure a sua apresentação comercial e a sua região de atendimento para os clientes.</p>

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
                            <input class="w3-input w3-border w3-round" type="number" name="raio_atuacao" value="<?= old('raio_atuacao', $profissional['raio_atendimento_km'] ?? 10) ?>" min="1" max="500" required>
                        </div>
                        <div class="w3-half w3-margin-top">
                            <label class="w3-text-dark-grey"><b>WhatsApp Comercial</b></label>
                            <input class="w3-input w3-border w3-round" type="text" id="whatsapp" name="whatsapp" value="<?= old('whatsapp', $links['whatsapp'] ?? '') ?>" placeholder="(00) 00000-0000" maxlength="15">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bloco Novo: Localização Base para Matching -->
            <div class="w3-card-4 w3-white w3-round-large w3-padding-24 w3-margin-bottom">
                <div class="w3-container">
                    <h4 class="w3-bold w3-border-bottom w3-padding-16"><i class="fa-solid fa-location-dot"></i> Região de Atendimento (Geolocalização)</h4>
                    <p class="w3-small w3-text-grey">Informe o CEP de onde você baseia o seu atendimento. Não armazenamos o número exato do seu endereço para garantir sua privacidade.</p>

                    <div class="w3-row-padding">
                        <div class="w3-third w3-margin-top">
                            <label class="w3-text-dark-grey"><b>CEP Base</b></label>
                            <input class="w3-input w3-border w3-round" type="text" id="cep" name="cep" value="<?= old('cep', $profissional['cep'] ?? '') ?>" placeholder="00000-000" maxlength="9" required>
                        </div>
                        <div class="w3-twothird w3-margin-top">
                            <label class="w3-text-dark-grey"><b>Bairro</b></label>
                            <input class="w3-input w3-border w3-round" type="text" id="bairro" name="bairro" value="<?= old('bairro', $profissional['bairro'] ?? '') ?>" readonly required>
                        </div>
                    </div>

                    <div class="w3-row-padding w3-margin-top">
                        <div class="w3-threequarter">
                            <label class="w3-text-dark-grey"><b>Cidade</b></label>
                            <input class="w3-input w3-border w3-round" type="text" id="cidade" name="cidade" value="<?= old('cidade', $profissional['cidade'] ?? '') ?>" readonly required>
                        </div>
                        <div class="w3-quarter">
                            <label class="w3-text-dark-grey"><b>Estado (UF)</b></label>
                            <input class="w3-input w3-border w3-round" type="text" id="estado" name="estado" value="<?= old('estado', $profissional['estado'] ?? '') ?>" readonly required>
                        </div>
                    </div>

                    <!-- Opção de Restrição por Cidade -->
                    <div class="w3-margin-top">
                        <label>
                            <input class="w3-check" type="checkbox" name="apenas_mesma_cidade" value="1" <?= old('apenas_mesma_cidade', $profissional['apenas_mesma_cidade'] ?? 0) ? 'checked' : '' ?>>
                            <b>Restringir atendimento apenas aos limites da minha cidade</b> (Não atender cidades vizinhas, mesmo dentro do raio em km)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Bloco 2: Redes Sociais e Portfólio Externo (Exibido condicionalmente) -->
            <?php if (!empty($profissional['permite_redes_sociais']) && $profissional['permite_redes_sociais'] == 1) : ?>
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
            <?php endif; ?>

            <!-- Botões de Ação Rodapé -->
            <div class="w3-margin-top w3-margin-bottom" style="display: flex; justify-content: space-between; align-items: center;">
                <a href="<?= $dashboardUrl ?>" class="w3-button w3-light-grey w3-border w3-round-large w3-large w3-card">
                    <i class="fa-solid fa-arrow-left w3-margin-right"></i> Cancelar e Voltar
                </a>
                <button type="submit" class="w3-button w3-blue w3-round-large w3-large w3-card">
                    <i class="fa-solid fa-floppy-disk w3-margin-right"></i> Salvar Perfil Profissional
                </button>
            </div>
        </form>
    </div>

    <!-- Scripts de Máscara e Consulta de CEP via AJAX -->
    <script>
    // Máscara para WhatsApp
    document.getElementById('whatsapp').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove tudo que não for dígito
        if (value.length > 11) {
            value = value.substring(0, 11); // Limita a 11 dígitos
        }
        
        // Aplica a formatação de acordo com o tamanho
        if (value.length > 10) {
            value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
        } else if (value.length > 6) {
            value = value.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
        } else if (value.length > 2) {
            value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
        } else if (value.length > 0) {
            value = value.replace(/^(\d*)/, '($1');
        }
        
        e.target.value = value;
    });

    // Máscara e Automação de CEP
    document.getElementById('cep').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 8) value = value.substring(0, 8);
        
        let valorFormatado = value;
        if (value.length > 5) {
            valorFormatado = value.replace(/^(\d{5})(\d{0,3})/, '$1-$2');
        }
        e.target.value = valorFormatado;

        // Quando o CEP estiver completo (8 dígitos), dispara a busca via API
        if (value.length === 8) {
            fetch('<?= base_url('api/consultar-cep') ?>?cep=' + value)
                .then(response => response.json())
                .then(data => {
                    if (data && !data.erro) {
                        document.getElementById('bairro').value = data.bairro || '';
                        document.getElementById('cidade').value = data.cidade || '';
                        document.getElementById('estado').value = data.estado || '';
                    } else {
                        alert('CEP não encontrado.');
                    }
                })
                .catch(error => console.error('Erro ao consultar CEP:', error));
        }
    });
    </script>

</body>
</html>