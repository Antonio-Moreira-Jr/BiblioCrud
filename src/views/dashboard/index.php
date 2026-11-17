<?php require __DIR__ . '/../temas/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 mb-1">Dashboard</h1>
        <p class="text-muted small mb-0">Indicadores em tempo real do catálogo e movimentação</p>
    </div>
    <div class="d-flex gap-2 mt-2 mt-md-0">
        <a href="/autores" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-person-plus me-1"></i> Autor
        </a>
        <a href="/assuntos" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-tag me-1"></i> Assunto
        </a>
        <a href="/livros" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Novo Livro
        </a>
    </div>
</div>

<!-- KPIs Principais -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Títulos no Acervo</span>
                    <h3 class="mb-0 mt-1 fw-bold"><?= $totais['livros'] ?></h3>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                    <i class="bi bi-journal-bookmark-fill fs-4"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="/livros" class="small text-decoration-none">Ver catálogo &rarr;</a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Autores</span>
                    <h3 class="mb-0 mt-1 fw-bold"><?= $totais['autores'] ?></h3>
                </div>
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                    <i class="bi bi-person-lines-fill fs-4"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="/autores" class="small text-decoration-none text-success">Gerenciar autores &rarr;</a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Assuntos</span>
                    <h3 class="mb-0 mt-1 fw-bold"><?= $totais['assuntos'] ?></h3>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                    <i class="bi bi-tags-fill fs-4"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="/assuntos" class="small text-decoration-none text-warning">Gerenciar assuntos &rarr;</a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Valor do Acervo</span>
                    <h3 class="mb-0 mt-1 fw-bold">R$ <?= number_format($totais['valorAcervo'], 2, ',', '.') ?></h3>
                </div>
                <div class="bg-dark bg-opacity-10 text-dark rounded-3 p-3">
                    <i class="bi bi-currency-dollar fs-4"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="/relatorios" class="small text-decoration-none text-secondary">Emitir relatório &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico + Últimos Cadastros -->
<div class="row g-3">
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-pie-chart me-2 text-primary"></i>Livros por Assunto</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <?php if (!empty($distribuicaoAssuntos)): ?>
                    <div style="width: 100%; max-height: 260px;">
                        <canvas id="graficoAssuntos"></canvas>
                    </div>
                <?php else: ?>
                    <p class="text-muted small my-5">Cadastre assuntos e livros para visualizar a distribuição.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2 text-primary"></i>Últimos Livros Catalogados</h6>
                <a href="/livros" class="btn btn-sm btn-light">Ver todos</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Título</th>
                                <th>Editora</th>
                                <th>Ano</th>
                                <th class="text-end">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ultimosLivros)): ?>
                                <?php foreach ($ultimosLivros as $livro): ?>
                                    <tr>
                                        <td class="fw-medium"><?= htmlspecialchars($livro['Titulo']) ?></td>
                                        <td class="text-muted"><?= htmlspecialchars($livro['Editora']) ?></td>
                                        <td><?= htmlspecialchars($livro['AnoPublicacao']) ?></td>
                                        <td class="text-end text-nowrap">R$ <?= number_format((float)$livro['Valor'], 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">Nenhum livro catalogado até o momento.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($distribuicaoAssuntos)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('graficoAssuntos');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($distribuicaoAssuntos, 'assunto')) ?>,
            datasets: [{
                data: <?= json_encode(array_map('intval', array_column($distribuicaoAssuntos, 'quantidade'))) ?>,
                backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#20c997'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
<?php endif; ?>

<?php require __DIR__ . '/../temas/footer.php'; ?>