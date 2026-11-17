<?php

namespace App\Controllers;

use App\Cache\CacheFactory;
use App\Cache\KeysCache;
use App\Models\DashboardModel;

class DashboardController {
    public function index() {
         $dados = [
            'totais'               => ['livros' => 0, 'autores' => 0, 'assuntos' => 0, 'valorAcervo' => 0.0],
            'distribuicaoAssuntos' => [],
            'ultimosLivros'        => [],
        ];

        try {
            $dados = CacheFactory::criar()->remember(
                KeysCache::DASHBOARD,
                KeysCache::DASHBOARD_TTL,
                static function (): array {
                    $model = new DashboardModel();

                    return [
                        'totais'               => $model->getTotaisGerais(),
                        'distribuicaoAssuntos' => $model->getDistribuicaoPorAssunto(),
                        'ultimosLivros'        => $model->getUltimosLivros(),
                    ];
                }
            );
        } catch (\PDOException $e) {
            error_log('[Dashboard] Falha ao carregar indicadores: ' . $e->getMessage());
        }

        $totais               = $dados['totais'];
        $distribuicaoAssuntos = $dados['distribuicaoAssuntos'];
        $ultimosLivros        = $dados['ultimosLivros'];

        require __DIR__ . '/../views/dashboard/index.php';
    }
}