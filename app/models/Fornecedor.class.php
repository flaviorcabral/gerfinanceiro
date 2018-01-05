<?php

    require_once 'Conexao.class.php';

class Fornecedor
{
    private $con;

    function __construct()
    {
        $conexao = new Conexao();
        $this->con = $conexao->getConexao();
    }

    //Inserir fornecedor
    function insertFornecedor($sql)
    {
        if ($this->con->exec($sql)){
            return true;
        }

        return false;
    }

    //Listar fornecedores
    function listarFornecedores()
    {
        $lista = $this->con->query("SELECT * FROM fornecedores WHERE LEFT(flativ,1) = 'S' ORDER BY cdforn");

        if (count($lista) > 0) {

            return $lista->fetchALL(PDO::FETCH_ASSOC);
        }

        return FALSE;
    }

    //Busca fornecedor por codigo
    function buscaFornecedor($codigo)
    {
        $busca = $this->con->query("SELECT * FROM fornecedores WHERE cdforn = '{$codigo}'");

        if (count($busca) > 0) {

            return $busca->fetch(PDO::FETCH_ASSOC);
        }

        return false;
    }

    //Atualizar infos fornecedor
    function updateFornecedor($sql)
    {

        if ($this->con->exec($sql)){
            return true;
        }

        return false;
    }

    //Excluir fornecedor
    function deleteFornecedor($codigo)
    {
        if ($this->con->exec("DELETE FROM fornecedores WHERE cdforn = '{$codigo}'")) {

            return TRUE;
        }

        return FALSE;
    }

}