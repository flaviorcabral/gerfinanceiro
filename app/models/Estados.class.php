<?php

    require_once 'Conexao.class.php';

class Estados
{
    private $con;

    function __construct()
    {
        $conexao = new Conexao();
        $this->con = $conexao->getConexao();
    }

    //Listar estados por ordem alfabetica
    function listarEstados()
    {
        $lista = $this->con->query("SELECT * FROM estados ORDER BY cdesta");

        if (count($lista) > 0) {

            return $lista->fetchALL(PDO::FETCH_ASSOC);
        }

        return FALSE;
    }
}