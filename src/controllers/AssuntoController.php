<?php

namespace App\controllers;

use App\models\AssuntoModel;

class AssuntoController {
    private $assuntoModel;

    public function __construct() {
        $this->assuntoModel = new AssuntoModel();
    }

    public function index() {

        $assuntos = $this->assuntoModel->getAssuntos();

        $assuntoEdicao = null;
        if (isset($_GET['editar'])) {
            $assuntoEdicao = $this->assuntoModel->getAssuntoPorCod($_GET['editar']);
        }

        require __DIR__ . '/../views/assunto/index.php';
    }

    public function inserirAssunto() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $descricao = trim($_POST['descricao'] ?? '');

            if ($descricao === '') {
                header('Location: /assuntos?erro=' . urlencode('A descrição do assunto não pode ser vazia ou conter apenas espaços.'));
                exit;
            }

            if (mb_strlen($descricao) > 20) {
                header('Location: /assuntos?erro=' . urlencode('A descrição do assunto deve ter no máximo 20 caracteres.'));
                exit;
            }

            if ($this->assuntoModel->existeAssunto($descricao)) {
                header('Location: /assuntos?erro=' . urlencode('Este assunto já está cadastrado no sistema.'));
                exit;
            }

            $this->assuntoModel->inserir($descricao);
        }
        header('Location: /assuntos');
        exit;
    }

    public function atualizarAssunto() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codAs = $_POST['codAs'] ?? null;
            $descricao = trim($_POST['descricao'] ?? '');

            if (!$codAs || $descricao === '') {
                header('Location: /assuntos?erro=' . urlencode('Insira um assunto.'));
                exit;
            }

            if ($this->assuntoModel->existeAssunto($descricao, $codAs)) {
                header('Location: /assuntos?erro=' . urlencode('Já existe outro assunto cadastrado com esta descrição. Escolha um nome diferente.'));
                exit;
            }

            $this->assuntoModel->atualizar($codAs, $descricao);
        }
        header('Location: /assuntos');
        exit;
    }

    public function excluirAssunto() {
        $codAs = $_GET['codAs'] ?? null;
        if ($codAs) {
            // Verifica se o assunto está vinculado a livros
            if ($this->assuntoModel->estaVinculadoALivro($codAs)) {
                header('Location: /assuntos?erro=' . urlencode('Não é possível excluir este assunto pois ele está vinculado a um ou mais livros.'));
                exit;
            }

            try {
                $this->assuntoModel->excluir($codAs);
            } catch (\PDOException $e) {
                error_log('[Assuntos] Erro ao excluir: ' . $e->getMessage());
                header('Location: /assuntos?erro=' . urlencode('Erro de integridade ao excluir o autor.'));
            }
        }
        header('Location: /assuntos');
        exit;
    }
}