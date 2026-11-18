<?php require __DIR__ . '/../temas/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Assuntos</h1>
</div>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($erro) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <i class="bi <?= $assuntoEdicao ? 'bi-pencil-square' : 'bi-plus-circle' ?> me-1"></i>
                <?= $assuntoEdicao ? 'Editar Assunto #' . $assuntoEdicao['codAs'] : 'Novo Assunto' ?>
            </div>
            <div class="card-body">
                <form action="<?= $assuntoEdicao ? '/assuntos/atualizar' : '/assuntos/inserir' ?>" method="POST">
                    
                    <?php if ($assuntoEdicao): ?>
                        <input type="hidden" name="codAs" value="<?= $assuntoEdicao['codAs'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição do Assunto</label>
                        <input type="text" class="form-control" id="descricao" name="descricao" maxlength="20"
                               value="<?= $assuntoEdicao ? htmlspecialchars($assuntoEdicao['Descricao']) : '' ?>" 
                               placeholder="Ex: Tecnologia" required>
                    </div>

                    <button type="submit" class="btn <?= $assuntoEdicao ? 'btn-warning text-dark fw-bold' : 'btn-primary' ?> w-100">
                        <?= $assuntoEdicao ? 'Atualizar Assunto' : 'Salvar Assunto' ?>
                    </button>

                    <?php if ($assuntoEdicao): ?>
                        <a href="/assuntos" class="btn btn-secondary w-100 mt-2">Cancelar Edição</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabela de Listagem -->
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <i class="bi bi-list-ul me-1"></i> Assuntos Cadastrados
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 10%;">Código</th>
                                <th style="width: 65%;">Descrição</th>
                                <th style="width: 25%;" class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($assuntos)): ?>
                                <?php foreach ($assuntos as $assunto): ?>
                                    <tr>
                                        <td><?= $assunto['codAs'] ?></td>
                                        <td><?= htmlspecialchars($assunto['Descricao']) ?></td>
                                        <td class="text-end text-nowrap">
                                            <a href="/assuntos?editar=<?= $assunto['codAs'] ?>" class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="/assuntos/excluir?codAs=<?= $assunto['codAs'] ?>" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este assunto?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Nenhum assunto encontrado.</td>
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