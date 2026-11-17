<?php

namespace App\Models;

use App\biblioteca\Conexao;
use PDO;

class LivroModel
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Conexao::getConexao();
    }

    public function getLivros() {
        $sql = "
            SELECT 
                l.Codl,
                l.Titulo,
                l.Editora,
                l.Edicao,
                l.AnoPublicacao,
                l.Valor,
                GROUP_CONCAT(DISTINCT au.Nome ORDER BY au.Nome SEPARATOR ', ') AS Autores,
                GROUP_CONCAT(DISTINCT a.Descricao ORDER BY a.Descricao SEPARATOR ', ') AS Assuntos
            FROM Livro l
            LEFT JOIN Livro_Autor la ON l.Codl = la.Livro_Codl
            LEFT JOIN Autor au ON la.Autor_CodAu = au.CodAu
            LEFT JOIN Livro_Assunto las ON l.Codl = las.Livro_Codl
            LEFT JOIN Assunto a ON las.Assunto_codAs = a.codAs
            GROUP BY l.Codl, l.Titulo, l.Editora, l.Edicao, l.AnoPublicacao, l.Valor
            ORDER BY l.Titulo ASC
        ";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLivroPorCodl(int $codl) {
        $stmt = $this->conn->prepare("SELECT * FROM Livro WHERE Codl = :codl");
        $stmt->bindValue(':codl', $codl, PDO::PARAM_INT);
        $stmt->execute();

        $livro = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$livro) {
            return null;
        }

        $stmtAutores = $this->conn->prepare("SELECT Autor_CodAu FROM Livro_Autor WHERE Livro_Codl = :codl");
        $stmtAutores->bindValue(':codl', $codl, PDO::PARAM_INT);
        $stmtAutores->execute();
        $livro['autoresIds'] = array_map('intval', $stmtAutores->fetchAll(PDO::FETCH_COLUMN));

        $stmtAssuntos = $this->conn->prepare("SELECT Assunto_codAs FROM Livro_Assunto WHERE Livro_Codl = :codl");
        $stmtAssuntos->bindValue(':codl', $codl, PDO::PARAM_INT);
        $stmtAssuntos->execute();
        $livro['assuntosIds'] = array_map('intval', $stmtAssuntos->fetchAll(PDO::FETCH_COLUMN));

        return $livro;
    }

    public function inserir(array $dados, array $autoresIds, array $assuntosIds): bool
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO Livro (Titulo, Editora, Edicao, AnoPublicacao, Valor) 
                    VALUES (:titulo, :editora, :edicao, :ano, :valor)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':titulo'  => $dados['titulo'],
                ':editora' => $dados['editora'],
                ':edicao'  => $dados['edicao'],
                ':ano'     => $dados['anoPublicacao'],
                ':valor'   => $dados['valor'],
            ]);

            $codl = (int) $this->conn->lastInsertId();
            $this->sincronizarRelacionamentos($codl, $autoresIds, $assuntosIds);

            $this->conn->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    public function atualizar(int $codl, array $dados, array $autoresIds, array $assuntosIds): bool
    {
        try {
            $this->conn->beginTransaction();

            $sql = "UPDATE Livro 
                    SET Titulo = :titulo, Editora = :editora, Edicao = :edicao, AnoPublicacao = :ano, Valor = :valor 
                    WHERE Codl = :codl";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':titulo'  => $dados['titulo'],
                ':editora' => $dados['editora'],
                ':edicao'  => $dados['edicao'],
                ':ano'     => $dados['anoPublicacao'],
                ':valor'   => $dados['valor'],
                ':codl'    => $codl,
            ]);

            $this->conn->prepare("DELETE FROM Livro_Autor WHERE Livro_Codl = :codl")->execute([':codl' => $codl]);
            $this->conn->prepare("DELETE FROM Livro_Assunto WHERE Livro_Codl = :codl")->execute([':codl' => $codl]);

            $this->sincronizarRelacionamentos($codl, $autoresIds, $assuntosIds);

            $this->conn->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    public function excluir(int $codl): bool
    {
        try {
            $this->conn->beginTransaction();

            $this->conn->prepare("DELETE FROM Livro_Autor WHERE Livro_Codl = :codl")->execute([':codl' => $codl]);
            $this->conn->prepare("DELETE FROM Livro_Assunto WHERE Livro_Codl = :codl")->execute([':codl' => $codl]);
            $this->conn->prepare("DELETE FROM Livro WHERE Codl = :codl")->execute([':codl' => $codl]);

            $this->conn->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    private function sincronizarRelacionamentos(int $codl, array $autoresIds, array $assuntosIds): void
    {
        $stmtAutor = $this->conn->prepare("INSERT INTO Livro_Autor (Livro_Codl, Autor_CodAu) VALUES (:codl, :codAu)");
        foreach (array_unique($autoresIds) as $codAu) {
            $stmtAutor->execute([':codl' => $codl, ':codAu' => (int) $codAu]);
        }

        $stmtAssunto = $this->conn->prepare("INSERT INTO Livro_Assunto (Livro_Codl, Assunto_codAs) VALUES (:codl, :codAs)");
        foreach (array_unique($assuntosIds) as $codAs) {
            $stmtAssunto->execute([':codl' => $codl, ':codAs' => (int) $codAs]);
        }
    }
}