<?php

namespace App\biblioteca;

use PDO;
use PDOException;


class Conexao {
    private static ?PDO $instance = null;

    public static function getConexao(): PDO {
        if(self::$instance === null){
           $host = getenv('DB_HOST') ?: 'db';
           $bd   = getenv('DB_NAME') ?: 'BiblioCrud';
           $usuario = getenv('DB_USER') ?: 'root';
           $senha = getenv('DB_PASS') ?: '';
           $port = '3306';

            try{
                $urlBd = "mysql:host=$host;port=$port;dbname=$bd;charset=utf8mb4";
                self::$instance = new PDO($urlBd, $usuario, $senha, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                die("Erro ao conectar com o banco de dados!" . $e->getMessage());
            }
        }
        return self::$instance;
    }
}