<?php
$h      = fn($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
$moeda  = fn($valor): string => 'R$ ' . number_format((float) $valor, 2, ',', '.');
?>
<?php require __DIR__ . '/../temas/header.php'; ?>

<style>
    @media print {
        #sidebar, .navbar-top { display: none !important; }
        body { background: #fff; }
    }
</style>

<div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-3 mb-3 border-bottom">
    <h1 class="h3 mb-0">Relatório -> Livros por Autor</h1>
    <div class="d-print-none">
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Imprimir
        </button>
        <a href="/relatorios/pdf" target="_blank" class="btn btn-dark btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i>Baixar PDF
        </a>
    </div>
</div>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $h($erro) ?>
    </div>
<?php endif; ?>

<?php if (empty($autores) && empty($erro)): ?>
    <div class="alert alert-info shadow-sm">
        Nenhum livro com autor cadastrado. <a href="/livros" class="alert-link">Cadastre um livro</a> para ver o relatório.
    </div>
<?php endif; ?>

<?php foreach ($autores as $autor): ?>
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <span><i class="bi bi-person-fill me-1"></i><?= $h($autor['autorNome']) ?></span>
            <small>
                <?= count($autor['livros']) ?> <?= count($autor['livros']) === 1 ? 'livro' : 'livros' ?>
                &middot; total <?= $moeda($autor['totalCentavos'] / 100) ?>
            </small>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Editora</th>
                        <th class="text-center">Edição</th>
                        <th class="text-center">Ano</th>
                        <th>Assuntos</th>
                        <th class="text-end">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($autor['livros'] as $livro): ?>
                        <tr>
                            <td><?= $h($livro['LivroTitulo']) ?></td>
                            <td><?= $h($livro['Editora']) ?></td>
                            <td class="text-center"><?= $h($livro['Edicao']) ?>ª</td>
                            <td class="text-center"><?= $h($livro['AnoPublicacao']) ?></td>
                            <td><?= $livro['Assuntos'] !== null ? $h($livro['Assuntos']) : '<span class="text-muted">-</span>' ?></td>
                            <td class="text-end"><?= $moeda($livro['Valor']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endforeach; ?>

<?php if (!empty($autores)): ?>
    <p class="text-muted small">Livros com mais de um autor aparecem sob cada um deles.</p>
<?php endif; ?>

<?php require __DIR__ . '/../temas/footer.php'; ?>