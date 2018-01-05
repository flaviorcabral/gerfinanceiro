<?php

    require_once 'Conexao.class.php';

class Conta
{
    private $con;

    function __construct()
    {
        $conexao = new Conexao();
        $this->con = $conexao->getConexao();
    }

    //Inserir conta
    function insertConta($sql)
    {
        if ($this->con->exec($sql)){
            return true;
        }
        return false;
    }

    //Update conta
    function updateConta($sql)
    {
        if ($this->con->exec($sql)){
            return true;
        }
        return false;
    }

    //Listar todas as contas
    function listarContas($tipo)
    {
        $lista = $this->con->query("SELECT * FROM contas WHERE cdtipo = '{$tipo}' ORDER BY cdcont");

        if (count($lista) > 0) {

            return $lista->fetchALL(PDO::FETCH_ASSOC);
        }

        return FALSE;
    }

    //Buscar conta
    function buscarConta($cod)
    {
        $busca = $this->con->query("SELECT * FROM contas WHERE cdcont = '{$cod}'");

        if (count($busca) > 0) {

            return $busca->fetchAll(PDO::FETCH_ASSOC);
        }

        return false;
    }

    //Deletar conta referente Venda
    function deleteContaVenda($cod)
    {
        if ($this->con->exec("DELETE FROM contas WHERE cdtipo = 'Receber' AND cdorig = '{$cod}'")) {

            return TRUE;
        }

        return FALSE;
    }

    //Deletar conta referente Pedido
    function deleteContaPedido($cod)
    {
        if ($this->con->exec("DELETE FROM contas WHERE cdtipo = 'Pagar' AND cdorig = '{$cod}'")) {

            return TRUE;
        }

        return FALSE;
    }

    //Deletar conta
    function deleteConta($cod)
    {
        if ($this->con->exec("DELETE FROM contas WHERE cdcont = '{$cod}'")) {

            return TRUE;
        }

        return FALSE;
    }

    //Busca contas por forma de pagamento
    function buscarContasFormPag()
    {
        $sql="select * from contas c, venda v where c.cdorig = v.cdvenda and (c.vlpago is null or c.vlpago < 1) and c.cdtipo = 'Receber' order by c.dtcont";

        $busca = $this->con->query($sql);

        if (count($busca) > 0) {

            return $busca->fetchAll(PDO::FETCH_ASSOC);
        }

        return false;
    }
}