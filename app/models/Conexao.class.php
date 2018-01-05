<?php

class Conexao
{

    public static $instance;


    public static function getConexao()
    {
        $host = 'localhost';
        $dataBase = 'gerfinanceiro';
        $usuario = 'root';
        $senha = '';

        if (!isset(self::$instance)) {

            try{
                self::$instance = new PDO('mysql:host='.$host.';dbname='.$dataBase, $usuario, $senha); //Conexao MySQL
                //self::$instance = new PDO("sqlsrv:Server=localhost;Database=chequesconf", "sa", "un1cr3d1"); //Conexao SQL Server
                //self::$instance = new PDO("pgsql:host=localhost;port=5432;dbname=chequesconf;user=postgres;password=un1cr3d1"); //Conexao Postgres

                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            }catch (PDOException $e){

                return false;

            }
        }

        return self::$instance;
    }
}