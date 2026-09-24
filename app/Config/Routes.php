<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\Home;
use App\Controllers\UsuarioController;
use App\Controllers\ProfissionalController;
use App\Controllers\AdminController;

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

// Rotas de Cadastro e Ativação do Profissional
$routes->get('profissional/cadastrar', [ProfissionalController::class, 'cadastrar']);
$routes->post('profissional/criar', [ProfissionalController::class, 'criar']);
$routes->get('profissional/ativarPerfil', [ProfissionalController::class, 'ativarPerfil']);
$routes->post('profissional/processarAtivacao', [ProfissionalController::class, 'processarAtivacao']);

// Painel / Dashboard do Profissional
$routes->get('profissional/ativar-perfil', [ProfissionalController::class, 'ativarPerfil']);
$routes->get('profissional/dashboard', [ProfissionalController::class, 'dashboard']);

// Rotas para alternar visualização entre os perfis
$routes->get('usuario/mudarParaCliente', [UsuarioController::class, 'mudarParaCliente']);
$routes->get('usuario/mudarParaProfissional', [UsuarioController::class, 'mudarParaProfissional']);
$routes->get('usuario/mudarParaAdmin', [UsuarioController::class, 'mudarParaAdmin']);

// --- Rotas Administrativas ---
$routes->group('admin', function($routes) {
    $routes->get('dashboard', 'AdminController::dashboard');
    
    // Rotas de Profissionais
    $routes->get('profissional/aprovar/(:num)', 'AdminController::aprovarProfissional/$1');
    $routes->post('profissional/solicitarAjustes/(:num)', 'AdminController::solicitarAjustes/$1');
    $routes->get('profissional/suspender/(:num)', 'AdminController::suspenderProfissional/$1');
    
    // Rota de Categorias
    $routes->post('categoria/criar', 'AdminController::criarCategoria');
});