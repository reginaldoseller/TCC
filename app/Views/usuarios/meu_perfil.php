<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - GetNinjas</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        html, body { height: 100%; margin: 0; }
        body { 
            background-color: #f8f9fa; 
            display: flex; 
            flex-direction: column; 
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .main-content { flex: 1 0 auto; }
        .bg-conecta { background-color: #fcf8f2; }
    </style>
</head>
<body>

    <?php
    // Define a URL de retorno dinâmica com base no perfil ativo na sessão
    $perfilAtivo = session()->get('perfil_ativo') ?? 'Cliente';
    $urlVoltar   = site_url('cliente/dashboard');

    if ($perfilAtivo === 'Administrador') {
        $urlVoltar = site_url('admin/dashboard');
    } elseif ($perfilAtivo === 'Profissional') {
        $urlVoltar = site_url('profissional/dashboard');
    }
    ?>

    <!-- Header / Navbar Global Reutilizada -->
    <?= view('components/navbar') ?>

    <div class="main-content w3-content w3-padding-large" style="max-width:800px; margin-top:20px;">
        
        <!-- Cabeçalho com Botão de Voltar -->
        <div class="w3-row w3-margin-bottom" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div>
                <h2 class="w3-bold w3-margin-none"><i class="fa-solid fa-user-gear w3-text-blue me-2"></i>Meu Perfil</h2>
                <p class="w3-text-gray w3-margin-none" style="margin-top: 4px !important;">Atualize as suas informações pessoais e credenciais de acesso.</p>
            </div>
            <div>
                <a href="<?= $urlVoltar ?>" class="w3-button w3-white w3-border w3-border-grey w3-round-large w3-bold w3-hover-light-grey" style="text-decoration: none;">
                    <i class="fa-solid fa-arrow-left me-2"></i>Voltar ao Painel
                </a>
            </div>
        </div>

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
                            <input class="w3-input w3-border w3-round" type="text" name="nome" value="<?= old('nome', $usuario['nome'] ?? '') ?>" required>
                        </div>
                        <div class="w3-half w3-margin-top">
                            <label class="w3-text-dark-grey"><b>E-mail</b></label>
                            <input class="w3-input w3-border w3-round" type="email" name="email" value="<?= old('email', $usuario['email'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="w3-row-padding w3-margin-top">
                        <div class="w3-half">
                            <label class="w3-text-dark-grey"><b>CPF</b> (Apenas leitura)</label>
                            <input id="cpf" class="w3-input w3-border w3-round w3-light-grey" type="text" value="<?= esc($usuario['cpf'] ?? '') ?>" disabled>
                        </div>
                        <div class="w3-half">
                            <label class="w3-text-dark-grey"><b>Telefone Principal</b></label>
                            <input id="telefone" class="w3-input w3-border w3-round" type="text" name="telefone" value="<?= old('telefone', $telefone ?? '') ?>" placeholder="(00) 00000-0000" maxlength="15">
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
                            <input id="cep" class="w3-input w3-border w3-round" type="text" name="cep" value="<?= old('cep', $usuario['cep'] ?? '') ?>" placeholder="00000-000" maxlength="9">
                        </div>
                        <div class="w3-third w3-margin-top">
                            <label class="w3-text-dark-grey"><b>Cidade</b></label>
                            <input class="w3-input w3-border w3-round" type="text" name="cidade" value="<?= old('cidade', $usuario['cidade'] ?? '') ?>">
                        </div>
                        <div class="w3-third w3-margin-top">
                            <label class="w3-text-dark-grey"><b>Estado (UF)</b></label>
                            <input class="w3-input w3-border w3-round" type="text" name="estado" maxlength="2" value="<?= old('estado', $usuario['estado'] ?? '') ?>">
                        </div>
                    </div>

                    <p class="w3-margin-top">
                        <label class="w3-text-dark-grey"><b>Bairro</b></label>
                        <input class="w3-input w3-border w3-round" type="text" name="bairro" value="<?= old('bairro', $usuario['bairro'] ?? '') ?>">
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

            <!-- Ações do Formulário -->
            <div class="w3-margin-top w3-margin-bottom" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <a href="<?= $urlVoltar ?>" class="w3-button w3-white w3-border w3-border-grey w3-round-large w3-bold w3-hover-light-grey" style="text-decoration: none;">
                    <i class="fa-solid fa-arrow-left me-2"></i>Cancelar e Voltar
                </a>

                <button type="submit" class="w3-button w3-blue w3-round-large w3-large w3-card">
                    <i class="fa-solid fa-floppy-disk w3-margin-right"></i> Salvar Alterações
                </button>
            </div>
        </form>
    </div>

    <!-- Rodapé Global Reutilizado -->
    <?= view('components/footer') ?>

    <!-- JavaScript das Máscaras Automáticas -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            
            function formatarCPF(v) {
                v = v.replace(/\D/g, '');
                if (v.length > 11) v = v.substring(0, 11);
                v = v.replace(/(\d{3})(\d)/, "$1.$2");
                v = v.replace(/(\d{3})(\d)/, "$1.$2");
                v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
                return v;
            }

            function formatarTelefone(v) {
                v = v.replace(/\D/g, '');
                if (v.length > 11) v = v.substring(0, 11);
                if (v.length > 10) {
                    return v.replace(/^(\d{2})(\d{5})(\d{4})$/, "($1) $2-$3");
                } else if (v.length > 6) {
                    return v.replace(/^(\d{2})(\d{4})(\d{0,4})$/, "($1) $2-$3");
                } else if (v.length > 2) {
                    return v.replace(/^(\d{2})(\d{0,5})$/, "($1) $2");
                } else if (v.length > 0) {
                    return v.replace(/^(\d{0,2})$/, "($1");
                }
                return v;
            }

            function formatarCEP(v) {
                v = v.replace(/\D/g, '');
                if (v.length > 8) v = v.substring(0, 8);
                return v.replace(/^(\d{5})(\d)/, "$1-$2");
            }

            const elCpf = document.getElementById('cpf');
            const elTel = document.getElementById('telefone');
            const elCep = document.getElementById('cep');

            if (elCpf && elCpf.value) elCpf.value = formatarCPF(elCpf.value);
            if (elTel && elTel.value) elTel.value = formatarTelefone(elTel.value);
            if (elCep && elCep.value) elCep.value = formatarCEP(elCep.value);

            if (elCpf) elCpf.addEventListener('input', e => e.target.value = formatarCPF(e.target.value));
            if (elTel) elTel.addEventListener('input', e => e.target.value = formatarTelefone(e.target.value));
            if (elCep) elCep.addEventListener('input', e => e.target.value = formatarCEP(e.target.value));
        });
    </script>
</body>
</html>