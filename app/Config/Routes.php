<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\Home;
use App\Controllers\UsuarioController;
use App\Controllers\ProfissionalController;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', [Home::class, 'index']);

// Rotas de Autenticação / Login
$routes->get('login', [UsuarioController::class, 'login']);
$routes->post('login/autenticar', [UsuarioController::class, 'autenticar']);
$routes->get('logout', [UsuarioController::class, 'logout']);

// Rotas de Cadastro de Usuário
$routes->get('usuario/cadastrar', [UsuarioController::class, 'novo']);
$routes->post('usuario/criar', [UsuarioController::class, 'criar']);

// Rota da Área do Cliente
$routes->get('cliente/dashboard', [UsuarioController::class, 'dashboard']);

// Rotas de Profissional
$routes->get('profissional/cadastrar', [ProfissionalController::class, 'cadastrar']);
$routes->post('profissional/criar', [ProfissionalController::class, 'criar']);

// Rota para exibir a tela de ativação de perfil
$routes->get('profissional/ativar', [ProfissionalController::class, 'ativarPerfil']);
$routes->get('profissional/ativar-perfil', [ProfissionalController::class, 'ativarPerfil']); // Alias

// Rota para processar o formulário de ativação
$routes->post('profissional/ativar', [ProfissionalController::class, 'processarAtivacao']);
$routes->post('profissional/processarAtivacao', [ProfissionalController::class, 'processarAtivacao']); // Alias

// Painel / Dashboard do Profissional
$routes->get('profissional/dashboard', [ProfissionalController::class, 'dashboard']);

// Rotas para alternar visualização do perfil ativo
$routes->get('usuario/mudar-cliente', [UsuarioController::class, 'mudarParaCliente']);
$routes->get('usuario/mudar-profissional', [UsuarioController::class, 'mudarParaProfissional']);