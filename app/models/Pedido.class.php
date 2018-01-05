<?php

    require_once 'Conexao.class.php';

class Pedido
{
    private $con;

    function __construct()
    {
        $conexao = new Conexao();
        $this->con = $conexao->getConexao();
    }

    //Insert novo pedido
    function insertPedido($sql)
    {
        if ($this->con->exec($sql)){
            return true;
        }

        return false;
    }

    //Insert itens pedido
    function insertItensPedido($sql)
    {
        if ($this->con->exec($sql)){
            return true;
        }

        return false;
    }

    //Listar pedidos
    function listaPedidos()
    {
        $lista = $this->con->query("SELECT * FROM pedidos ORDER BY cdpedi");

        if (count($lista) > 0) {

            return $lista->fetchALL(PDO::FETCH_ASSOC);
        }

        return FALSE;
    }

    //Buscar pedido por codigo tabel pedidos
    function buscaPedido($cod)
    {
        $busca = $this->con->query("SELECT * FROM pedidos WHERE cdpedi = '{$cod}'");

        if (count($busca) > 0) {

            return $busca->fetchAll(PDO::FETCH_ASSOC);
        }

        return false;
    }

    //Buscar itens pedido na tabela pedidosi
    function buscaItensPedido($cod)
    {
        $busca = $this->con->query("SELECT * FROM pedidosi WHERE cdpedi = '{$cod}'");

        if (count($busca) > 0) {

            return $busca->fetchAll(PDO::FETCH_ASSOC);
        }

        return false;
    }

    //Busca venda tbl venda com indice
    function buscaPedidoIndices($cod)
    {
        $busca = $this->con->query("SELECT * FROM pedidos WHERE cdpedi = '{$cod}'");

        if (count($busca) > 0) {

            return $busca->fetchAll(PDO::FETCH_ASSOC);
        }

        return false;
    }

    //Busca maior codigo pedido de determinado fornecedor
    function buscaMaxPedidoPorFornecedor($codForn, $dtPedido)
    {
        $lista = $this->con->query("SELECT MAX(cdpedi) cdpedi FROM pedidos WHERE cdforn = '{$codForn}' and dtpedi = '{$dtPedido}'");

        if (count($lista) > 0) {

            return $lista->fetchALL(PDO::FETCH_ASSOC);
        }

        return FALSE;
    }

    //Excluir pedido
    function deletePedido($cod)
    {
        if ($this->con->exec("DELETE FROM pedidos WHERE cdpedi = '{$cod}'")) {

            return TRUE;
        }

        return FALSE;
    }

    //Exluir itens do pedido
    function deleteItensPedido($cod)
    {
        if ($this->con->exec("DELETE FROM pedidosi WHERE cdpedi = '{$cod}'")) {

            return TRUE;
        }

        return FALSE;
    }

    //Buscar total de pedidos por situação
    function totalPedidosSituacao($qual)
    {
        $busca = $this->con->query( "select count(cdpedi) qtde from pedidos where left(status,1) = '{$qual}'");

        if (count($busca) > 0) {

            return $busca->fetchAll(PDO::FETCH_ASSOC);
        }

        return false;
    }
}