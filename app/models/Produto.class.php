<?php

    require_once 'Conexao.class.php';

class Produto
{
    private $con;

    function __construct()
    {
        $conexao = new Conexao();
        $this->con = $conexao->getConexao();
    }

    //Insert novo produto
    function insertProduto($sql)
    {
        if ($this->con->exec($sql)){
            return true;
        }

        return false;
    }

    //Listas todos os produtos
    function listarProdutos()
    {
        $lista = $this->con->query("SELECT * FROM produtos ORDER BY deprod");

        if (count($lista) > 0) {

            return $lista->fetchALL(PDO::FETCH_ASSOC);
        }

        return FALSE;
    }

    //Buscar produto por codigo
    function buscaProduto($cod)
    {
        $busca = $this->con->query("SELECT * FROM produtos WHERE cdprod = '{$cod}'");

        if (count($busca) > 0) {

            return $busca->fetch(PDO::FETCH_ASSOC);
        }

        return false;
    }

    //Update info produto
    function updateProduto($sql)
    {
        if ($this->con->exec($sql)){
            return true;
        }

        return false;
    }

    //Delete produto
    function deleteProduto($codigo)
    {
        if ($this->con->exec("DELETE FROM produtos WHERE cdprod = '{$codigo}'")) {

            return TRUE;
        }

        return FALSE;
    }

    //Busca total produto estoque
    function estoqueProduto($codigo)
    {
        $busca = $this->con->query("SELECT qtprod FROM produtos WHERE cdprod = '{$codigo}'");

        if (count($busca) > 0) {

            return $busca->fetch(PDO::FETCH_COLUMN);
        }

        return false;
    }

    //Update produto estoque
    function atualizaEstoque($codigo, $qtd)
    {
        if ($this->con->exec("UPDATE produtos SET qtprod = '{$qtd}' WHERE cdprod = '{$codigo}'")) {

            return TRUE;
        }

        return FALSE;
    }

}