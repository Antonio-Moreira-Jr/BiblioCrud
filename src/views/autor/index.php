<?php require __DIR__ . '/../temas/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-3 border-bottom">
    <h1 class="h3 mb-0">Autores</h1>
</div>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($erro) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <i class="bi <?= $autorEdicao ? 'bi-pencil-square' : 'bi-plus-circle' ?> me-1"></i>
                <?= $autorEdicao ? 'Editar Autor #' . $autorEdicao['CodAu'] : 'Novo Autor' ?>
            </div>
            <div class="card-body">
                <form action="<?= $autorEdicao ? '/autores/atualizar' : '/autores/inserir' ?>" method="POST">
                    <?php if ($autorEdicao): ?>
                        <input type="hidden" name="codAu" value="<?= $autorEdicao['CodAu'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Autor</label>
                        <input type="text" class="form-control" id="nome" name="nome" maxlength="40"
                               value="<?= $autorEdicao ? htmlspecialchars($autorEdicao['Nome']) : '' ?>"
                               placeholder="Ex: Machado de Assis" required>
                    </div>

                    <button type="submit" class="btn <?= $autorEdicao ? 'btn-warning text-dark fw-bold' : 'btn-primary' ?> w-100">
                        <?= $autorEdicao ? 'Atualizar Autor' : 'Salvar Autor' ?>
                    </button>

                    <?php if ($autorEdicao): ?>
                        <a href="/autores" class="btn btn-secondary w-100 mt-2">Cancelar Edição</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <i class="bi bi-list-ul me-1"></i> Autores Cadastrados
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 12%;">Código</th>
                                <th style="width: 68%;">Nome</th>
                                <th style="width: 20%;" class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($autores)): ?>
                                <?php foreach ($autores as $autor): ?>
                                    <tr>
                                        <td><?= $autor['CodAu'] ?></td>
                                        <td><?= htmlspecialchars($autor['Nome']) ?></td>
                                        <td class="text-end text-nowrap">
                                            <a href="/autores?editar=<?= $autor['CodAu'] ?>" class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="/autores/excluir?codAu=<?= $autor['CodAu'] ?>" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este autor?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Nenhum autor encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../temas/footer.php'; ?>