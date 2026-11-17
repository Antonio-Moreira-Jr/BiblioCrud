<?php require __DIR__ . '/../temas/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-3 border-bottom">
    <h1 class="h3 mb-0">Livros</h1>
</div>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($erro) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-12 col-xl-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <i class="bi <?= !empty($livroEdicao['Codl']) ? 'bi-pencil-square' : 'bi-plus-circle' ?> me-1"></i>
                <?= !empty($livroEdicao['Codl']) ? 'Editar Livro #' . $livroEdicao['Codl'] : 'Novo Livro' ?>
            </div>
            <div class="card-body">
                <form action="<?= !empty($livroEdicao['Codl']) ? '/livros/atualizar' : '/livros/inserir' ?>" method="POST">
                    <?php if (!empty($livroEdicao['Codl'])): ?>
                        <input type="hidden" name="codl" value="<?= $livroEdicao['Codl'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" maxlength="40"
                               value="<?= $livroEdicao ? htmlspecialchars($livroEdicao['Titulo']) : '' ?>"
                               placeholder="Ex: Rede de Computadores" required>
                    </div>

                    <div class="mb-3">
                        <label for="editora" class="form-label">Editora</label>
                        <input type="text" class="form-control" id="editora" name="editora" maxlength="40"
                               value="<?= $livroEdicao ? htmlspecialchars($livroEdicao['Editora']) : '' ?>"
                               placeholder="Ex: Companhia das Letras" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <label for="edicao" class="form-label">Edição</label>
                            <input type="number" class="form-control" id="edicao" name="edicao" min="1"
                                   value="<?= $livroEdicao ? $livroEdicao['Edicao'] : '1' ?>" required>
                        </div>
                        <div class="col-4">
                            <label for="anoPublicacao" class="form-label">Ano</label>
                            <input type="text" class="form-control" id="anoPublicacao" name="anoPublicacao" maxlength="4"
                                   value="<?= $livroEdicao ? htmlspecialchars($livroEdicao['AnoPublicacao']) : '' ?>"
                                   placeholder="2024" required>
                        </div>
                        <div class="col-4">
                            <label for="valor" class="form-label">Valor (R$)</label>
                            <input type="text" class="form-control" id="valor" name="valor"
                                value="<?= $livroEdicao ? (is_numeric($livroEdicao['Valor']) ? number_format($livroEdicao['Valor'], 2, ',', '.') : htmlspecialchars($livroEdicao['Valor'])) : '' ?>"
                                placeholder="0,00" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-flex justify-content-between">
                            <span>Autor(es)</span>
                            <a href="/autores" class="small text-decoration-none">+ Novo</a>
                        </label>
                        <div class="border rounded p-2 bg-light" style="max-height: 130px; overflow-y: auto;">
                            <?php if (!empty($autores)): ?>
                                <?php foreach ($autores as $autor): ?>
                                    <?php $checked = $livroEdicao && in_array($autor['CodAu'], $livroEdicao['autoresIds'], true); ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="autores[]"
                                               value="<?= $autor['CodAu'] ?>" id="autor_<?= $autor['CodAu'] ?>"
                                               <?= $checked ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="autor_<?= $autor['CodAu'] ?>">
                                            <?= htmlspecialchars($autor['Nome']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted small">Cadastre um autor antes de prosseguir.</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-flex justify-content-between">
                            <span>Assunto(s)</span>
                            <a href="/assuntos" class="small text-decoration-none">+ Novo</a>
                        </label>
                        <div class="border rounded p-2 bg-light" style="max-height: 130px; overflow-y: auto;">
                            <?php if (!empty($assuntos)): ?>
                                <?php foreach ($assuntos as $assunto): ?>
                                    <?php $checked = $livroEdicao && in_array($assunto['codAs'], $livroEdicao['assuntosIds'], true); ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="assuntos[]"
                                               value="<?= $assunto['codAs'] ?>" id="assunto_<?= $assunto['codAs'] ?>"
                                               <?= $checked ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="assunto_<?= $assunto['codAs'] ?>">
                                            <?= htmlspecialchars($assunto['Descricao']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted small">Cadastre um assunto antes de prosseguir.</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn <?= !empty($livroEdicao['Codl']) ? 'btn-warning text-dark fw-bold' : 'btn-primary' ?> w-100">
                        <?= !empty($livroEdicao['Codl']) ? 'Atualizar Livro' : 'Salvar Livro' ?>
                    </button>

                    <?php if (!empty($livroEdicao['Codl'])): ?>
                        <a href="/livros" class="btn btn-secondary w-100 mt-2">Cancelar Edição</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-8">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <i class="bi bi-list-ul me-1"></i> Acervo Cadastrado
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Título</th>
                                <th>Autor(es)</th>
                                <th>Assunto(s)</th>
                                <th>Editora / Ano</th>
                                <th class="text-end">Valor</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($livros)): ?>
                                <?php foreach ($livros as $livro): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold"><?= htmlspecialchars($livro['Titulo']) ?></div>
                                            <small class="text-muted"><?= $livro['Edicao'] ?>ª ed.</small>
                                        </td>
                                        <td><small><?= htmlspecialchars($livro['Autores'] ?? '-') ?></small></td>
                                        <td>
                                            <?php if (!empty($livro['Assuntos'])): ?>
                                                <?php foreach (explode(', ', $livro['Assuntos']) as $tag): ?>
                                                    <span class="badge bg-light text-dark border"><?= htmlspecialchars($tag) ?></span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div><?= htmlspecialchars($livro['Editora']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($livro['AnoPublicacao']) ?></small>
                                        </td>
                                        <td class="text-end text-nowrap fw-medium">
                                            R$ <?= number_format($livro['Valor'], 2, ',', '.') ?>
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <a href="/livros?editar=<?= $livro['Codl'] ?>" class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="/livros/excluir?codl=<?= $livro['Codl'] ?>" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este livro?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Nenhum livro encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const campoValor = document.getElementById('valor');
    if (campoValor) {
        campoValor.addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v === '') {
                e.target.value = '';
                return;
            }
            v = (parseInt(v, 10) / 100).toFixed(2) + '';
            v = v.replace('.', ',');
            v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            e.target.value = v;
        });
    }
</script>

<?php require __DIR__ . '/../temas/footer.php'; ?>