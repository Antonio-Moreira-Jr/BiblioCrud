<?php
$h     = fn($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
$moeda = fn($valor): string => 'R$ ' . number_format((float) $valor, 2, ',', '.');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório: livros por autor</title>
    <style>
        @page { margin: 28px 32px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #212529; }
        h1 { font-size: 16px; margin: 0 0 2px 0; }
        .gerado { color: #6c757d; margin-bottom: 14px; }
        .autor { margin-bottom: 14px; page-break-inside: avoid; }
        .autor-titulo { background: #212529; color: #fff; padding: 5px 8px; font-weight: bold; }
        .autor-titulo span { float: right; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #dee2e6; padding: 4px 6px; text-align: left; }
        th { background: #f1f3f5; }
        .c { text-align: center; }
        .r { text-align: right; }
        .rodape { margin-top: 10px; color: #6c757d; font-size: 9px; }
    </style>
</head>
<body>
    <h1>Relatório: Livros por Autor</h1>
    <div class="gerado">Gerado em <?= $geradoEm->format('d/m/Y H:i') ?></div>

    <?php foreach ($autores as $autor): ?>
        <div class="autor">
            <div class="autor-titulo">
                <?= $h($autor['autorNome']) ?>
                <span><?= count($autor['livros']) ?> <?= count($autor['livros']) === 1 ? 'livro' : 'livros' ?> &middot; total <?= $moeda($autor['totalCentavos'] / 100) ?></span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Editora</th>
                        <th class="c">Edição</th>
                        <th class="c">Ano</th>
                        <th>Assuntos</th>
                        <th class="r">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($autor['livros'] as $livro): ?>
                        <tr>
                            <td><?= $h($livro['LivroTitulo']) ?></td>
                            <td><?= $h($livro['Editora']) ?></td>
                            <td class="c"><?= $h($livro['Edicao']) ?>ª</td>
                            <td class="c"><?= $h($livro['AnoPublicacao']) ?></td>
                            <td><?= $livro['Assuntos'] !== null ? $h($livro['Assuntos']) : '-' ?></td>
                            <td class="r"><?= $moeda($livro['Valor']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>

    <div class="rodape">Livros com mais de um autor aparecem sob cada um deles.</div>
</body>
</html>