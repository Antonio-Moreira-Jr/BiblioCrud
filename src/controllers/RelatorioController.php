<?php

declare(strict_types=1);

namespace App\Controllers;

use App\models\RelatorioModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class RelatorioController
{
    private RelatorioModel $relatorioModel;

    public function __construct() {
        $this->relatorioModel = new RelatorioModel();
    }

    public function index() {
        $erroConsulta = null;
        $autores = $this->carregarAutores($erroConsulta);
        $erro = $erroConsulta ?? ($_GET['erro'] ?? null);

        require __DIR__ . '/../views/relatorio/index.php';
    }

    public function pdf() {
        $erro = null;
        $autores = $this->carregarAutores($erro);

        if ($erro !== null) {
            header('Location: /relatorios?erro=' . urlencode($erro));
            exit;
        }

        $geradoEm = new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo'));

        ob_start();
        require __DIR__ . '/../views/relatorio/pdf/relatorio.php';
        $html = (string) ob_get_clean();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('relatorio-livros-por-autor.pdf', ['Attachment' => false]);
        exit;
    }

    private function carregarAutores(?string &$erro) {
        try {
            return $this->relatorioModel->getLivrosPorAutor();
        } catch (\PDOException $e) {
            error_log('[Relatorio] ' . $e->getMessage());
            return [];
        }
    }
}