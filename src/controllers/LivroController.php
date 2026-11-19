<?php

namespace App\Controllers;

use App\Models\LivroModel;
use App\Models\AutorModel;
use App\Models\AssuntoModel;
use App\Cache\CacheFactory;
use App\Cache\KeysCache;

class LivroController
{
    private LivroModel $livroModel;
    private AutorModel $autorModel;
    private AssuntoModel $assuntoModel;

    public function __construct() {
        $this->livroModel = new LivroModel();
        $this->autorModel = new AutorModel();
        $this->assuntoModel = new AssuntoModel();
    }

    public function index() {
        $erro = $_GET['erro'] ?? null;

        $livroEdicao = null;

        if (!empty($_GET['editar'])) {
            $livroEdicao = $this->livroModel->getLivroPorCodl((int) $_GET['editar']);
        }

        if (!empty($_SESSION['old_livro'])) {
            $old = $_SESSION['old_livro'];
            unset($_SESSION['old_livro']);

            $livroEdicao = [
                'Codl'          => $old['codl'] ?? null,
                'Titulo'        => $old['titulo'] ?? '',
                'Editora'       => $old['editora'] ?? '',
                'Edicao'        => $old['edicao'] ?? 1,
                'AnoPublicacao' => $old['anoPublicacao'] ?? '',
                'Valor'         => $old['valor'] ?? '',
                'autoresIds'    => $old['autores'] ?? [],
                'assuntosIds'   => $old['assuntos'] ?? [],
            ];
        }

        $livros   = $this->livroModel->getLivros();
        $autores  = $this->autorModel->getAutores();
        $assuntos = $this->assuntoModel->getAssuntos();

        require __DIR__ . '/../views/livro/index.php';
    }

    public function inserir() {
        $validacao = $this->validarFormulario($_POST);
        if (isset($validacao['erro'])) {
            $_SESSION['old_livro'] = $_POST;
            header('Location: /livros?erro=' . urlencode($validacao['erro']));
            exit;
        }

        try {
            $this->livroModel->inserir($validacao['dados'], $validacao['autores'], $validacao['assuntos']);
        } catch (\PDOException $e) {
            error_log('[Livros] Erro ao inserir: ' . $e->getMessage());
            header('Location: /livros?erro=' . urlencode('Erro ao cadastrar o livro no banco de dados.'));
            exit;
        }

        CacheFactory::criar()->delete(KeysCache::DASHBOARD);
        header('Location: /livros');
        exit;
    }

    public function atualizar() {
        $codl = (int) ($_POST['codl'] ?? 0);
        if ($codl <= 0) {
            header('Location: /livros?erro=' . urlencode('Livro inválido para atualização.'));
            exit;
        }

        $validacao = $this->validarFormulario($_POST);
        if (isset($validacao['erro'])) {
            $_SESSION['old_livro'] = $_POST;
            header('Location: /livros?editar=' . $codl . '&erro=' . urlencode($validacao['erro']));
            exit;
        }

        try {
            $this->livroModel->atualizar($codl, $validacao['dados'], $validacao['autores'], $validacao['assuntos']);
        } catch (\PDOException $e) {
            error_log('[Livros] Erro ao atualizar: ' . $e->getMessage());
            header('Location: /livros?editar=' . $codl . '&erro=' . urlencode('Erro ao atualizar o livro.'));
            exit;
        }

        CacheFactory::criar()->delete(KeysCache::DASHBOARD);
        header('Location: /livros');
        exit;
    }

    public function excluir() {
        $codl = (int) ($_GET['codl'] ?? 0);

        if ($codl > 0) {
            try {
                $this->livroModel->excluir($codl);
            } catch (\PDOException $e) {
                error_log('[Livros] Erro ao excluir: ' . $e->getMessage());
                header('Location: /livros?erro=' . urlencode('Erro ao excluir o livro.'));
                exit;
            }
        }

        CacheFactory::criar()->delete(KeysCache::DASHBOARD);
        header('Location: /livros');
        exit;
    }

    private function validarFormulario(array $post) {
        $titulo        = trim($post['titulo'] ?? '');
        $editora       = trim($post['editora'] ?? '');
        $edicao        = (int) ($post['edicao'] ?? 0);
        $anoPublicacao = trim($post['anoPublicacao'] ?? '');
        $valorBruto    = trim($post['valor'] ?? '');
        $autores       = array_filter(array_map('intval', $post['autores'] ?? []));
        $assuntos      = array_filter(array_map('intval', $post['assuntos'] ?? []));

        if ($titulo === '' || mb_strlen($titulo) > 40) {
            return ['erro' => 'O título é obrigatório e deve ter no máximo 40 caracteres.'];
        }

        if ($editora === '' || mb_strlen($editora) > 40) {
            return ['erro' => 'A editora é obrigatória e deve ter no máximo 40 caracteres.'];
        }

        if ($edicao <= 0) {
            return ['erro' => 'Informe um número de edição válido.'];
        }

        if (!preg_match('/^\d{4}$/', $anoPublicacao) || $anoPublicacao < 1000 || $anoPublicacao > (date('Y') + 1)) {
            return ['erro' => 'Informe um ano de publicação válido.'];
        }

        // Com vírgula, o ponto é separador de milhar ("1.234,56"); sem vírgula, o ponto é decimal ("59.90").
        $valorNormalizado = str_replace(['R$', ' '], '', $valorBruto);
        if (str_contains($valorNormalizado, ',')) {
            $valorNormalizado = str_replace(['.', ','], ['', '.'], $valorNormalizado);
        }

        if ($valorNormalizado === '' || !is_numeric($valorNormalizado) || $valorNormalizado < 0) {
            return ['erro' => 'Informe um preço válido para o livro.'];
        }

        if (empty($autores)) {
            return ['erro' => 'Selecione pelo menos um autor para o livro.'];
        }

        if (empty($assuntos)) {
            return ['erro' => 'Selecione pelo menos um assunto para o livro.'];
        }

        return [
            'dados' => [
                'titulo'        => $titulo,
                'editora'       => $editora,
                'edicao'        => $edicao,
                'anoPublicacao' => $anoPublicacao,
                'valor'         => (float) $valorNormalizado,
            ],
            'autores'  => $autores,
            'assuntos' => $assuntos,
        ];
    }
}