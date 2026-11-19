<?php

namespace App\Controllers;

use App\Cache\CacheFactory;
use App\Cache\KeysCache;
use App\Models\AutorModel;

class AutorController {
    private AutorModel $autorModel;

    public function __construct()
    {
        $this->autorModel = new AutorModel();
    }

    public function index() {
        $erro = $_GET['erro'] ?? null;
        $autorEdicao = null;

        if (!empty($_GET['editar'])) {
            $autorEdicao = $this->autorModel->getAutorPorCodAu($_GET['editar']);
        }

        $autores = $this->autorModel->getAutores();
        require __DIR__ . '/../views/autor/index.php';
    }

    public function inserir() {
        $nome = trim($_POST['nome'] ?? '');

        if ($nome === '') {
            header('Location: /autores?erro=' . urlencode('O nome do autor é obrigatório.'));
            exit;
        }

        if (mb_strlen($nome) > 40) {
            header('Location: /autores?erro=' . urlencode('O nome do autor deve ter no máximo 40 caracteres.'));
            exit;
        }

        if ($this->autorModel->existeNome($nome)) {
            header('Location: /autores?erro=' . urlencode('Já existe um autor cadastrado com este nome.'));
            exit;
        }

        try {
            $this->autorModel->inserir($nome);
        } catch (\PDOException $e) {
            error_log('[Autores] Erro ao inserir: ' . $e->getMessage());
            header('Location: /autores?erro=' . urlencode('Erro ao salvar o autor no banco de dados.'));
            exit;
        }

        CacheFactory::criar()->delete(KeysCache::DASHBOARD);
        header('Location: /autores');
        exit;
    }

    public function atualizar() {
        $codAu = (int) ($_POST['codAu'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');

        if ($codAu <= 0 || $nome === '') {
            header('Location: /autores?erro=' . urlencode('Dados inválidos para atualização.'));
            exit;
        }

        if (mb_strlen($nome) > 40) {
            header('Location: /autores?erro=' . urlencode('O nome do autor deve ter no máximo 40 caracteres.'));
            exit;
        }

        if ($this->autorModel->existeNome($nome, $codAu)) {
            header('Location: /autores?editar=' . $codAu . '&erro=' . urlencode('Já existe outro autor com este nome.'));
            exit;
        }

        try {
            $this->autorModel->atualizar($codAu, $nome);
        } catch (\PDOException $e) {
            error_log('[Autores] Erro ao atualizar: ' . $e->getMessage());
            header('Location: /autores?erro=' . urlencode('Erro ao atualizar o autor.'));
            exit;
        }

        CacheFactory::criar()->delete(KeysCache::DASHBOARD);
        header('Location: /autores');
        exit;
    }

    public function excluir() {
        $codAu = (int) ($_GET['codAu'] ?? 0);

        if ($codAu > 0) {
            if ($this->autorModel->estaVinculadoALivro($codAu)) {
                header('Location: /autores?erro=' . urlencode('Não é possível excluir este autor pois ele está vinculado a um ou mais livros.'));
                exit;
            }

            try {
                $this->autorModel->excluir($codAu);
            } catch (\PDOException $e) {
                error_log('[Autores] Erro ao excluir: ' . $e->getMessage());
                header('Location: /autores?erro=' . urlencode('Erro de integridade ao excluir o autor.'));
                exit;
            }
        }

        CacheFactory::criar()->delete(KeysCache::DASHBOARD);
        header('Location: /autores');
        exit;
    }
}