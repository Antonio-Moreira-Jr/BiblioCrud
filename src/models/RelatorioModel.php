<?php

declare(strict_types=1);

namespace App\Models;

use App\Biblioteca\Conexao;
use PDO;

class RelatorioModel
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Conexao::getConexao();
    }

    public function getLivrosPorAutor() {
        $sql = "SELECT AutorId, AutorNome, LivroId, LivroTitulo, Editora,
                       Edicao, AnoPublicacao, Valor, Assuntos
                FROM vw_relatorio_livros
                ORDER BY AutorNome ASC, LivroTitulo ASC";

        $linhas = $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $autores = [];
        foreach ($linhas as $linha) {
            $autorId = (int) $linha['AutorId'];

            if (!isset($autores[$autorId])) {
                $autores[$autorId] = [
                    'autorId'       => $autorId,
                    'autorNome'     => $linha['AutorNome'],
                    'livros'        => [],
                    'totalCentavos' => 0,
                ];
            }

            $autores[$autorId]['livros'][] = $linha;
            $autores[$autorId]['totalCentavos'] += (int) round(((float) $linha['Valor']) * 100);
        }

        return array_values($autores);
    }
}