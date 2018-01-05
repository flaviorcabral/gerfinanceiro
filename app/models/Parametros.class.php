<?php

    require_once 'Conexao.class.php';

class Parametros
{
    private $con;

    function __construct()
    {
        $conexao = new Conexao();
        $this->con = $conexao->getConexao();
    }

    //Buscar todas informações da empresa
    function informaçõesEmp()
    {

        $lista = $this->con->query("SELECT * FROM parametros");

        if (count($lista) > 0) {

            return $lista->fetchALL(PDO::FETCH_ASSOC);
        }

        return FALSE;
    }

    //Update infor empresa
    function updateInformacoes($sql)
    {
        if($this->con->exec($sql)){
            return true;
        }

        return false;
    }
}