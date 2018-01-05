<?php

require_once 'Conexao.class.php';

class Vendas
{
    private $con;

    function __construct()
    {
        $conexao = new Conexao();
        $this->con = $conexao->getConexao();
    }

    //Listar todas as ordens de serviços
    function listaVendas()
    {
        $lista = $this->con->query("SELECT * FROM venda ORDER BY cdvenda");

        if (count($lista) > 0) {

            return $lista->fetchALL(PDO::FETCH_ASSOC);
        }

        return FALSE;
    }

    //Busca maior codigo venda de determinado cliente
    function buscaMaiorVendaPorCliente($codCliente, $dtVenda)
    {
        $lista = $this->con->query("SELECT MAX(cdvenda) cdvenda FROM venda WHERE cdclie = '{$codCliente}' and dtvenda = '{$dtVenda}'");

        if (count($lista) > 0) {

            return $lista->fetchALL(PDO::FETCH_ASSOC);
        }

        return FALSE;
    }

    //Busca venda tbl venda sem indice
    function buscaVenda($cod)
    {
        $busca = $this->con->query("SELECT * FROM venda WHERE cdvenda = '{$cod}'");

        if (count($busca) > 0) {

            return $busca->fetchAll(PDO::FETCH_ASSOC);
        }

        return false;
    }

    //Busca venda tbl venda com indice
    function buscaVendaIndices($cod)
    {
        $busca = $this->con->query("SELECT * FROM venda WHERE cdvenda = '{$cod}'");

        if (count($busca) > 0) {

            return $busca->fetchAll(PDO::FETCH_ASSOC);
        }

        return false;
    }

    //Busca itens da venda
    function buscaItensVenda($cod)
    {
        $busca = $this->con->query("SELECT * FROM vendai WHERE cdvenda = '{$cod}'");

        if (count($busca) > 0) {

            return $busca->fetchAll(PDO::FETCH_ASSOC);
        }

        return false;
    }

    //Salvar venda
    function insertVenda($sql)
    {
        if ($this->con->exec($sql)){
            return true;
        }

        return false;
    }

    //Salva itens venda
    function insertItensVenda($sql)
    {
        if ($this->con->exec($sql)){
            return true;
        }

        return false;
    }

    //Exluir Venda
    function deleteVenda($cod)
    {
        if ($this->con->exec("DELETE FROM venda WHERE cdvenda = '{$cod}'")) {

            return TRUE;
        }

        return FALSE;
    }

    //Exluir itens venda
    function deleteItensVenda($cod)
    {
        if ($this->con->exec("DELETE FROM vendai WHERE cdvenda = '{$cod}'")) {

            return TRUE;
        }

        return FALSE;
    }

    //Buscar total de contas por situação
    function totalContasSituacao($qual)
    {
        $busca = $this->con->query( "select count(cdvenda) qtde from venda where left(cdsitu,1) = '{$qual}'");

        if (count($busca) > 0) {

            return $busca->fetchAll(PDO::FETCH_ASSOC);
        }

        return false;
    }

    //Soma contas
    function somaContas($mes,$tipo)
    {
        $valor=0;

        if ($tipo == 'P') {
            $sql = "SELECT sum(vlcont) valor FROM contas where cdtipo = 'Pagar' and month(dtcont)= {$mes} and year(dtcont) = year(CURRENT_DATE) and (vlpago is null or vlpago <= 0)";
        } Else {
            $sql = "SELECT sum(vlcont) valor FROM contas where cdtipo = 'Receber' and month(dtcont)= {$mes} and year(dtcont) = year(CURRENT_DATE) and (vlpago is null or vlpago <= 0)";
        }

        $resultado = $this->con->query($sql);

        if ($resultado) {
            foreach($resultado as $conta){
                $valor = $conta["valor"];
            }

            return $valor;
        }

        return false;
    }

    //Busca venda por situação diferente de orçamento e Entregue
    function buscarVendaSituacao()
    {
        $sql = "select * from venda where (cdsitu <> 'Orcamento' and cdsitu <> 'Entregue') order by dtvenda";

        $busca = $this->con->query($sql);

        if (count($busca) > 0) {

            return $busca->fetchAll(PDO::FETCH_ASSOC);
        }

        return false;

    }
}