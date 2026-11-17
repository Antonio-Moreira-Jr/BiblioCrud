<?php
use App\Controllers\AssuntoController;
use App\Controllers\DashboardController;
use App\Controllers\AutorController;
use App\Controllers\LivroController;
use App\Controllers\RelatorioController;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($uri) {
    case '/':
    case '/home':
        $dashboardController = new DashboardController();
        $dashboardController->index();
        break;
    
    case '/assuntos':
        $assuntoController = new AssuntoController();
        $assuntoController->index();
        break;

    case '/assuntos/inserir':
        $assuntoController = new AssuntoController();
        $assuntoController->inserirAssunto();
        break;

    case '/assuntos/atualizar':
        $assuntoController = new AssuntoController();
        $assuntoController->atualizarAssunto();
        break;

    case '/assuntos/excluir':
        $assuntoController = new AssuntoController();
        $assuntoController->excluirAssunto();
        break;

    case '/autores':
        $autorController = new AutorController();
        $autorController->index();
        break;

    case '/autores/inserir':
        $autorController = new AutorController();
        $autorController->inserir();
        break;

    case '/autores/atualizar':
        $autorController = new AutorController();
        $autorController->atualizar();
        break;

    case '/autores/excluir':
        $autorController = new AutorController();
        $autorController->excluir();
        break;
    
    case '/livros':
        $livroController = new LivroController();
        $livroController->index();
        break;

    case '/livros/inserir':
        $livroController = new LivroController();
        $livroController->inserir();
        break;

    case '/livros/atualizar':
        $livroController = new LivroController();
        $livroController->atualizar();
        break;

    case '/livros/excluir':
        $livroController = new LivroController();
        $livroController->excluir();
        break;

    case '/relatorios':
        $livroController = new RelatorioController();
        $livroController->index();
        break;

    case '/relatorios/pdf':
        $livroController = new RelatorioController();
        $livroController->pdf();
        break;

    default:
        http_response_code(404);
        require __DIR__ . '/src/views/404.php';
        break;
}