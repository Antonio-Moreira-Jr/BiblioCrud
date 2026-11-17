<?php

namespace App\Models;

use App\biblioteca\Conexao;
use PDO;

class AutorModel
{
    private PDO $conn;

    public function __construct(){
        $this->conn = Conexao::getConexao();
    }

    public function getAutores() {
        $stmt = $this->conn->query("SELECT CodAu, Nome FROM Autor ORDER BY Nome ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAutorPorCodAu($codAu) {
        $stmt = $this->conn->prepare("SELECT CodAu, Nome FROM Autor WHERE CodAu = :codAu");
        $stmt->bindValue(':codAu', $codAu, PDO::PARAM_INT);
        $stmt->execute();

        $autor = $stmt->fetch(PDO::FETCH_ASSOC);
        return $autor ?: null;
    }

    public function existeNome($nome, $ignorarCodAu = null) {
        $sql = "SELECT COUNT(*) FROM Autor WHERE LOWER(TRIM(Nome)) = LOWER(TRIM(:nome))";
        if ($ignorarCodAu !== null) {
            $sql .= " AND CodAu != :codAu";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        if ($ignorarCodAu !== null) {
            $stmt->bindValue(':codAu', $ignorarCodAu, PDO::PARAM_INT);
        }
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    public function inserir($nome) {
        $stmt = $this->conn->prepare("INSERT INTO Autor (Nome) VALUES (:nome)");
        $stmt->bindValue(':nome', $nome);
        return $stmt->execute();
    }

    public function atualizar($codAu, $nome) {
        $stmt = $this->conn->prepare("UPDATE Autor SET Nome = :nome WHERE CodAu = :codAu");
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':codAu', $codAu, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function estaVinculadoALivro($codAu) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Livro_Autor WHERE Autor_CodAu = :codAu");
        $stmt->bindValue(':codAu', $codAu, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    public function excluir($codAu) {
        $stmt = $this->conn->prepare("DELETE FROM Autor WHERE CodAu = :codAu");
        $stmt->bindValue(':codAu', $codAu, PDO::PARAM_INT);
        return $stmt->execute();
    }
}