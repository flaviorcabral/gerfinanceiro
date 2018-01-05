<?php

    require_once 'Conexao.class.php';

class logsistema
{
    private $con;

    function __construct()
    {
        $conexao = new Conexao();
        $this->con = $conexao->getConexao();
    }

    //Salvar log de operações no sistema
    function salvarLog($sql)
    {
        if($this->con->exec($sql)){

            return true;

        }

        return false;
    }

    function listaHistorico()
    {
        $logs = $this->con->query("select * from logs ORDER BY dtlog");

        if (count($logs) > 0) {

            return $logs->fetchALL(PDO::FETCH_ASSOC);
        }

        return false;
    }

}