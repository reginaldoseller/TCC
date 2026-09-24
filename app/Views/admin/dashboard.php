<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - GetNinjas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #fcfbfa;
        }

        .navbar-custom {
            background-color: #3d3a37 !important;
        }

        .card-custom {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .border-ocre {
            border-left-color: #d8be92 !important;
        }

        .nav-pills .nav-link.active {
            background-color: #5c554e;
            color: #ffffff;
        }

        .nav-pills .nav-link {
            color: #6c635b;
            font-weight: 500;
        }

        .nav-pills .nav-link:hover {
            color: #3d3a37;
        }

        .btn-ocre {
            background-color: #d8be92;
            color: #3d3a37;
            border: none;
            font-weight: 600;
        }

        .btn-ocre:hover {
            background-color: #c9ad7f;
            color: #2b2826;
        }
    </style>
</head>

<body>

    <!-- Header Admin -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom px-4 mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= site_url('admin/dashboard') ?>">
                <i class="bi bi-shield-lock me-2"></i>Painel Administrativo
            </a>

            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-badge me-1"></i> Alternar Perfil
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li>
                            <a class="dropdown-item fw-bold text-dark" href="<?= site_url('usuario/mudarParaAdmin') ?>">
                                <i class="bi bi-shield-lock me-2 text-warning"></i>Modo Administrador
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= site_url('usuario/mudarParaCliente') ?>">
                                <i class="bi bi-person me-2"></i>Modo Cliente
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= site_url('usuario/mudarParaProfissional') ?>">
                                <i class="bi bi-briefcase me-2"></i>Modo Profissional
                            </a>
                        </li>
                    </ul>
                </div>

                <span class="text-white-50 small">Administrador</span>
                <a href="<?= site_url('logout') ?>" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-2">

        <!-- Alertas -->
        <?php if (session()->getFlashdata('sucesso')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i> <?= session()->getFlashdata('sucesso') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('erro')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i> <?= session()->getFlashdata('erro') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('aviso')): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-1"></i> <?= session()->getFlashdata('aviso') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Métricas Rápidas -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card card-custom p-3 bg-white border-start border-4 border-ocre">
                    <span class="text-muted small">Análises Pendentes</span>
                    <h3 class="mb-0 text-dark"><?= count($profissionaisPendentes ?? []) ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom p-3 bg-white border-start border-4 border-primary">
                    <span class="text-muted small">Total de Usuários</span>
                    <h3 class="mb-0 text-dark"><?= count($usuarios ?? []) ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom p-3 bg-white border-start border-4 border-success">
                    <span class="text-muted small">Categorias Ativas</span>
                    <h3 class="mb-0 text-dark"><?= count($categorias ?? []) ?></h3>
                </div>
            </div>
        </div>

        <!-- Abas Administrativas -->
        <div class="card card-custom bg-white p-4">
            <ul class="nav nav-pills mb-4" id="admin-tabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="tab-pendentes" data-bs-toggle="pill" data-bs-target="#content-pendentes" type="button">
                        <i class="bi bi-person-check me-1"></i> Aprov. Profissionais (<?= count($profissionaisPendentes ?? []) ?>)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="tab-usuarios" data-bs-toggle="pill" data-bs-target="#content-usuarios" type="button">
                        <i class="bi bi-people me-1"></i> Usuários
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="tab-categorias" data-bs-toggle="pill" data-bs-target="#content-categorias" type="button">
                        <i class="bi bi-tags me-1"></i> Categorias
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="admin-tabContent">

                <!-- Aba 1: Profissionais Pendentes -->
                <div class="tab-pane fade show active" id="content-pendentes">
                    <h5 class="fw-bold mb-3 text-dark">Solicitações de Perfil Profissional</h5>
                    <?php if (empty($profissionaisPendentes)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-check2-circle fs-1"></i>
                            <p class="mt-2 mb-0">Nenhuma solicitação pendente no momento.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID Prof.</th>
                                        <th>Nome</th>
                                        <th>E-mail</th>
                                        <th>Data Solicitação</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($profissionaisPendentes as $prof): ?>
                                        <?php $profId = $prof['profissional_id'] ?? $prof['id']; ?>
                                        <tr>
                                            <td>#<?= $profId ?? '-' ?></td>
                                            <td><strong><?= $prof['nome'] ?? 'Sem nome' ?></strong></td>
                                            <td><?= $prof['email'] ?? '-' ?></td>
                                            <td><?= !empty($prof['dtCadastro']) ? date('d/m/Y H:i', strtotime($prof['dtCadastro'])) : '-' ?></td>
                                            <td class="text-end">
                                                <!-- Botão Aprovar -->
                                                <a href="<?= site_url('admin/profissional/aprovar/' . $profId) ?>" class="btn btn-success btn-sm">
                                                    <i class="bi bi-check-lg"></i> Aprovar
                                                </a>

                                                <!-- Botão Solicitar Ajustes (Abre Modal) -->
                                                <button type="button" class="btn btn-warning btn-sm text-dark" data-bs-toggle="modal" data-bs-target="#modalAjustes<?= $profId ?>">
                                                    <i class="bi bi-pencil-square"></i> Solicitar Ajustes
                                                </button>

                                                <!-- Botão Suspender Adesão (Sem usar termo rejeitar) -->
                                                <a href="<?= site_url('admin/profissional/suspender/' . $profId) ?>" class="btn btn-secondary btn-sm" onclick="return confirm('Confirma a suspensão temporária da adesão deste perfil?')">
                                                    <i class="bi bi-slash-circle"></i> Suspender
                                                </a>
                                            </td>
                                        </tr>

                                        <!-- Modal para Solicitar Ajustes de Dados -->
                                        <div class="modal fade" id="modalAjustes<?= $profId ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form action="<?= site_url('admin/profissional/solicitarAjustes/' . $profId) ?>" method="post">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold">Orientação de Ajustes</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            <p class="small text-muted mb-2">
                                                                Digite abaixo quais dados ou informações o profissional <strong><?= $prof['nome'] ?? '' ?></strong> precisa corrigir para regularizar a solicitação:
                                                            </p>
                                                            <div class="mb-3">
                                                                <textarea class="form-control" name="observacao" rows="4" placeholder="Ex: A foto do documento está ilegível ou a descrição do perfil necessita de mais detalhes." required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-warning">Enviar Orientação</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Aba 2: Lista de Usuários -->
                <div class="tab-pane fade" id="content-usuarios">
                    <h5 class="fw-bold mb-3 text-dark">Usuários Cadastrados</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Data Cadastro</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($usuarios)): ?>
                                    <?php foreach ($usuarios as $usr): ?>
                                        <tr>
                                            <td>#<?= $usr['id'] ?? '-' ?></td>
                                            <td><strong><?= $usr['nome'] ?? 'Sem nome' ?></strong></td>
                                            <td><?= $usr['email'] ?? '-' ?></td>
                                            <td><?= !empty($usr['dtCadastro']) ? date('d/m/Y', strtotime($usr['dtCadastro'])) : '-' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Nenhum usuário cadastrado.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Aba 3: Cadastro de Categorias -->
                <div class="tab-pane fade" id="content-categorias">
                    <div class="row g-4">
                        <div class="col-md-5">
                            <div class="card border-0 bg-light p-3">
                                <h6 class="fw-bold mb-3 text-dark">Nova Categoria</h6>
                                <form action="<?= site_url('admin/categoria/criar') ?>" method="post">
                                    <div class="mb-3">
                                        <label for="nome" class="form-label text-secondary small">Nome da Categoria</label>
                                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Ex: Reformas, Assistência Técnica" required>
                                    </div>
                                    <button type="submit" class="btn btn-ocre w-100">
                                        <i class="bi bi-plus-circle me-1"></i> Cadastrar Categoria
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <h6 class="fw-bold mb-3 text-dark">Categorias Existentes</h6>
                            <ul class="list-group">
                                <?php if (!empty($categorias)): ?>
                                    <?php foreach ($categorias as $cat): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-tag me-2 text-muted"></i><?= $cat['categoria'] ?? $cat['nome'] ?? 'Sem nome' ?></span>
                                            <span class="badge bg-light text-dark border">#<?= $cat['id'] ?? '-' ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="list-group-item text-muted text-center py-3">Nenhuma categoria cadastrada.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>