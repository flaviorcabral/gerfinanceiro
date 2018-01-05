<?php

    require_once 'Conexao.class.php';

class Cliente
{
    private $con;

    function __construct()
    {
        $conexao = new Conexao();
        $this->con = $conexao->getConexao();
    }

    //Salvar novo cliente
    function insertCliente($sql)
    {
        if ($this->con->exec($sql)){
            return true;
        }

        return false;
    }

    //Listar todos os clientes ativos pro ordem alfabetica
    function listarClientes()
    {
        $lista = $this->con->query("SELECT * FROM clientes  WHERE LEFT (flativ,1) = 'S' ORDER BY declie");

        if (count($lista) > 0) {

            return $lista->fetchALL(PDO::FETCH_ASSOC);
        }

        return FALSE;
    }

    //Buscar cliente por codigo
    function buscarCliente($cod)
    {
        $busca = $this->con->query("SELECT * FROM clientes WHERE cdclie = '{$cod}'");

        if (count($busca) > 0) {

            return $busca->fetch(PDO::FETCH_ASSOC);
        }

        return false;
    }

    //Atualizar info cliente
    function updateCliente($sql)
    {
        if ($this->con->exec($sql)){
            return true;
        }

        return false;
    }

    //Excluir cliente
    function deleteCliente($codigo)
    {
        if ($this->con->exec("DELETE FROM clientes WHERE cdclie = '{$codigo}'")) {

            return TRUE;
        }

        return FALSE;
    }
}