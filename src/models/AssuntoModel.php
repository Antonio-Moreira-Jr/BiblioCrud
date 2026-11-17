<?php
namespace App\models;

use App\biblioteca\Conexao;
use PDO;

class AssuntoModel {
    private $conn;

    public function __construct()
    {
        $this->conn = Conexao::getConexao();
    }

    public function getAssuntos() {
        $qrAssunto = $this->conn->query(" SELECT codAs, Descricao FROM Assunto ");
        return $qrAssunto->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAssuntoPorCod($codAs) {
        $qrBuscaAssunto = $this->conn->prepare(" SELECT codAs, Descricao FROM Assunto WHERE codAs = :codAs ");
        $qrBuscaAssunto->execute(['codAs' => $codAs]);
        return $qrBuscaAssunto->fetch(PDO::FETCH_ASSOC);
    }

    public function existeAssunto($descricao, $codAsExistente = null) {
        if ($codAsExistente) {
            $qrVerificaAssunto = $this->conn->prepare(" SELECT codAs FROM Assunto WHERE Descricao = :descricao AND codAs != :codAs ");
            $qrVerificaAssunto->execute(['descricao' => $descricao, 'codAs' => $codAsExistente]);
        } else {
            $qrVerificaAssunto = $this->conn->prepare(" SELECT codAs FROM Assunto WHERE descricao = :descricao ");
            $qrVerificaAssunto->execute(['descricao' => $descricao]);
        }
        return $qrVerificaAssunto->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function estaVinculadoALivro($codAs) {
        $qrVerificaVinculoLivro = $this->conn->prepare(" SELECT COUNT(*) as total FROM Livro_Assunto WHERE Assunto_codAs = :codAs ");
        $qrVerificaVinculoLivro->execute(['codAs' => $codAs]);
        $resultado = $qrVerificaVinculoLivro->fetch(PDO::FETCH_ASSOC);
        
        return ($resultado['total'] ?? 0) > 0;
    }

    public function inserir($desc) {
        $qrInsereAssunto = $this->conn->prepare(" INSERT INTO Assunto (Descricao) VALUES (:Descricao) ");
        return $qrInsereAssunto->execute(['Descricao' => $desc]);
    }

    public function atualizar($codAs, $desc) {
        $qrAtualizaAssunto = $this->conn->prepare(" UPDATE Assunto SET descricao = :descricao WHERE codAs = :codAs ");
        return $qrAtualizaAssunto->execute(['codAs' => $codAs, 'descricao' => $desc]);
    }

    public function excluir($codAs) {
        $qrExclueAssunto = $this->conn->prepare("DELETE FROM Assunto WHERE codAs = :codAs");
        return $qrExclueAssunto->execute(['codAs' => $codAs]);
    }
}