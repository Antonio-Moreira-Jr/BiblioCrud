<?php

namespace App\Models;

use App\biblioteca\Conexao;
use PDO;

class DashboardModel
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Conexao::getConexao();
    }

    public function getTotaisGerais() {
        $totais = [
            'livros' => 0,
            'autores' => 0,
            'assuntos' => 0,
            'valorAcervo' => 0.0,
        ];

        $totais['assuntos'] = (int) $this->conn->query("SELECT COUNT(*) FROM Assunto")->fetchColumn();
        $totais['autores']  = (int) $this->conn->query("SELECT COUNT(*) FROM Autor")->fetchColumn();

        $qrSeleciona = $this->conn->query("SELECT COUNT(*) as total, COALESCE(SUM(Valor), 0) as valor FROM Livro");
        if ($row = $qrSeleciona->fetch(PDO::FETCH_ASSOC)) {
            $totais['livros'] = (int) $row['total'];
            $totais['valorAcervo'] = (float) $row['valor'];
        }

        return $totais;
    }

    public function getDistribuicaoPorAssunto(int $limite = 6) {
        $sql = "
            SELECT a.Descricao AS assunto, COUNT(la.Livro_Codl) AS quantidade
            FROM Assunto a
            JOIN Livro_Assunto la ON a.codAs = la.Assunto_codAs
            GROUP BY a.codAs, a.Descricao
            ORDER BY quantidade DESC, a.Descricao ASC
            LIMIT :limite
        ";
        $qrSeleciona = $this->conn->prepare($sql);
        $qrSeleciona->bindValue(':limite', $limite, PDO::PARAM_INT);
        $qrSeleciona->execute();

        return $qrSeleciona->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUltimosLivros(int $limite = 5) {
        $sql = "
            SELECT Codl, Titulo, Editora, AnoPublicacao, Valor
            FROM Livro
            ORDER BY Codl DESC
            LIMIT :limite
        ";
        $qrSeleciona = $this->conn->prepare($sql);
        $qrSeleciona->bindValue(':limite', $limite, PDO::PARAM_INT);
        $qrSeleciona->execute();

        return $qrSeleciona->fetchAll(PDO::FETCH_ASSOC);
    }
}