<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Cliente - GetNinjas</title>

    <!-- Bootstrap 5 & Ícones -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body { 
            background-color: #f8f9fa; 
            min-height: 100vh; 
            display: flex; 
            flex-direction: column; 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        /* Container central que expande para empurrar o rodapé */
        .main-content {
            flex: 1 0 auto;
        }

        .card-dash { 
            border: none; 
            border-radius: 10px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); 
            transition: transform 0.2s ease, box-shadow 0.2s ease; 
        }

        .card-dash:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.08); 
        }

        .border-ocre { 
            border-left: 4px solid #d9a74a !important; 
        }

        .bg-ocre-soft { 
            background-color: #fdfaf3; 
            color: #8c6b23; 
        }
    </style>
</head>

<body>

    <?php
    $usuarioId = session()->get('id');
    $isAdmin   = (bool) session()->get('is_admin');

    $db = \Config\Database::connect();

    $dadosUsuario = $db->table('usuario')->where('id', $usuarioId)->get()->getRowArray();
    $statusUsuario       = $dadosUsuario['status'] ?? 'ativo';
    $motivoBloqueio      = $dadosUsuario['motivo_bloqueio'] ?? '';
    $usuarioBloqueadoAte = $dadosUsuario['bloqueado_ate'] ?? '';

    $dadosProfissional = $db->table('profissional')->where('usuario_id', $usuarioId)->get()->getRowArray();
    $temCadastroProfissional = !empty($dadosProfissional);
    $statusProfissional      = $temCadastroProfissional ? ($dadosProfissional['status'] ?? '') : '';
    $observacaoAdmin         = $temCadastroProfissional ? ($dadosProfissional['observacao_admin'] ?? '') : '';
    $bloqueadoAte             = $temCadastroProfissional ? ($dadosProfissional['bloqueado_ate'] ?? '') : '';

    $ehProfissionalAtivo        = ($temCadastroProfissional && $statusProfissional === 'ativo') || $isAdmin;
    $ehProfissionalEmAnalise    = $temCadastroProfissional && ($statusProfissional === 'em_analise' || $statusProfissional === 'pendente');
    $ehProfissionalAjustes      = $temCadastroProfissional && ($statusProfissional === 'ajustes_solicitados');
    $ehProfissionalIndisponivel = $temCadastroProfissional && ($statusProfissional === 'indisponivel');
    ?>

    <!-- Header / Navbar Global Reutilizada -->
    <?= view('components/navbar') ?>

    <!-- Conteúdo Principal -->
    <div class="main-content container py-4">

        <!-- Mensagens de Alerta Flashdata Reutilizadas -->
        <?= view('components/alerts') ?>

        <!-- Alertas Dinâmicos de Status da Conta / Profissional -->
        <?= view('cliente/components/alertas_status', compact('statusUsuario', 'motivoBloqueio', 'usuarioBloqueadoAte', 'ehProfissionalEmAnalise', 'ehProfissionalAjustes', 'observacaoAdmin', 'ehProfissionalIndisponivel', 'bloqueadoAte')) ?>

        <!-- Cabeçalho do Painel -->
        <?= view('cliente/components/cabecalho', compact('isAdmin', 'ehProfissionalAtivo', 'ehProfissionalEmAnalise', 'ehProfissionalAjustes', 'ehProfissionalIndisponivel')) ?>

        <!-- Cards com as Opções do Cliente -->
        <?= view('cliente/components/cards_opcoes', compact('statusUsuario')) ?>

    </div>

    <!-- Rodapé Reutilizado -->
    <?= view('components/footer') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>