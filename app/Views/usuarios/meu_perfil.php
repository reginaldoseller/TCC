<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - GetNinjas</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .bg-conecta { background-color: #fcf8f2; }
    </style>
</head>
<body>

    <!-- Topo -->
    <div class="w3-bar w3-dark-grey w3-padding w3-card">
        <div class="w3-content" style="max-width:1200px">
            <a href="<?= base_url('/') ?>" class="w3-bar-item w3-button w3-large w3-bold">GetNinjas</a>
            <a href="<?= base_url('cliente/dashboard') ?>" class="w3-bar-item w3-button w3-right"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        </div>
    </div>

    <div class="w3-content w3-padding-large" style="max-width:800px; margin-top:20px;">
        
        <h2 class="w3-bold"><i class="fa-solid fa-user-gear w3-text-blue"></i> Meu Perfil</h2>
        <p class="w3-text-gray">Atualize as suas informações pessoais e credenciais de acesso.</p>

        <!-- Mensagens de Sucesso ou Erro -->
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

        <form action="<?= base_url('usuario/atualizar-perfil') ?>" method="post">
            <?= csrf_field() ?>

            <!-- Bloco 1: Dados Pessoais -->
            <div class="w3-card-4 w3-white w3-round-large w3-padding-24 w3-margin-bottom">
                <div class="w3-container">
                    <h4 class="w3-bold w3-border-bottom w3-padding-16"><i class="fa-solid fa-id-card"></i> Dados Pessoais</h4>
                    
                    <div class="w3-row-padding">
                        <div class="w3-half w3-margin-top">
                            <label class="w3-text-dark-grey"><b>Nome Completo</b></label>
                            <input class="w3-input w3-border w3-round" type="text" name="nome" value="<?= old('nome', $usuario['nome']) ?>" required>
                        </div>
                        <div class="w3-half w3-margin-top">
                            <label class="w3-text-dark-grey"><b>E-mail</b></label>
                            <input class="w3-input w3-border w3-round" type="email" name="email" value="<?= old('email', $usuario['email']) ?>" required>
                        </div>
                    </div>

                    <div class="w3-row-padding w3-margin-top">
                        <div class="w3-half">
                            <label class="w3-text-dark-grey"><b>CPF</b> (Apenas leitura)</label>
                            <input class="w3-input w3-border w3-round w3-light-grey" type="text" value="<?= esc($usuario['cpf']) ?>" disabled>
                        </div>
                        <div class="w3-half">
                            <label class="w3-text-dark-grey"><b>Telefone Principal</b></label>
                            <input class="w3-input w3-border w3-round" type="text" name="telefone" value="<?= old('telefone', $telefone) ?>" placeholder="(00) 00000-0000">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bloco 2: Endereço -->
            <div class="w3-card-4 w3-white w3-round-large w3-padding-24 w3-margin-bottom">
                <div class="w3-container">
                    <h4 class="w3-bold w3-border-bottom w3-padding-16"><i class="fa-solid fa-location-dot"></i> Endereço</h4>

                    <div class="w3-row-padding">
                        <div class="w3-third w3-margin-top">
                            <label class="w3-text-dark-grey"><b>CEP</b></label>
                            <input class="w3-input w3-border w3-round" type="text" name="cep" value="<?= old('cep', $usuario['cep']) ?>">
                        </div>
                        <div class="w3-third w3-margin-top">
                            <label class="w3-text-dark-grey"><b>Cidade</b></label>
                            <input class="w3-input w3-border w3-round" type="text" name="cidade" value="<?= old('cidade', $usuario['cidade']) ?>">
                        </div>
                        <div class="w3-third w3-margin-top">
                            <label class="w3-text-dark-grey"><b>Estado (UF)</b></label>
                            <input class="w3-input w3-border w3-round" type="text" name="estado" maxlength="2" value="<?= old('estado', $usuario['estado']) ?>">
                        </div>
                    </div>

                    <p class="w3-margin-top">
                        <label class="w3-text-dark-grey"><b>Bairro</b></label>
                        <input class="w3-input w3-border w3-round" type="text" name="bairro" value="<?= old('bairro', $usuario['bairro']) ?>">
                    </p>
                </div>
            </div>

            <!-- Bloco 3: Alteração de Senha (Opcional) -->
            <div class="w3-card-4 w3-white w3-round-large w3-padding-24 w3-margin-bottom">
                <div class="w3-container">
                    <h4 class="w3-bold w3-border-bottom w3-padding-16"><i class="fa-solid fa-lock"></i> Alterar Senha <span class="w3-small w3-text-gray">(Preencha apenas se desejar alterar)</span></h4>

                    <p>
                        <label class="w3-text-dark-grey"><b>Senha Atual</b></label>
                        <input class="w3-input w3-border w3-round" type="password" name="senha_atual" placeholder="Digite a senha atual para confirmar a troca">
                    </p>

                    <div class="w3-row-padding">
                        <div class="w3-half">
                            <label class="w3-text-dark-grey"><b>Nova Senha</b></label>
                            <input class="w3-input w3-border w3-round" type="password" name="nova_senha" minlength="6" placeholder="Mínimo 6 caracteres">
                        </div>
                        <div class="w3-half">
                            <label class="w3-text-dark-grey"><b>Confirmar Nova Senha</b></label>
                            <input class="w3-input w3-border w3-round" type="password" name="confirma_nova_senha" minlength="6" placeholder="Repita a nova senha">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botão Salvar -->
            <div class="w3-margin-top w3-margin-bottom">
                <button type="submit" class="w3-button w3-blue w3-round-large w3-large w3-card w3-right">
                    <i class="fa-solid fa-floppy-disk w3-margin-right"></i> Salvar Alterações
                </button>
            </div>
        </form>
    </div>

</body>
</html>