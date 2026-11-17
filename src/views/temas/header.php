<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Bibliotecário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body { min-height: 100vh; overflow-x: hidden; background-color: #f8f9fa; }
        #sidebar { width: 260px; min-height: 100vh; background-color: #212529 !important; color: #fff; }
        #sidebar .nav-link { color: #adb5bd; padding: 12px 20px; font-weight: 500; border-left: 4px solid transparent; }
        #sidebar .nav-link:hover, #sidebar .nav-link.active { color: #fff; background-color: #343a40; border-left-color: #0d6efd; }
        #content { min-width: 0; }
        .navbar-top { background-color: #ffffff; border-bottom: 1px solid #dee2e6; }
        @media (max-width: 767.98px) {
            #sidebar { width: 260px; }
        }
    </style>
</head>
<body>

<div class="d-flex">
    <nav id="sidebar" class="offcanvas-md offcanvas-start text-bg-dark flex-shrink-0 p-3" tabindex="-1">
        <div class="offcanvas-header p-0 mb-3 d-md-none">
            <a href="/" class="d-flex align-items-center text-white text-decoration-none">
                <i class="bi bi-book-half fs-4 me-2 text-primary"></i>
                <span class="fs-5 fw-bold">BiblioCrud</span>
            </a>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Fechar"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column p-0 w-100 h-100">
            <a href="/" class="d-none d-md-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <i class="bi bi-book-half fs-4 me-2 text-primary"></i>
                <span class="fs-5 fw-bold">BiblioCrud</span>
            </a>
            <hr class="text-secondary">
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item mb-1">
                    <a href="/" class="nav-link"><i class="bi bi-speedometer2 me-2"></i> Início</a>
                </li>
                <li class="nav-item mb-1">
                    <a href="/autores" class="nav-link"><i class="bi bi-person-lines-fill me-2"></i>Autor</a>
                </li>
                <li class="nav-item mb-1">
                    <a href="/assuntos" class="nav-link"><i class="bi bi-tags-fill me-2"></i>Assunto</a>
                </li>
                <li class="nav-item mb-1">
                    <a href="/livros" class="nav-link"><i class="bi bi-journal-bookmark-fill me-2"></i>Livro</a>
                </li>
                <li class="nav-item mb-1">
                    <a href="/relatorios" class="nav-link"><i class="bi bi-file-earmark-bar-graph-fill me-2"></i> Relatórios </a>
                </li>
            </ul>
            <hr class="text-secondary">
            <div class="text-center text-muted small">
                Ambiente Corporativo <br> A.M.J.
            </div>
        </div>
    </nav>

    <div id="content" class="flex-grow-1">
        <nav class="navbar navbar-top px-3 px-md-4 py-3 shadow-sm">
            <div class="container-fluid">
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-secondary d-md-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <span class="navbar-brand mb-0 h6 text-secondary"><i class="bi bi-book"></i> Painel Bibliotecário</span>
                </div>
                <span class="badge bg-success">Online</span>
            </div>
        </nav>
        
        <main class="container-fluid p-4">