<?php

require_once 'Conexao.class.php';

class Usuario
{
    private $con;

    function __construct()
    {
        $conexao = new Conexao();
        $this->con = $conexao->getConexao();
    }

    //Valida permissão de acesso
    function validaAcesso($login){

        $result = $this->con->query("SELECT * FROM usuarios WHERE delogin = '{$login}'");

        if (count($result) > 0) {

            return $result->fetch(PDO::FETCH_ASSOC);
        }

        return FALSE;

    }

    //Salvar novo usuario
    function insertUsuario($sql)
    {
        if ($this->con->exec($sql)){
            return true;
        }

        return false;
    }

    //Lista todos os usuarios
    function listaUsuarios()
    {
        $lista = $this->con->query("SELECT * FROM usuarios");

        if (count($lista) > 0) {

            return $lista->fetchALL(PDO::FETCH_ASSOC);
        }

        return FALSE;
    }

    //Busca usuario pela Matricula
    function buscaUsuario($mat)
    {
        $busca = $this->con->query("SELECT * FROM usuarios WHERE cdusua = '{$mat}'");

        if (count($busca) > 0) {

            return $busca->fetch(PDO::FETCH_ASSOC);
        }

        return false;

    }

    //Atualiza dados pelo admin
    function updateDados($sql)
    {
        if ($this->con->exec($sql)){
            return true;
        }

        return false;
    }

    //Atualiza dados pelo usuario
    function updateMeusDados($mat, $nome, $email, $telefone, $camfoto)
    {

        if ($this->con->exec("UPDATE usuarios SET deusua = '{$nome}' , demail = '{$email}', nrtele = '{$telefone}', defoto = '{$camfoto}' WHERE cdusua = '{$mat}'")) {

            return TRUE;
        }

        return FALSE;
    }

    //Atualiza senha do usuario
    function updateSenha($mat, $nvsenha)
    {
        if ($this->con->exec("UPDATE usuarios SET desenh = '{$nvsenha}' WHERE cdusua = '{$mat}'")) {

            return TRUE;
        }

        return FALSE;
    }

    //Delete usuario
    function deleteUsuario($codigo)
    {
        if ($this->con->exec("DELETE FROM usuarios WHERE cdusua = '{$codigo}'")) {

            return TRUE;
        }

        return FALSE;
    }
}