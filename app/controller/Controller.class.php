<?php

    session_start();

class Controller
{
    public $venda = null;
    public $pedido = null;
    public $itens = null;
    public $produto = null;
    public $cliente = null;
    public $conta = null;
    public $clientes = null;
    public $fornecedor = null;
    public $fornecedores = null;
    public $usuario = null;
    public $produtos = null;
    public $estados = null;

    private $util;

    function __construct()
    {
        $getUtil = new Util();
        $this->util = $getUtil->getUtil();
    }

    //Valida acesso ao sistema
    function login()
    {
        $delogin = preg_replace('/[^[:alnum:]_]/', '', $_POST["delogin"]);
        $desenh = md5(preg_replace('/[^[:alnum:]_]/', '', $_POST["desenh"]));

        $usuario = new Usuario();

        $result = $usuario->validaAcesso($delogin);

        if ($result) {

            $senha = $result['desenh'];

            if ($senha == $desenh) {
                // dados ok
                $cdusua = $result["cdusua"];
                $deusua = $result["deusua"];
                $cdtipo = substr($result["cdtipo"], 0, 1);
                $defoto = $result["defoto"];
                $demail = $result["demail"];

                $_SESSION['login'] = $deusua;

                date_default_timezone_set("Brazil/East");
                $tempoLimite = 1800; //30 minutos de inatividade

                $_SESSION['logado'] = time();
                $_SESSION['tempo_permitido'] = $tempoLimite;

                setcookie("cdusua", $cdusua);
                setcookie("cdtipo", $cdtipo);
                setcookie("defoto", $defoto);
                setcookie("demail", $demail);

                $delog = "Acesso ao Sistema";

                $this->util->geraLogSistema($cdusua, $delog);

                header('Location: app/views/home.php');

            } else {
                // senha NÃO confere
                $demens = "A senha não confere. Tente novamente!";
                $detitu = "Gerenciador Financeiro | Acesso";
                header('Location: app/views/mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
            }

        } else {
            // Usuario NÃO encontrado
            $demens = "Usuário não cadastrado ou inativo!";
            $detitu = "Gerenciador Financeiro | Acesso";
            header('Location: app/views/mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
        }
    }

    //Efetua logoff do sistema
    function logoff()
    {

        if (isset($_SESSION['login'])) {
            unset($_COOKIE);
            unset($_SESSION['login']);
            session_destroy();
            header('Location: index.php');
            exit;
        }
    }

    //Verifica sessão ativa do usuário
    function verificaSessao()
    {
        if (array_key_exists('login', $_SESSION)) {
           return true;
        }

        return false;
    }

    //Verifca inatividade da sessao
    function verificaInatividade()
    {
        $logado = $_SESSION['logado'];
        $limite = $_SESSION['tempo_permitido'];

        if($logado){
            $segundos = time() - $logado;
        }

        if($segundos > $limite){
            session_destroy();
            header('Location: Location: ../../index.php');
            exit;
        }else{
            $_SESSION['logado'] = time();
        }
    }

    //Salvar novo usuario
    function salvarUsuario($nomes, $dados)
    {

        $sql = "insert into " . "usuarios" . " (";
        $campos = "";
        $total = count($nomes) - 1;

        for ($i = 0; $i < count($nomes); $i++) {

            $campos = $campos . $nomes[$i];

            if ($i < $total) {
                $campos = $campos . ", ";
            }

        }

        $sql = $sql . $campos . ") values (";

        $campos = "";

        for ($x = 0; $x < count($dados); $x++) {

            $campo = "'" . $dados[$x] . "'";

            if ($x < $total) {
                $campos = $campos . $campo . ", ";
            } Else {
                $campos = $campos . $campo . ")";
            }
        }

        $sql = $sql . $campos;

        $user = new Usuario();

        if ($user->insertUsuario($sql)) {

             $delog = "Inclusão de usuario de acesso ao sistema na tabela [usuarios]";

            if (isset($_COOKIE['cdusua'])) {
                 $cdusua = $_COOKIE['cdusua'];
             }

            $this->util->geraLogSistema($cdusua, $delog);


            return true;
        }

        return false;
    }

    //Busca funcionario por matricula
    function buscarUsuario($mat)
    {
        $user = new Usuario();
        $result = $user->buscaUsuario($mat);
        return $result;
    }

    //Lista todos os usuarios cadastrados no sistema
    function listarUsuarios()
    {
        $user = new Usuario();
        $result = $user->listaUsuarios();
        return $result;
    }

    //Atualiza dados do usuario pelo admin
    function atualizaDadosUsuario($nomes, $dados, $codigo)
    {

        $campos = "";
        $total = count($dados) - 1;

        $sql = "update " . "usuarios" . " set ";

        for ($i = 0; $i < count($dados); $i++) {

            $campos = $campos . $nomes[$i] . " = '" . $dados[$i] . "'";

            if ($i < $total) {
                $campos = $campos . ", ";
            }

        }

        $sql = $sql . $campos . " where cdusua = " . "'{$codigo}'";

        $user = new Usuario();

        if ($user->updateDados($sql)) {

            $delog = "Alteração de dados na tabela [usuarios] codigo ". $codigo;

            if (isset($_COOKIE['cdusua'])) {
                $cdusua = $_COOKIE['cdusua'];
            }

            $this->util->geraLogSistema($cdusua, $delog);

            return true;
        }

        return false;
    }

    //Excluir usuario
    function excluirUsuario($cod)
    {
        $user = new Usuario();
        $result = $user->deleteUsuario($cod);

        if($result) {
            $delog = "Exclusão de usuario na tabela [usuarios] codigo " . $cod;

            if (isset($_COOKIE['cdusua'])) {
                $cdusua = $_COOKIE['cdusua'];
            }

            $this->util->geraLogSistema($cdusua, $delog);
            return $result;
        }

        return false;
    }

    //Atualiza dados pelo o usuario
    function atualizaMeusDados()
    {
        $user = new Usuario();

        $cdusua = $_POST["cdusua"];
        $demail = $_POST["demail"];
        $deusua = $_POST["deusua"];
        $defoto = $_POST["defoto"];
        $tel = $_POST["nrtele"];

        // tratando o upload da foto
        $uploaddir = '../../templates/img/' . $cdusua;
        $uploadfile = $uploaddir . basename($_FILES['defoto']['name']);

        // upload do arquivo da foto
        move_uploaded_file($_FILES['defoto']['tmp_name'], $uploadfile);

        $defoto1 = basename($_FILES['defoto']['name']);

        if (!empty($defoto1) == true) {
            $defoto = $uploadfile;
        }

        if ($user->updateMeusDados($cdusua, $deusua, $demail, $tel, $defoto))
        {
            setcookie("deusua", $deusua);
            setcookie("defoto", $defoto);
            setcookie("demail", $demail);

            $delog = "Atualização dados usuario na tabela [usuarios] codigo " . $cdusua;

            if (isset($_COOKIE['cdusua'])) {
                $cdusua = $_COOKIE['cdusua'];
            }

            $this->util->geraLogSistema($cdusua, $delog);

            return true;
        }

        return false;
    }

    //Atualiza senha do usuario
    function updateSenhaUsuario()
    {
        // receber as variaveis usuario (e-mail) e senha
        $data = date('Y-m-d H:i:s');
        $cdusua = $_POST["cdusua"];
        $desenh = md5($_POST["desenh"]);
        $desenh1 = md5($_POST["desenh1"]);

        if (empty($desenh) == true)
        {
            $demens = "É obrigatório informar a nova senha!";
            $detitu = "Gerenciador Financeiro | Alterar Senha";
            $devolt = "minhasenha.php";
            header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu . '&devolt=' . $devolt);
        } Else {

            if ($desenh !== $desenh1) {
                $demens = "As senhas informadas estão diferentes! Favor corrigir.";
                $detitu = "Gerenciador Financeiro | Alterar Senha";
                $devolt = "minhasenha.php";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu . '&devolt=' . $devolt);
            } Else {

                $user = new Usuario();

                if ($user->updateSenha($cdusua, $desenh)) {

                    $delog = "Atualização senha usuario na tabela [usuarios] codigo " . $cdusua;

                    if (isset($_COOKIE['cdusua'])) {
                        $cdusua = $_COOKIE['cdusua'];
                    }

                    $this->util->geraLogSistema($cdusua, $delog);

                    return true;
                }

            }
        }

        return false;
    }

    //Traz informações da empresa
    function infoEmpresa()
    {
        $param = new Parametros();
        $result = $param->informaçõesEmp();
        return $result;
    }

    //Atualizar informações empresa
    function atualizaInfoEmp($dados, $nomes, $codigo)
    {
        $campos="";
        $total=count($dados)-1;

        $sql="update "."parametros"." set ";

        for ($i =0 ; $i < count($dados) ; $i++ ) {

            $campos=$campos.$nomes[$i]." = '".$dados[$i]."'";

            if ($i < $total) {
                $campos=$campos.", ";
            }

        }

        $sql=$sql.$campos." where cdprop = "."'{$codigo}'";

        $param = new Parametros();

        if($param->updateInformacoes($sql))
        {

            $delog = "Alteração de dados na tabela parametros na chave " . $codigo;

            if (isset($_COOKIE['cdusua'])) {
                $cdusua = $_COOKIE['cdusua'];
            }

            $this->util->geraLogSistema($cdusua, $delog);

            return true;
        }

        return false;
    }

    //Salvar venda
    function salvarVenda($dados, $nomes, $proc, $cod)
    {
        $venda = new Vendas();

        $sql = "insert into " . "venda" . " (";
        $campos = "";
        $total = count($nomes) - 1;

        for ($i = 0; $i < count($nomes); $i++) {

            $campos = $campos . $nomes[$i];

            if ($i < $total) {
                $campos = $campos . ", ";
            }

        }

        $sql = $sql . $campos . ") values (";

        $campos = "";

        for ($x = 0; $x < count($dados); $x++) {

            $campo = "'" . $dados[$x] . "'";

            if ($x < $total) {
                $campos = $campos . $campo . ", ";
            } Else {
                $campos = $campos . $campo . ")";
            }
        }

        $sql = $sql . $campos;
        $venda->insertVenda($sql);

        $delog = $proc." de dados na tabela [venda] codigo ".$cod;

        if (isset($_COOKIE['cdusua'])) {
            $cdusua = $_COOKIE['cdusua'];
        }

        $this->util->geraLogSistema($cdusua, $delog);

        return;
    }

    //Salvar itens venda
    function salvarItensVenda($dados, $nomes)
    {
        $venda = new Vendas();

        $sql = "insert into " . "vendai" . " (";
        $campos = "";
        $total = count($nomes) - 1;

        for ($i = 0; $i < count($nomes); $i++) {

            $campos = $campos . $nomes[$i];

            if ($i < $total) {
                $campos = $campos . ", ";
            }

        }

        $sql = $sql . $campos . ") values (";

        $campos = "";

        for ($x = 0; $x < count($dados); $x++) {

            $campo = "'" . $dados[$x] . "'";

            if ($x < $total) {
                $campos = $campos . $campo . ", ";
            } Else {
                $campos = $campos . $campo . ")";
            }
        }

        $sql = $sql . $campos;
        if ($venda->insertItensVenda($sql)) {

            return true;
        }

        return false;
    }

    //Lista todas as vendas
    function listarVendas()
    {
        $venda= new Vendas();
        $result = $venda->listaVendas();
        return $result;
    }

    //Busca venda pelo codigo na tbl ordem retorno sem indice
    function buscarVenda($cod)
    {
        $venda = new Vendas();
        $result = $venda->buscaVenda($cod);
        return $result;
    }

    //Busca venda pelo codigo na tbl ordem retorno com indice
    function buscarVendaCindice($cod)
    {
        $venda = new Vendas();
        $result = $venda->buscaVendaIndices($cod);
        return $result;
    }

    //Buscar itens da venda
    function buscarItensVenda($cod)
    {
        $venda = new Vendas();
        $result = $venda->buscaItensVenda($cod);
        return $result;
    }

    //Buscar maior venda por cliente
    function buscarMaiorVendaPorCliente($codCliente, $dtOrdem)
    {
        $venda = new Vendas();
        $result = $venda->buscaMaiorVendaPorCliente($codCliente, $dtOrdem);
        return $result;
    }

    //Excluir venda
    function excluirVenda($cod)
    {
        $venda = new Vendas();
        $result = $venda->deleteVenda($cod);

        if($result)
        {
            $delog = "Exclusão venda na tabela [venda] codigo ".$cod;

            if (isset($_COOKIE['cdusua'])) {
                $cdusua = $_COOKIE['cdusua'];
            }

            $this->util->geraLogSistema($cdusua, $delog);

            return $result;
        }

        return false;
    }

    //Busca venda por situação diferente de orçamento e Entregue
    function buscaVendaSituacao()
    {
        $venda = new Vendas();
        $result = $venda->buscarVendaSituacao();
        return $result;
    }

    //Exluir itens venda
    function excluirItensVenda($cod)
    {
        $venda = new Vendas();
        $result = $venda->deleteItensVenda($cod);
        return $result;
    }

    //Salvar novo cliente
    function salvarCliente($dados, $nomes)
    {

        $sql = "insert into " . "clientes" . " (";
        $campos = "";
        $total = count($nomes) - 1;

        for ($i = 0; $i < count($nomes); $i++) {

            $campos = $campos . $nomes[$i];

            if ($i < $total) {
                $campos = $campos . ", ";
            }

        }

        $sql = $sql . $campos . ") values (";

        $campos = "";

        for ($x = 0; $x < count($dados); $x++) {

            $campo = "'" . $dados[$x] . "'";

            if ($x < $total) {
                $campos = $campos . $campo . ", ";
            } Else {
                $campos = $campos . $campo . ")";
            }
        }

        $sql = $sql . $campos;

        $cliente = new Cliente();

        $result = $cliente->insertCliente($sql);

        if($result)
        {
            $delog = "Inclusão de clientes na tabela [clientes]";

            if (isset($_COOKIE['cdusua'])) {
                $cdusua = $_COOKIE['cdusua'];
            }

            $this->util->geraLogSistema($cdusua, $delog);

            return $result;
        }

        return false;
    }

    //Excluir cliente
    function excluirCliente($cod)
    {
        $cliente = new Cliente();
        $result = $cliente->deleteCliente($cod);

        if($result)
        {
            $delog = "Exclusão de cliente na tabela [clientes] Cpf/Cnpj " . $cod;

            if (isset($_COOKIE['cdusua'])) {
                $cdusua = $_COOKIE['cdusua'];
            }

            $this->util->geraLogSistema($cdusua,$delog);

            return $result;
        }

        return false;
    }

    //BUscar cliente por codigo
    function buscarCliente($codigo)
    {
        $cliente = new Cliente();
        $result = $cliente->buscarCliente($codigo);
        return $result;
    }

    //Atualizar info cliente
    function atualizarCliente($nomes, $dados, $codigo)
    {
        $campos = "";
        $total = count($dados) - 1;

        $sql = "update " . "clientes" . " set ";

        for ($i = 0; $i < count($dados); $i++) {

            $campos = $campos . $nomes[$i] . " = '" . $dados[$i] . "'";

            if ($i < $total) {
                $campos = $campos . ", ";
            }
        }

        $sql = $sql . $campos . " where cdclie = " . "'{$codigo}'";

        $cliente = new Cliente();

        if ($cliente->updateCliente($sql)) {

            $delog = "Alteração de dados na tabela [clientes] Cpf/Cnpj " . $codigo;

            if (isset($_COOKIE['cdusua'])) {
                $cdusua = $_COOKIE['cdusua'];
            }

            $this->util->geraLogSistema($cdusua,$delog);

            return true;
        }
    }

    //Lista todos os clientes cadastrados no sistema
    function listarClientes()
    {
        $cliente = new Cliente();
        $result = $cliente->listarClientes();
        return $result;
    }

    //Salvar novo fornecedor
    function salvarFornecedor($nomes, $dados)
    {
        $sql = "insert into " . "fornecedores" . " (";
        $campos = "";
        $total = count($nomes) - 1;

        for ($i = 0; $i < count($nomes); $i++) {

            $campos = $campos . $nomes[$i];

            if ($i < $total) {
                $campos = $campos . ", ";
            }

        }

        $sql = $sql . $campos . ") values (";

        $campos = "";

        for ($x = 0; $x < count($dados); $x++) {

            $campo = "'" . $dados[$x] . "'";

            if ($x < $total) {
                $campos = $campos . $campo . ", ";
            } Else {
                $campos = $campos . $campo . ")";
            }
        }

        $sql = $sql . $campos;

        $forn = new Fornecedor();

        if ($forn->insertFornecedor($sql)) {

            $delog = "Inclusão de dados na tabela [fornecedores]";

            if (isset($_COOKIE['cdusua'])) {
                $cdusua = $_COOKIE['cdusua'];
            }

            $this->util->geraLogSistema($cdusua, $delog);

            return true;
        }

        return false;
    }

    //Listar todos os fornecedores
    function listaFornecedores()
    {
        $forn = new Fornecedor();
        $result = $forn->listarFornecedores();
        return $result;
    }

    //Atualiza infos fornecedor
    function atualizarFornecedor($nomes, $dados, $codigo)
    {
        $campos = "";
        $total = count($dados) - 1;

        $sql = "update " . "fornecedores" . " set ";

        for ($i = 0; $i < count($dados); $i++) {

            $campos = $campos . $nomes[$i] . " = '" . $dados[$i] . "'";

            if ($i < $total) {
                $campos = $campos . ", ";
            }

        }

        $sql = $sql . $campos . " where cdforn = " . "'{$codigo}'";

        $forn = new Fornecedor();

        if ($forn->updateFornecedor($sql)) {

            $delog = "Alteração de dados na tabela [fornecedores] Cpf/Cnpj ".$codigo;

            if (isset($_COOKIE['cdusua'])) {
                $cdusua = $_COOKIE['cdusua'];
            }

            $this->util->geraLogSistema($cdusua, $delog);

            return true;
        }

        return false;
    }

    //Buscar fornecedor pelo codigo
    function buscaFornecedor($cod)
    {
        $forn = new Fornecedor();
        $result = $forn->buscaFornecedor($cod);
        return $result;
    }

    //Delete fornecedor
    function excluirFornecedor($cod)
    {
        $forn = new Fornecedor();
        $result = $forn->deleteFornecedor($cod);

        if($result)
        {
            $delog = "Exclusão de dados na tabela [fornecedores] Cpf/Cnpj ".$cod;

            if (isset($_COOKIE['cdusua'])) {
                $cdusua = $_COOKIE['cdusua'];
            }

            $this->util->geraLogSistema($cdusua, $delog);

            return true;
        }

        return false;
    }

    //Salvar pedido
    function salvarPedido($nomes, $dados, $proc, $cod)
    {

        $sql="insert into "."pedidos"." (";
        $campos="";
        $total=count($nomes)-1;

        for ($i=0 ; $i < count($nomes) ; $i++ ) {

            $campos=$campos.$nomes[$i];

            if ($i < $total) {
                $campos=$campos.", ";
            }

        }

        $sql=$sql.$campos.") values (";

        $campos="";

        for ($x =0 ; $x < count($dados) ; $x++ ) {

            $campo="'".$dados[$x]."'";

            if ($x < $total) {
                $campos=$campos.$campo.", ";
            } Else {
                $campos=$campos.$campo.")";
            }
        }

        $sql=$sql.$campos;
        $ped = new Pedido();

        if($ped->insertPedido($sql)){

            $delog = $proc . " de dados na tabela [pedidos] codigo ".$cod;

            if (isset($_COOKIE['cdusua'])) {
                $cdusua = $_COOKIE['cdusua'];
            }

            $this->util->geraLogSistema($cdusua, $delog);

            return true;

        }

        return false;
    }

    //Salvar itens pedido
    function salvarItensPedido($nomes, $dados)
    {

        $sql="insert into "."pedidosi"." (";
        $campos="";
        $total=count($nomes)-1;

        for ($i=0 ; $i < count($nomes) ; $i++ ) {

            $campos=$campos.$nomes[$i];

            if ($i < $total) {
                $campos=$campos.", ";
            }

        }

        $sql=$sql.$campos.") values (";

        $campos="";

        for ($x =0 ; $x < count($dados) ; $x++ ) {

            $campo="'".$dados[$x]."'";

            if ($x < $total) {
                $campos=$campos.$campo.", ";
            } Else {
                $campos=$campos.$campo.")";
            }
        }

        $sql=$sql.$campos;
        $ped = new Pedido();

        if($ped->insertPedido($sql)){

            return true;

        }

        return false;
    }

    //Listar todos os pedidos
    function listaPedidos()
    {
        $ped = new Pedido();
        $result = $ped->listaPedidos();
        return $result;
    }

    //Buscar pedido por codigo
    function buscarPedido($codigo)
    {
        $ped = new Pedido();
        $result = $ped->buscaPedido($codigo);
        return $result;
    }

    //BUscar pedido com Indice
    function buscarPedidoCIndice($codigo)
    {
        $ped = new Pedido();
        $result = $ped->buscaPedidoIndices($codigo);
        return $result;
    }

    //Buscar intes do pedido
    function buscaItensPedido($codigo)
    {
        $ped = new Pedido();
        $result = $ped->buscaItensPedido($codigo);
        return $result;
    }

    //Busca maior numero pedido por fornecedor
    function buscarMaiorPedidoFornecedor($codForne, $dataPed)
    {
        $ped = new Pedido();
        $result = $ped->buscaMaxPedidoPorFornecedor($codForne, $dataPed);
        return $result;
    }

    //Excluir pedido
    function excluirPedido($codigo)
    {
        $ped = new Pedido();
        $result = $ped->deletePedido($codigo);

        if($result)
        {
            $delog = "Exclusão de dados na tabela [pedidos] codigo ".$codigo;

            if (isset($_COOKIE['cdusua'])) {
                $cdusua = $_COOKIE['cdusua'];
            }

            $this->util->geraLogSistema($cdusua, $delog);

            return true;
        }

        return false;
    }

    //Excluir itens do pedido
    function excluirItensPedido($codigo)
    {
        $ped = new Pedido();
        $result = $ped->deleteItensPedido($codigo);
        return $result;
    }

    //Salvar novo produto
    function salvarProduto($nomes, $dados)
    {
        $sql="insert into "."produtos"." (";
        $campos="";
        $total=count($nomes)-1;

        for ($i=0 ; $i < count($nomes) ; $i++ ) {

            $campos=$campos.$nomes[$i];

            if ($i < $total) {
                $campos=$campos.", ";
            }

        }

        $sql=$sql.$campos.") values (";

        $campos="";

        for ($x =0 ; $x < count($dados) ; $x++ ) {

            $campo="'".$dados[$x]."'";

            if ($x < $total) {
                $campos=$campos.$campo.", ";
            } Else {
                $campos=$campos.$campo.")";
            }
        }

        $sql=$sql.$campos;

        $prod = new Produto();

        if($prod->insertProduto($sql))
        {
            $delog = "Inclusão de dados na tabela [produtos]";

            if (isset($_COOKIE['cdusua'])) {
                $cdusua = $_COOKIE['cdusua'];
            }

            $this->util->geraLogSistema($cdusua, $delog);

            return true;
        }

        return false;
    }

    //Listar todos os produtos
    function listarProdutos()
    {
        $produtos = new Produto();
        $result = $produtos->listarProdutos();
        return $result;
    }

    //Buscar produto
    function buscarProduto($codigo)
    {
        $prod = new Produto();
        $result = $prod->buscaProduto($codigo);
        return $result;
    }

    //Atualizar info produto
    function atualizaProduto($nomes, $dados, $cod)
    {
        $campos="";
        $total=count($dados)-1;

        $sql="update "."produtos"." set ";

        for ($i =0 ; $i < count($dados) ; $i++ ) {

            $campos=$campos.$nomes[$i]." = '".$dados[$i]."'";

            if ($i < $total) {
                $campos=$campos.", ";
            }

        }

        $sql=$sql.$campos." where cdprod = "."'{$cod}'";

        $prod = new Produto();

        if($prod->updateProduto($sql))
        {
            $delog = "Alteração de dados na tabela [produtos] codigo ".$cod;

            if (isset($_COOKIE['cdusua'])) {
                $cdusua = $_COOKIE['cdusua'];
            }

            $this->util->geraLogSistema($cdusua, $delog);

            return true;

        }

        return false;
    }

    //Excluir produto
    function excluirProduto($codigo)
    {
        $prod = new Produto();
        $result = $prod->deleteProduto($codigo);

        if($result)
        {
            $delog = "Exclusão de dados na tabela [produtos] codigo ".$codigo;

            if (isset($_COOKIE['cdusua'])) {
                $cdusua = $_COOKIE['cdusua'];
            }

            $this->util->geraLogSistema($cdusua, $delog);

            return $result;

        }

        return false;
    }

    //Exibir valor produto
    function exibeValorProduto($valor)
    {
        $valor = $this->util->trataEntradaValor($valor);
        $valor = $this->util->trataSaidaValor($valor);

        return $valor;
    }

    //Atualiza quantidade de produto em estoque
    function atualizarEstoque($codigo, $qtd)
    {
        $prod = new Produto();
        $result = $prod->atualizaEstoque($codigo, $qtd);

        if($result)
        {
            $delog = "Atualização de dados na tabela [produtos] codigo ".$codigo;

            if (isset($_COOKIE['cdusua'])) {
                $cdusua = $_COOKIE['cdusua'];
            }

            $this->util->geraLogSistema($cdusua, $delog);

            return $result;

        }

        return false;
    }

    //Buscar quantidade de produto no estoque
    function buscaQtdProdutoEstoque($codigo)
    {
        $prod = new Produto();
        $result = $prod->estoqueProduto($codigo);
        return $result;
    }

    //Lista estados brasileiros
    function listarEstadosBra()
    {
        $est = new Estados();
        $result = $est->listarEstados();
        return $result;
    }

    //Salvar conta
    function salvarConta($nomes, $dados)
    {
        $sql="insert into "."contas"." (";
        $campos = "";
        $total = count($nomes) - 1;

        for ($i = 0; $i < count($nomes); $i++) {

            $campos = $campos . $nomes[$i];

            if ($i < $total) {
                $campos = $campos . ", ";
            }

        }

        $sql = $sql . $campos . ") values (";

        $campos = "";

        for ($x = 0; $x < count($dados); $x++) {

            $campo = "'" . $dados[$x] . "'";

            if ($x < $total) {
                $campos = $campos . $campo . ", ";
            } Else {
                $campos = $campos . $campo . ")";
            }
        }

        $sql = $sql . $campos;

        $conta = new Conta();

        if ($conta->insertConta($sql)) {

            $delog = "Inclusão de dados na tabela [contas]";

            if (isset($_COOKIE['cdusua'])) {
                $cdusua = $_COOKIE['cdusua'];
            }

            $this->util->geraLogSistema($cdusua, $delog);

            return true;
        }

        return false;
    }

    //Atualizar conta
    function atualizaConta($nomes, $dados, $cod)
    {
        $campos="";
        $total=count($dados)-1;

        $sql="update "."contas"." set ";

        for ($i =0 ; $i < count($dados) ; $i++ ) {

            $campos=$campos.$nomes[$i]." = '".$dados[$i]."'";

            if ($i < $total) {
                $campos=$campos.", ";
            }

        }

        $sql=$sql.$campos." where cdcont = "."'{$cod}'";

        $conta = new Conta();

        if($conta->updateConta($sql)) {


            $delog = "Alteração de dados na tabela [contas] chave " . $cod;

            if (isset($_COOKIE['cdusua'])) {
                $cdusua = $_COOKIE['cdusua'];
            }

            $this->util->geraLogSistema($cdusua, $delog);

            return true;
        }

        return false;
    }

    //Buscar conta
    function buscaConta($cod)
    {
        $conta = new Conta();
        $result = $conta->buscarConta($cod);
        return $result;
    }

    //Excluir conta referente Venda
    function excluirContaVenda($cod)
    {
        $conta = new Conta();
        $result = $conta->deleteContaVenda($cod);
        return $result;
    }

    //Excluir conta referente Pedido
    function excluirContaPedido($cod)
    {
        $conta = new Conta();
        $result = $conta->deleteContaPedido($cod);
        return $result;
    }

    //Excluir qualquer tipo de conta
    function excluirConta($cod)
    {
        $conta = new Conta();
        $result = $conta->deleteConta($cod);

        if($result)
        {
            $delog = "Exclusão de dados na tabela [contas] chave " . $cod;

            if (isset($_COOKIE['cdusua'])) {
                $cdusua = $_COOKIE['cdusua'];
            }

            $this->util->geraLogSistema($cdusua, $delog);

            return $result;
        }

        return false;
    }

    //Listar contas
    function listaContas($tipo)
    {
        $conta = new Conta();
        $result = $conta->listarContas($tipo);
        return $result;
    }

    //Buscar total de vendas por situação
    function totalContasSituacao($qual)
    {
        $qtde=0;

        $venda = new Vendas();

        $resultado= $venda->totalContasSituacao($qual);

        if ($resultado) {
            foreach($resultado as $conta){
                $qtde=$conta["qtde"];
            }

            return $qtde;
        }

        return false;
    }

    //Buscar total de pedidos por situação
    function totalPedidosSituacao($qual)
    {
        $qtde=0;

        $pedido = new Pedido();

        $resultado= $pedido->totalPedidosSituacao($qual);

        if ($resultado) {
            foreach($resultado as $pedido){
                $qtde=$pedido["qtde"];
            }

            return $qtde;
        }

        return false;
    }

    //Somar contas
    function somarTotalValorContas($mes,$tipo)
    {
        $venda = new Vendas();
        $result =  $venda->somaContas($mes, $tipo);
        return $result;
    }

    //Buscar conta por forma de pagamento
    function buscaContasPorFormaPag()
    {
        $conta = new Conta();
        $result = $conta->buscarContasFormPag();
        return $result;
    }

    //Listar historico de logs
    function listaHistorico()
    {
        $log = new logsistema();
        $result = $log->listaHistorico();
        return $result;
    }

    //Pagina index.php
    function pagLogin()
    {
        if(isset($_REQUEST['login']))
        {
            $this->login();
        }

        if(isset($_REQUEST['logoff']))
        {
            $this->logoff();
        }
    }

    //Pagina home.php
    function pagHome()
    {
        if (!$this->verificaSessao()) {
            header('Location: ../../index.php');
            exit;
        }

        $this->verificaInatividade();

    }

    //Pagina meus dados e senha
    function pagsMeusDados()
    {
        if (!$this->verificaSessao()) {
            header('Location: ../../index.php');
            exit;
        }

        $this->verificaInatividade();

        if (isset($_REQUEST['atualiza']))
        {
            if ($this->atualizaMeusDados()) {

                $demens = "Cadastro atualizado com sucesso!";

            } else {
                $demens = "Ocorreu um problema durante atualização de dados. Se persistir contate o Suporte!";
            }

            $detitu = "Gerenciador Financeiro | Meus Dados";
            $devolt = "home.php";
            header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu . '&devolt=' . $devolt);
        }

        if (isset($_REQUEST['atualizaSenha']))
        {

            if ($this->updateSenhaUsuario()) {

                $demens = "Senha atualizada com sucesso!";

            } else {
                $demens = "Ocorreu um problema durante atualização de senha. Se persistir contate o Suporte!";
            }

            $detitu = "Gerenciador Financeiro | Alterar Senha";
            $devolt = "home.php";
            header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu . '&devolt=' . $devolt);
        }
    }

    //Pagina clienteacoes.php
    function pagClientes()
    {
        if (!$this->verificaSessao()) {
            header('Location: ../../index.php');
            exit;
        }

        $this->verificaInatividade();

        $data = date('Y-m-d');
        $acao = $_REQUEST['acao'];

        $flag = true;
        $flag2 = false;
        $this->estados = $this->listarEstadosBra();

        if ($flag == true) {

            if ($acao == 'ver' or $acao == 'edita' or $acao == 'apaga') {
                $chave = $_REQUEST['chave'];
                $this->cliente = $this->buscarCliente($chave);
            }

            if (isset($_REQUEST['editar'])) {
                $cdclie = $_POST["cdclie"];

                if (strlen($cdclie) < 12) {
                    $cdclie = $this->util->RetirarMascara($cdclie, "cpf");
                    if ($this->util->validaCPF($cdclie) == false) {
                        $demens = "Cpf inválido!";
                        $detitu = "Gerenciador Financeiro | Cadastro de Clientes";
                        header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                        $flag = false;
                    }
                } Else {
                    $cdclie = $this->util->utilRetirarMascara($cdclie, "cnpj");
                    if ($this->util->validaCNPJ($cdclie) == false) {
                        $demens = "Cnpj inválido!";
                        $detitu = "Gerenciador Financeiro | Cadastro de Clientes";
                        header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                        $flag = false;
                    }
                }

                if ($flag2 == true) {
                } Else {

                    //campos da tabela
                    $aNomes = array();

                    $aNomes[] = "cdclie";
                    $aNomes[] = "declie";
                    $aNomes[] = "cdtipo";
                    $aNomes[] = "nrinsc";
                    $aNomes[] = "nrccm";
                    $aNomes[] = "nrrg";
                    $aNomes[] = "deende";
                    $aNomes[] = "nrende";
                    $aNomes[] = "decomp";
                    $aNomes[] = "debair";
                    $aNomes[] = "decida";
                    $aNomes[] = "cdesta";
                    $aNomes[] = "nrcepi";
                    $aNomes[] = "nrtele";
                    $aNomes[] = "nrcelu";
                    $aNomes[] = "demail";
                    $aNomes[] = "deobse";
                    $aNomes[] = "flativ";
                    $aNomes[] = "dtcada";

                    //dados da tabela
                    $aDados = array();
                    $aDados[] = $_POST["cdclie"];
                    $aDados[] = $_POST["declie"];
                    $aDados[] = $_POST["cdtipo"];
                    $aDados[] = $_POST["nrinsc"];
                    $aDados[] = $_POST["nrccm"];
                    $aDados[] = $_POST["nrrg"];
                    $aDados[] = $_POST["deende"];
                    $aDados[] = $_POST["nrende"];
                    $aDados[] = $_POST["decomp"];
                    $aDados[] = $_POST["debair"];
                    $aDados[] = $_POST["decida"];
                    $aDados[] = $_POST["cdesta"];
                    $aDados[] = $_POST["nrcepi"];
                    $aDados[] = $_POST["nrtele"];
                    $aDados[] = $_POST["nrcelu"];
                    $aDados[] = $_POST["demail"];
                    $aDados[] = $_POST["deobse"];
                    $aDados[] = "S";
                    $aDados[] = $data;

                    if ($this->atualizarCliente($aNomes, $aDados, $chave)) {

                        if ($flag2 == false) {
                            $demens = "Atualização efetuada com sucesso!";
                        }
                    } else {
                        $demens = "Ocorreu um problema na atualização/exclusão. Se persistir contate o Suporte!";
                    }
                }

                $detitu = "Gerenciador Financeiro | Cadastro de Clientes";
                $devolt = "cliente.php";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu . '&devolt=' . $devolt);
            }

            if (isset($_REQUEST['apagar'])) {

                $cdclie = $_POST["cdclie"];

                $result = $this->excluirCliente($cdclie);

                if ($flag2 == false and $result == true) {

                    $demens = "Exclusão efetuada com sucesso!";

                } else {
                    $demens = "Ocorreu um problema na exclusão. Se persistir contate o Suporte!";
                }

                $detitu = "Gerenciador Financeiro | Cadastro de Clientes";
                $devolt = "cliente.php";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu . '&devolt=' . $devolt);
            }

            if (isset($_REQUEST['salvar'])) {

                $cdclie = $_POST["cdclie"];

                $Flag = true;

                if (strlen($cdclie) < 12) {
                    $cdclie = $this->util->RetirarMascara($cdclie, "cpf");
                    if ($this->util->validaCPF($cdclie) == false) {
                        $demens = "Cpf inválido!";
                        $detitu = "Gerenciador Financeiro | Cadastro de Clientes";
                        header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                        $Flag = false;
                    }
                } Else {
                    $cdclie = $this->util->RetirarMascara($cdclie, "cnpj");
                    if ($this->util->validaCNPJ($cdclie) == false) {
                        $demens = "Cnpj inválido!";
                        $detitu = "Gerenciador Financeiro | Cadastro de Clientes";
                        header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                        $Flag = false;
                    }
                }

                $result = $this->buscarCliente($cdclie);

                if ($result) {
                    $demens = "Cpf/Cnpj já cadastrado!";
                    $detitu = "Gerenciador Financeiro | Cadastro de Clientes";
                    header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                    $Flag = false;
                }

                if ($Flag == true) {

                    //campos da tabela
                    $aNomes = array();
                    $aNomes[] = "cdclie";
                    $aNomes[] = "declie";
                    $aNomes[] = "cdtipo";
                    $aNomes[] = "nrinsc";
                    $aNomes[] = "nrccm";
                    $aNomes[] = "nrrg";
                    $aNomes[] = "deende";
                    $aNomes[] = "nrende";
                    $aNomes[] = "decomp";
                    $aNomes[] = "debair";
                    $aNomes[] = "decida";
                    $aNomes[] = "cdesta";
                    $aNomes[] = "nrcepi";
                    $aNomes[] = "nrtele";
                    $aNomes[] = "nrcelu";
                    $aNomes[] = "demail";
                    $aNomes[] = "deobse";
                    $aNomes[] = "flativ";
                    $aNomes[] = "dtcada";

                    //dados da tabela
                    $aDados = array();
                    $aDados[] = intval($_POST["cdclie"]);
                    $aDados[] = $_POST["declie"];
                    $aDados[] = $_POST["cdtipo"];
                    $aDados[] = $_POST["nrinsc"];
                    $aDados[] = $_POST["nrccm"];
                    $aDados[] = $_POST["nrrg"];
                    $aDados[] = $_POST["deende"];
                    $aDados[] = $_POST["nrende"];
                    $aDados[] = $_POST["decomp"];
                    $aDados[] = $_POST["debair"];
                    $aDados[] = $_POST["decida"];
                    $aDados[] = $_POST["cdesta"];
                    $aDados[] = $_POST["nrcepi"];
                    $aDados[] = $_POST["nrtele"];
                    $aDados[] = $_POST["nrcelu"];
                    $aDados[] = $_POST["demail"];
                    $aDados[] = $_POST["deobse"];
                    $aDados[] = "S";
                    $aDados[] = $data;

                    if ($this->salvarCliente($aDados, $aNomes)) {

                        $demens = "Cadastro efetuado com sucesso!";

                    } else {
                        $demens = "Ocorreu um problema durante o cadastro. Se persistir contate o suporte!";
                    }
                }

                $detitu = "Gerenciador Financeiro | Cadastro de Clientes";
                $devolt = "cliente.php";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu . '&devolt=' . $devolt);

            }

        }
    }

    //Pagina ordem de vendaacoes.php
    function pagVendas()
    {
        if (!$this->verificaSessao()) {
            header('Location: ../../index.php');
            exit;
        }

        $this->verificaInatividade();

        $acao = $_REQUEST['acao'];

        if ($acao == 'nova') {
            $this->clientes = $this->listarClientes();
            $this->produtos = $this->listarProdutos();

        }

        if ($acao == 'ver' or $acao == 'edita' or $acao == 'apaga') {

            $chave = $_REQUEST['chave'];
            $this->venda = $this->buscarVenda($chave);
            $this->itens = $this->buscarItensVenda($chave);
            $this->clientes = $this->listarClientes();
            $this->produtos = $this->listarProdutos();

        }

        if (isset($_REQUEST['editar'])) {

            $cdvenda = $_REQUEST["cdorde"];

            $busca = $this->buscarVendaCindice($cdvenda);
            $statusold = $busca[0]["cdsitu"];

            $status = $_POST["cdsitu"];

            if($statusold != "Orcamento") {

                $itensVenda = $this->buscarItensVenda($cdvenda);

                foreach ($itensVenda as $item) {

                    $cod = intval($item['cdprod']);
                    $qtdItem = intval($item['qtprod']);

                    $qtdProdEst = intval($this->buscaQtdProdutoEstoque($cod));

                    $qtdProdEst += $qtdItem;

                    $this->atualizarEstoque($cod, $qtdProdEst);

                }
            }

            $dtcada = date('Y-m-d');
            $Flag = true;

            $codItem = $_POST["cditem"];
            $qtdItem = $_POST["qtitem"];
            $vlrItem = $_POST["vlitem"];
            $cdclie = $_POST["cdclie"];
            $dtvenda = $_POST["dtorde"];
            $vlvenda = $_POST["vlorde"];
            $vlpago = $_POST["vlpago"];
            $vlvenda = str_replace(".", "", $vlvenda);
            $vlvenda = str_replace(",", ".", $vlvenda);
            $vlpago = str_replace(".", "", $vlpago);
            $vlpago = str_replace(",", ".", $vlpago);

            $qtitem = 0;

            for ($f = 1; $f <= 20; $f++) {

                $aPrimeiro = explode("|", $codItem[$f]);

                if ($aPrimeiro[0] !== 'X') {
                    $qtitem++;
                }
            }

            if ($qtitem <= 0) {

                $demens = "É preciso informar os itens da Venda!";
                $detitu = "Gerenciador Financeiro | Cadastro de Venda";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                $Flag = false;
                exit;
            }

            if (empty($cdclie) == true) {

                $demens = "É preciso informar o Cliente!";
                $detitu = "Gerenciador Financeiro | Cadastro de Venda";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                $Flag = false;
                exit;
            }

            if (empty(strtotime($dtvenda)) == true) {

                $demens = "É preciso informar a data da Venda!";
                $detitu = "Gerenciador Financeiro | Cadastro de Venda";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                $Flag = false;
                exit;
            }

            //Implementando controle de estoque
            if($status != "Orcamento") {
                for ($f = 1; $f <= 20; $f++) {

                    $aPrimeiro = explode("|", $codItem[$f]);

                    if ($aPrimeiro[0] !== 'X') {
                        if ($aPrimeiro[0] == 'P') {

                            $cdprod = $aPrimeiro[2];
                            $qtprod = intval($qtdItem[$f]);

                            $qtdProdEst = intval($this->buscaQtdProdutoEstoque($cdprod));

                            if ($qtdProdEst >= $qtprod) {
                                $qtdProdEst -= $qtprod;
                                $qtd = $qtdProdEst;

                                $this->atualizarEstoque($cdprod, $qtd);

                            } else {

                                $demens = "Venda possui quantidade de produto maior que estoque!";
                                $detitu = "Gerenciador | Alteração da Venda";
                                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                                $Flag = false;
                                exit;
                            }

                        }
                    }
                }
            }

            if ($Flag == true) {

                $this->excluirVenda($cdvenda);
                $this->excluirItensVenda($cdvenda);
                $this->excluirContaVenda($cdvenda);


                if($_POST["qtform"] == 0){
                    $parcPague = 1;
                }else{
                    $parcPague = $_POST["qtform"];
                }

                //campos da tabela
                $aNomes = array();
                $aNomes[] = "cdvenda";
                $aNomes[] = "cdclie";
                $aNomes[] = "cdsitu";
                $aNomes[] = "dtvenda";
                $aNomes[] = "vlvenda";
                $aNomes[] = "cdform";
                $aNomes[] = "qtform";
                $aNomes[] = "vlpago";
                $aNomes[] = "dtpago";
                $aNomes[] = "deobse";
                $aNomes[] = "flativ";
                $aNomes[] = "dtcada";

                //dados da tabela
                $aDados = array();
                $aDados[] = $_POST["cdorde"];
                $aDados[] = $_POST["cdclie"];
                $aDados[] = $_POST["cdsitu"];
                $aDados[] = $_POST["dtorde"];
                $aDados[] = $vlvenda;
                $aDados[] = $_POST["cdform"];
                $aDados[] = $parcPague;
                $aDados[] = $vlpago;
                $aDados[] = $_POST["dtpago"];
                $aDados[] = $_POST["deobse"];
                $aDados[] = 'Sim';
                $aDados[] = $dtcada;

                $proc = "Alteração";
                $chave = $_POST["cdorde"];

                $this->salvarVenda($aDados, $aNomes, $proc, $chave);

                $nritem = 1;
                for ($f = 1; $f <= 20; $f++) {

                    $aPrimeiro = explode("|", $codItem[$f]);

                    if ($aPrimeiro[0] !== 'X') {
                        $cdprod = $aPrimeiro[2];
                        $qtprod = $qtdItem[$f];
                        $vlprod = $vlrItem[$f];
                        $vltota = $qtprod * $vlprod;

                        $aNomes = array();
                        $aNomes[] = "cdvenda";
                        $aNomes[] = "nritem";
                        $aNomes[] = "cdprod";
                        $aNomes[] = "qtprod";
                        $aNomes[] = "vlprod";
                        $aNomes[] = "vltota";

                        $aDados = array();
                        $aDados[] = $cdvenda;
                        $aDados[] = $nritem++;
                        $aDados[] = $cdprod;
                        $aDados[] = $qtprod;
                        $aDados[] = $vlprod;
                        $aDados[] = $vltota;

                        $this->salvarItensVenda($aDados, $aNomes);

                    }
                }

                $busca = $this->buscarVendaCindice($cdvenda);
                $dtvenda = $busca[0]["dtvenda"];
                $qtform = $busca[0]["qtform"];

                if($qtform === "0"){

                    $qtform = 1;

                }

                if($status != "Orcamento") {
                    for ($f = 1; $f <= $qtform; $f++) {

                        $vlcont = $busca[0]["vlvenda"] / $qtform;
                        $dtcont = strtotime($dtvenda . "+ {$f} months");
                        $dtcont = date("Y-m-d", $dtcont);

                        $aNomes = array();
                        $aNomes[] = "decont";
                        $aNomes[] = "dtcont";
                        $aNomes[] = "vlcont";
                        $aNomes[] = "cdtipo";
                        $aNomes[] = "cdquem";
                        $aNomes[] = "cdorig";
                        $aNomes[] = "flativ";
                        $aNomes[] = "dtcada";

                        $aDados = array();
                        $aDados[] = 'Cliente a Receber';
                        $aDados[] = $dtcont;
                        $aDados[] = $vlcont;
                        $aDados[] = 'Receber';
                        $aDados[] = $busca[0]["cdclie"];
                        $aDados[] = $busca[0]["cdvenda"];
                        $aDados[] = 'Sim';
                        $aDados[] = $dtcada;

                        $this->salvarConta($aNomes, $aDados);

                    }
                }

                $demens = "Alteração efetuada com sucesso!";
                $detitu = "Gerenciador Financeiro | Cadastro de Venda";
                $devolt = "venda.php";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu . '&devolt=' . $devolt);
            }

        }

        if (isset($_REQUEST['apagar']))
        {
            $cdvenda = $_REQUEST["cdorde"];

            $itensVenda = $this->buscarItensVenda($cdvenda);

            foreach($itensVenda as $item)
            {
                $cod = intval($item['cdprod']);
                $qtdItem = intval($item['qtprod']);

                $qtdProdEst = intval($this->buscaQtdProdutoEstoque($cod));

                $qtdProdEst += $qtdItem;

                $this->atualizarEstoque($cod, $qtdProdEst);

            }

            if ($this->excluirVenda($cdvenda) and $this->excluirItensVenda($cdvenda) and $this->excluirContaVenda($cdvenda)) {
                $demens = "Exclusão efetuada com sucesso!";

            } else {
                $demens = "Ocorreu um problema durante exclusão da Venda Se persistir contate o Suporte!";
            }

            $detitu = "Gerenciador Financeiro | Cadastro de Venda";
            $devolt = "venda.php";
            header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu . '&devolt=' . $devolt);
        }

        if (isset($_REQUEST['salvar']))
        {
            $dtcada = date('Y-m-d');
            $Flag = true;

            $aCditem = $_POST["cditem"];
            $aQtitem = $_POST["qtitem"];
            $aVlitem = $_POST["vlitem"];
            $cdclie = $_POST["cdclie"];
            $dtvenda = $_POST["dtorde"];
            $vlvenda = $_POST["vlorde"];
            $vlpago = $_POST["vlpago"];
            $status = $_POST["cdsitu"];
            $vlvenda = str_replace(".", "", $vlvenda);
            $vlvenda = str_replace(",", ".", $vlvenda);
            $vlpago = str_replace(".", "", $vlpago);
            $vlpago = str_replace(",", ".", $vlpago);
            $qtitem = 0;

            for ($f = 1; $f <= 20; $f++) {

                $aPrimeiro = explode("|", $aCditem[$f]);
                if ($aPrimeiro[0] !== 'X') {
                    $qtitem++;
                }
            }

            if ($qtitem <= 0) {

                $demens = "É preciso informar os itens da Venda!";
                $detitu = "Gerenciador Financeiro | Lançamendo de Venda";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                $Flag = false;
            }

            if (empty($cdclie) == true) {

                $demens = "É preciso informar o cliente!";
                $detitu = "Gerenciador Financeiro | Lançamendo de Venda";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                $Flag = false;
            }

            if (empty(strtotime($dtvenda)) == true) {

                $demens = "É preciso informar a data de abertura da Venda!";
                $detitu = "Gerenciador Financeiro | Lançamendo de Venda";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                $Flag = false;
            }

            //Implementando controle de estoque
            if($status != "Orcamento") {
                for ($f = 1; $f <= 20; $f++) {

                    $aPrimeiro = explode("|", $aCditem[$f]);

                    if ($aPrimeiro[0] !== 'X') {
                        if ($aPrimeiro[0] == 'P') {

                            $cdprod = $aPrimeiro[2];
                            $qtprod = intval($aQtitem[$f]);

                            $qtdProdEst = intval($this->buscaQtdProdutoEstoque($cdprod));

                            if ($qtdProdEst >= $qtprod) {
                                $qtdProdEst -= $qtprod;
                                $qtd = $qtdProdEst;

                                $this->atualizarEstoque($cdprod, $qtd);

                            } else {

                                $demens = "Venda possui produto a maior que estoque!";
                                $detitu = "Gerenciador Financeiro | Lançamendo de Venda";
                                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                                $Flag = false;

                            }

                        }
                    }
                }
            }

            if ($Flag == true) {

                if($_POST["qtform"] == 0){
                    $parcPague = 1;
                }else{
                    $parcPague = $_POST["qtform"];
                }

                //campos da tabela
                $aNomes = array();
                $aNomes[] = "cdclie";
                $aNomes[] = "cdsitu";
                $aNomes[] = "dtvenda";
                $aNomes[] = "vlvenda";
                $aNomes[] = "cdform";
                $aNomes[] = "qtform";
                $aNomes[] = "vlpago";
                $aNomes[] = "dtpago";
                $aNomes[] = "deobse";
                $aNomes[] = "flativ";
                $aNomes[] = "dtcada";

                //dados da tabela
                $aDados = array();
                $aDados[] = $_POST["cdclie"];
                $aDados[] = $status;
                $aDados[] = $_POST["dtorde"];
                $aDados[] = $vlvenda;
                $aDados[] = $_POST["cdform"];
                $aDados[] = $parcPague;
                $aDados[] = $vlpago;
                $aDados[] = $_POST["dtpago"];
                $aDados[] = $_POST["deobse"];
                $aDados[] = 'Sim';
                $aDados[] = $dtcada;

                $proc = "Inclusão";
                $chave = "";

                $this->salvarVenda($aDados, $aNomes, $proc, $chave);

                $result = $this->buscarMaiorVendaPorCliente($cdclie, $dtvenda);

                $cdvenda = $result[0]["cdvenda"];

                $nritem = 1;
                for ($f = 1; $f <= 20; $f++) {

                    $aPrimeiro = explode("|", $aCditem[$f]);

                    if ($aPrimeiro[0] !== 'X') {

                        $cdprod = $aPrimeiro[2];
                        $qtprod = $aQtitem[$f];
                        $vlprod = $aVlitem[$f];
                        $vltota = $qtprod * $vlprod;

                        $aNomes = array();
                        $aNomes[] = "cdvenda";
                        $aNomes[] = "nritem";
                        $aNomes[] = "cdprod";
                        $aNomes[] = "qtprod";
                        $aNomes[] = "vlprod";
                        $aNomes[] = "vltota";

                        $aDados = array();
                        $aDados[] = $cdvenda;
                        $aDados[] = $nritem++;
                        $aDados[] = $cdprod;
                        $aDados[] = $qtprod;
                        $aDados[] = $vlprod;
                        $aDados[] = $vltota;

                        $this->salvarItensVenda($aDados, $aNomes);
                    }

                }

                $result = $this->buscarVenda($cdvenda);

                $dtvenda = $result[0]["dtvenda"];
                $qtform = $result[0]["qtform"];

                if($qtform === "0"){

                    $qtform = 1;

                }

                if($status != "Orcamento"){
                    for ($f = 1; $f <= $qtform; $f++) {

                    $vlcont = $result[0]["vlvenda"] / $qtform;
                    $dtcont = strtotime($dtvenda . "+ {$f} months");
                    $dtcont = date("Y-m-d", $dtcont);

                    $aNomes = array();
                    $aNomes[] = "decont";
                    $aNomes[] = "dtcont";
                    $aNomes[] = "vlcont";
                    $aNomes[] = "cdtipo";
                    $aNomes[] = "cdquem";
                    $aNomes[] = "cdorig";
                    $aNomes[] = "flativ";
                    $aNomes[] = "dtcada";

                    $aDados = array();
                    $aDados[] = 'Cliente a Receber';
                    $aDados[] = $dtcont;
                    $aDados[] = $vlcont;
                    $aDados[] = 'Receber';
                    $aDados[] = $result[0]["cdclie"];
                    $aDados[] = $result[0]["cdvenda"];
                    $aDados[] = 'Sim';
                    $aDados[] = $dtcada;

                    $this->salvarConta($aNomes, $aDados);
                }
                }

                $demens = "Cadastro efetuado com sucesso!";
                $detitu = "Gerenciador Financeiro | Lançamento de Venda";
                $devolt = "venda.php";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu . '&devolt=' . $devolt);
            }
        }
    }

    //Pagina de pedidosacoes.php
    function pagPedidos()
    {
        if (!$this->verificaSessao()) {
            header('Location: ../../index.php');
            exit;
        }

        $this->verificaInatividade();

        $acao = $_REQUEST['acao'];

        if ($acao == 'novo') {
            $this->fornecedores = $this->listaFornecedores();
            $this->produtos = $this->listarProdutos();

        }

        if ($acao == 'ver' or $acao == 'edita' or $acao == 'apaga') {
            $chave = $_REQUEST['chave'];
            $this->pedido = $this->buscarPedido($chave);
            $this->itens = $this->buscaItensPedido($chave);
            $this->fornecedores = $this->listaFornecedores();
            $this->produtos = $this->listarProdutos();


        }

        if (isset($_REQUEST['editar']))
        {
            $cdpedi = $_POST["cdpedi"];

            $busca = $this->buscarPedido($cdpedi);
            $statusold = $busca[0]["status"];

            $status = $_POST["status"];

            if($statusold == "Entregue" and $status == "Entregue") {

                $itensPedido = $this->buscaItensPedido($cdpedi);

                foreach ($itensPedido as $item) {

                    $cod = intval($item['cdprod']);
                    $qtdItem = intval($item['qtprod']);

                    $qtdPecaEst = intval($this->buscaQtdProdutoEstoque($cod));

                    $qtdPecaEst -= $qtdItem;

                    $this->atualizarEstoque($cod, $qtdPecaEst);

                }
            }

            $dtcada = date('Y-m-d');
            $Flag = true;

            $aCditem = $_POST["cditem"];
            $aQtitem = $_POST["qtitem"];
            $aVlitem = $_POST["vlitem"];
            $cdforn = $_POST["cdforn"];
            $dtpedi = $_POST["dtpedi"];
            $vlpedi = $_POST["vlpedi"];
            $status = $_POST['status'];
            $vlpedi = str_replace(".", "", $vlpedi);
            $vlpedi = str_replace(",", ".", $vlpedi);

            $qtitem = 0;
            for ($f = 1; $f <= 10; $f++) {
                $primeiro = $aCditem[$f];
                $aPrimeiro = explode("|", $aCditem[$f]);
                if ($aPrimeiro[0] !== 'X') {
                    $qtitem++;
                }
            }

            if ($qtitem <= 0) {
                $demens = "É preciso informar os itens do pedido!";
                $detitu = "Gerenciador Financeiro | Cadastro de Pedidos";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                $Flag = false;
            }

            if (empty($cdforn) == true) {
                $demens = "É preciso informar o fornecedor!";
                $detitu = "Gerenciador Financeiro | Cadastro de Pedidos";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                $Flag = false;
            }

            if (empty(strtotime($dtpedi)) == true) {
                $demens = "É preciso informar a data do pedido!";
                $detitu = "Gerenciador Financeiro | Cadastro de Pedidos";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                $Flag = false;
            }

            $this->excluirPedido($cdpedi);
            $this->excluirItensPedido($cdpedi);
            $this->excluirContaPedido($cdpedi);

            //Implementando controle de estoque
            if($status == "Entregue") {
                for ($f = 1; $f <= 20; $f++) {

                    $aPrimeiro = explode("|", $aCditem[$f]);

                    if ($aPrimeiro[0] !== 'X') {
                        if ($aPrimeiro[0] == 'P') {

                            $cdprod = $aPrimeiro[2];
                            $qtprod = intval($aQtitem[$f]);

                            $qtdProdEst = intval($this->buscaQtdProdutoEstoque($cdprod));

                            $qtdProdEst += $qtprod;
                            $qtd = $qtdProdEst;

                            $this->atualizarEstoque($cdprod, $qtd);
                        }
                    }
                }
            }

            if($status == "Pendente" and $statusold != "Pendente") {
                for ($f = 1; $f <= 20; $f++) {

                    $aPrimeiro = explode("|", $aCditem[$f]);

                    if ($aPrimeiro[0] !== 'X') {
                        if ($aPrimeiro[0] == 'P') {

                            $cdprod = $aPrimeiro[2];
                            $qtprod = intval($aQtitem[$f]);

                            $qtdProdEst = intval($this->buscaQtdProdutoEstoque($cdprod));

                            $qtdProdEst -= $qtprod;
                            $qtd = $qtdProdEst;

                            $this->atualizarEstoque($cdprod, $qtd);
                        }
                    }
                }
            }

            if ($Flag == true) {

                //campos da tabela
                $aNomes = array();
                $aNomes[] = "cdpedi";
                $aNomes[] = "cdforn";
                $aNomes[] = "dtpedi";
                $aNomes[] = "vlpedi";
                $aNomes[] = "decont";
                $aNomes[] = "dtentr";
                $aNomes[] = "status";
                $aNomes[] = "deobse";
                $aNomes[] = "flativ";
                $aNomes[] = "dtcada";
                $aNomes[] = "cdform";
                $aNomes[] = "qtform";


                //dados da tabela
                $aDados = array();
                $aDados[] = $_POST["cdpedi"];
                $aDados[] = $_POST["cdforn"];
                $aDados[] = $_POST["dtpedi"];
                $aDados[] = $vlpedi;
                $aDados[] = $_POST["decont"];
                $aDados[] = $_POST["dtentr"];
                $aDados[] = $_POST["status"];
                $aDados[] = $_POST["deobse"];
                $aDados[] = 'Sim';
                $aDados[] = $dtcada;
                $aDados[] = $_POST["cdform"];
                $aDados[] = $_POST["qtform"];

                $proc = "Alteração";
                $chave = $_POST["cdpedi"];

                $this->salvarPedido($aNomes, $aDados, $proc, $chave);

                $nritem = 1;
                for ($f = 1; $f <= 10; $f++) {
                    $primeiro = $aCditem[$f];
                    $aPrimeiro = explode("|", $aCditem[$f]);
                    if ($aPrimeiro[0] !== 'X') {
                        $cdpeca = $aPrimeiro[2];
                        $qtprod = $aQtitem[$f];
                        $vlpeca = $aVlitem[$f];

                        $vltota = $qtprod * $vlpeca;

                        $aNomes = array();
                        $aNomes[] = "cdpedi";
                        $aNomes[] = "nritem";
                        $aNomes[] = "cdprod";
                        $aNomes[] = "qtprod";
                        $aNomes[] = "vlprod";
                        $aNomes[] = "vltota";

                        $aDados = array();
                        $aDados[] = $cdpedi;
                        $aDados[] = $nritem++;
                        $aDados[] = $cdpeca;
                        $aDados[] = $qtprod;
                        $aDados[] = $vlpeca;
                        $aDados[] = $vltota;

                        $this->salvarItensPedido($aNomes, $aDados);

                    }
                }

                $pedidos = $this->buscarPedido($cdpedi);
                $dtpedi = $pedidos[0]["dtpedi"];
                $qtform = $pedidos[0]["qtform"];

                if($qtform === "0"){

                    $qtform = 1;

                }

                for ($f = 1; $f <= $qtform; $f++) {
                    $vlcont = $pedidos[0]["vlpedi"] / $qtform;

                    $dtcont = strtotime($dtpedi . "+ {$f} months");
                    $dtcont = date("Y-m-d", $dtcont);

                    $aNomes = array();
                    $aNomes[] = "decont";
                    $aNomes[] = "dtcont";
                    $aNomes[] = "vlcont";
                    $aNomes[] = "cdtipo";
                    $aNomes[] = "cdquem";
                    $aNomes[] = "cdorig";
                    $aNomes[] = "flativ";
                    $aNomes[] = "dtcada";

                    $aDados = array();
                    $aDados[] = 'Pedido a Pagar';
                    $aDados[] = $dtcont;
                    $aDados[] = $vlcont;
                    $aDados[] = 'Pagar';
                    $aDados[] = $pedidos[0]["cdforn"];
                    $aDados[] = $pedidos[0]["cdpedi"];
                    $aDados[] = 'Sim';
                    $aDados[] = $dtcada;

                    $this->salvarConta($aNomes, $aDados);

                }

                $demens = "Alteração efetuada com sucesso!";
                $detitu = "Gerenciador Financeiro | Cadastro de Pedidos";
                $devolt = "pedidos.php";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu . '&devolt=' . $devolt);
            }

        }

        if (isset($_REQUEST['apagar'])) {

            $cdpedi = $_POST["cdpedi"];

            $itensPedido = $this->buscaItensPedido($cdpedi);

            foreach($itensPedido as $item)
            {
                $cod = intval($item['cdprod']);
                $qtdItem = intval($item['qtprod']);

                $qtdProdEst = intval($this->buscaQtdProdutoEstoque($cod));

                $qtdProdEst -= $qtdItem;

                $this->atualizarEstoque($cod, $qtdProdEst);

            }

            if ($this->excluirPedido($cdpedi) and $this->excluirItensPedido($cdpedi) and $this->excluirContaPedido($cdpedi)) {
                $demens = "Exclusão efetuada com sucesso!";

            } else {

                $demens = "Ocorreu um problema durante exclusão do pedido. Se persistir contate o Suporte!";
            }

            $detitu = "Gerenciador FInanceiro | Cadastro de Pedidos";
            $devolt = "pedidos.php";
            header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu . '&devolt=' . $devolt);

        }

        if (isset($_REQUEST['salvar'])) {

            $dtcada = date('Y-m-d');
            $Flag = true;

            $aCditem = $_POST["cditem"];
            $aQtitem = $_POST["qtitem"];
            $aVlitem = $_POST["vlitem"];
            $cdforn = $_POST["cdforn"];
            $dtpedi = $_POST["dtpedi"];
            $vlpedi = $_POST["vlpedi"];
            $status = $_POST['status'];
            $vlpedi = str_replace(".", "", $vlpedi);
            $vlpedi = str_replace(",", ".", $vlpedi);

            $qtitem = 0;
            for ($f = 1; $f <= 10; $f++) {
                $primeiro = $aCditem[$f];
                $aPrimeiro = explode("|", $aCditem[$f]);
                if ($aPrimeiro[0] !== 'X') {
                    $qtitem++;
                }
            }

            if ($qtitem <= 0) {
                $demens = "É preciso informar os itens do pedido!";
                $detitu = "Gerenciador Financeiro | Cadastro de Pedidos";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                $Flag = false;
            }

            if (empty($cdforn) == true) {
                $demens = "É preciso informar o fornecedor!";
                $detitu = "Gerenciador Financeiro | Cadastro de Pedidos";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                $Flag = false;
            }

            if (empty(strtotime($dtpedi)) == true) {
                $demens = "É preciso informar a data do pedido!";
                $detitu = "Gerenciador Financeiro | Cadastro de Pedidos";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                $Flag = false;
            }

            //Implementando controle de estoque
            if($status == "Entregue") {
                for ($f = 1; $f <= 20; $f++) {

                    $aPrimeiro = explode("|", $aCditem[$f]);

                    if ($aPrimeiro[0] !== 'X') {
                        if ($aPrimeiro[0] == 'P') {

                            $cdprod = $aPrimeiro[2];
                            $qtprod = intval($aQtitem[$f]);

                            $qtdProdEst = intval($this->buscaQtdProdutoEstoque($cdprod));

                            $qtdProdEst += $qtprod;

                            $this->atualizarEstoque($cdprod, $qtdProdEst);
                        }
                    }
                }
            }

            if ($Flag == true) {

                //campos da tabela
                $aNomes = array();
                $aNomes[] = "cdforn";
                $aNomes[] = "dtpedi";
                $aNomes[] = "vlpedi";
                $aNomes[] = "decont";
                $aNomes[] = "dtentr";
                $aNomes[] = "deobse";
                $aNomes[] = "status";
                $aNomes[] = "flativ";
                $aNomes[] = "dtcada";
                $aNomes[] = "cdform";
                $aNomes[] = "qtform";


                //dados da tabela
                $aDados = array();
                $aDados[] = $_POST["cdforn"];
                $aDados[] = $_POST["dtpedi"];
                $aDados[] = $vlpedi;
                $aDados[] = $_POST["decont"];
                $aDados[] = $_POST["dtentr"];
                $aDados[] = $_POST["deobse"];
                $aDados[] = $_POST["status"];
                $aDados[] = 'Sim';
                $aDados[] = $dtcada;
                $aDados[] = $_POST["cdform"];
                $aDados[] = $_POST["qtform"];

                $proc = "Inclusão";
                $chave = "";

                $this->salvarPedido($aNomes, $aDados, $proc, $chave);

                $pedido = $this->buscarMaiorPedidoFornecedor($cdforn,$dtpedi);
                $cdpedi = $pedido[0]["cdpedi"];

                $nritem = 1;
                for ($f = 1; $f <= 10; $f++) {

                    $primeiro = $aCditem[$f];
                    $aPrimeiro = explode("|", $aCditem[$f]);

                    if ($aPrimeiro[0] !== 'X') {
                        $cdprod = $aPrimeiro[2];
                        $qtprod = $aQtitem[$f];
                        $vlprod = $aVlitem[$f];

                        $vltota = $qtprod * $vlprod;

                        $aNomes = array();
                        $aNomes[] = "cdpedi";
                        $aNomes[] = "nritem";
                        $aNomes[] = "cdprod";
                        $aNomes[] = "qtprod";
                        $aNomes[] = "vlprod";
                        $aNomes[] = "vltota";

                        $aDados = array();
                        $aDados[] = $cdpedi;
                        $aDados[] = $nritem++;
                        $aDados[] = $cdprod;
                        $aDados[] = $qtprod;
                        $aDados[] = $vlprod;
                        $aDados[] = $vltota;

                        $this->salvarItensPedido($aNomes, $aDados);

                    }
                }

                $result = $this->buscarPedido($cdpedi);

                $dtpedi = $result[0]["dtpedi"];
                $qtform = $result[0]["qtform"];

                if($qtform === "0"){

                    $qtform = 1;

                }

                for ($f = 1; $f <= $qtform; $f++) {

                    $vlcont = $result[0]["vlpedi"] / $qtform;

                    $dtcont = strtotime($dtpedi . "+ {$f} months");
                    $dtcont = date("Y-m-d", $dtcont);

                    $aNomes = array();
                    $aNomes[] = "decont";
                    $aNomes[] = "dtcont";
                    $aNomes[] = "vlcont";
                    $aNomes[] = "cdtipo";
                    $aNomes[] = "cdquem";
                    $aNomes[] = "cdorig";
                    $aNomes[] = "flativ";
                    $aNomes[] = "dtcada";

                    $aDados = array();
                    $aDados[] = 'Pedido a Pagar';
                    $aDados[] = $dtcont;
                    $aDados[] = $vlcont;
                    $aDados[] = 'Pagar';
                    $aDados[] = $result[0]["cdforn"];
                    $aDados[] = $result[0]["cdpedi"];
                    $aDados[] = 'Sim';
                    $aDados[] = $dtcada;

                    $this->salvarConta($aNomes, $aDados);

                }

                $demens = "Cadastro efetuado com sucesso!";
                $detitu = "Gerenciador Financeiro | Cadastro de Pedidos";
                $devolt = "pedidos.php";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu . '&devolt=' . $devolt);
        }

        }
    }

    //Pagina fornecedoresacoes.php
    function pagFornecedores()
    {
        if (!$this->verificaSessao()) {
            header('Location: ../../index.php');
            exit;
        }

        $this->verificaInatividade();

        $data = date('Y-m-d');
        $acao = $_REQUEST['acao'];

        $flag = true;
        $flag2 = false;
        $this->estados = $this->listarEstadosBra();

        if ($flag == true) {

            if ($acao == 'ver' or $acao == 'edita' or $acao == 'apaga') {
                $chave = $_REQUEST['chave'];
                $this->fornecedor = $this->buscaFornecedor($chave);
            }

            if (isset($_REQUEST['editar'])) {

                $cdforn = $_POST["cdforn"];

                if (strlen($cdforn) < 12) {
                    $cdforn = $this->util->RetirarMascara($cdforn, "cpf");
                    if ($this->util->validaCPF($cdforn) == false) {
                        $demens = "Cpf inválido!";
                        $detitu = "Gerenciador Financeiro | Cadastro de Fornecedores";
                        header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                        $flag = false;
                    }
                } Else {
                    $cdforn = $this->util->RetirarMascara($cdforn, "cnpj");
                    if ($this->util->validaCNPJ($cdforn) == false) {
                        $demens = "Cnpj inválido!";
                        $detitu = "Gerenciador Financeiro | Cadastro de Clientes";
                        header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                        $flag = false;
                    }
                }

                if ($flag2 == true) {
                } Else {

                    //campos da tabela
                    $aNomes = array();
                    $aNomes[] = "cdforn";
                    $aNomes[] = "deforn";
                    $aNomes[] = "cdtipo";
                    $aNomes[] = "nrinsc";
                    $aNomes[] = "nrccm";
                    $aNomes[] = "nrrg";
                    $aNomes[] = "deende";
                    $aNomes[] = "nrende";
                    $aNomes[] = "decomp";
                    $aNomes[] = "debair";
                    $aNomes[] = "decida";
                    $aNomes[] = "cdesta";
                    $aNomes[] = "nrcepi";
                    $aNomes[] = "nrtele";
                    $aNomes[] = "nrcelu";
                    $aNomes[] = "demail";
                    $aNomes[] = "deobse";
                    $aNomes[] = "flativ";
                    $aNomes[] = "dtcada";

                    //dados da tabela
                    $aDados = array();
                    $aDados[] = $_POST["cdforn"];
                    $aDados[] = $_POST["deforn"];
                    $aDados[] = $_POST["cdtipo"];
                    $aDados[] = $_POST["nrinsc"];
                    $aDados[] = $_POST["nrccm"];
                    $aDados[] = $_POST["nrrg"];
                    $aDados[] = $_POST["deende"];
                    $aDados[] = $_POST["nrende"];
                    $aDados[] = $_POST["decomp"];
                    $aDados[] = $_POST["debair"];
                    $aDados[] = $_POST["decida"];
                    $aDados[] = $_POST["cdesta"];
                    $aDados[] = $_POST["nrcepi"];
                    $aDados[] = $_POST["nrtele"];
                    $aDados[] = $_POST["nrcelu"];
                    $aDados[] = $_POST["demail"];
                    $aDados[] = $_POST["deobse"];
                    $aDados[] = "S";
                    $aDados[] = $data;

                    if ($this->atualizarFornecedor($aNomes, $aDados, $chave)) {
                        if ($flag2 == false) {
                            $demens = "Atualização efetuada com sucesso!";
                        }
                    } else {
                        $demens = "Ocorreu um problema na atualização/exclusão. Se persistir contate o Suporte!";
                    }
                }
                $detitu = "Gerenciador Financeiro | Cadastro de fornecedores";
                $devolt = "fornecedores.php";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu . '&devolt=' . $devolt);
            }

            if (isset($_REQUEST['apagar'])) {
                $cdforn = $_POST["cdforn"];

                if ($this->excluirFornecedor($cdforn)) {

                    if ($flag2 == false) {
                        $demens = "Exclusão efetuada com sucesso!";

                    }
                } else {
                    $demens = "Ocorreu um problema na atualização/exclusão. Se persistir contate o Suporte!";
                }

                $detitu = "Gerenciador Financeiro | Cadastro de fornecedores";
                $devolt = "fornecedores.php";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu . '&devolt=' . $devolt);
            }

            if (isset($_REQUEST['salvar'])) {

                $data = date('Y-m-d');
                $cdforn = $_POST["cdforn"];
                $demail = $_POST["demail"];
                $Flag = true;

                if (strlen($cdforn) < 12) {

                    $cdforn = $this->util->RetirarMascara($cdforn, "cpf");

                    if ($this->util->validaCPF($cdforn) == false) {
                        $demens = "Cpf inválido!";
                        $detitu = "Gerenciador Financeiro | Cadastro de fornecedores";
                        header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                        $Flag = false;
                    }
                } Else {

                    $cdforn = $this->util->RetirarMascara($cdforn, "cnpj");

                    if ($this->util->validaCNPJ($cdforn) == false) {
                        $demens = "Cnpj inválido!";
                        $detitu = "Gerenciador Financeiro | Cadastro de fornecedores";
                        header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                        $Flag = false;
                    }
                }

                $forn = $this->buscaFornecedor($cdforn);

                if ($forn) {
                    $demens = "Cpf/Cnpj já cadastrado!";
                    $detitu = "Gerenciador Financeiro | Cadastro de fornecedores";
                    header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu);
                    $Flag = false;
                }

                if ($Flag == true) {

                    //campos da tabela
                    $aNomes = array();
                    $aNomes[] = "cdforn";
                    $aNomes[] = "deforn";
                    $aNomes[] = "cdtipo";
                    $aNomes[] = "nrinsc";
                    $aNomes[] = "nrccm";
                    $aNomes[] = "nrrg";
                    $aNomes[] = "deende";
                    $aNomes[] = "nrende";
                    $aNomes[] = "decomp";
                    $aNomes[] = "debair";
                    $aNomes[] = "decida";
                    $aNomes[] = "cdesta";
                    $aNomes[] = "nrcepi";
                    $aNomes[] = "nrtele";
                    $aNomes[] = "nrcelu";
                    $aNomes[] = "demail";
                    $aNomes[] = "deobse";
                    $aNomes[] = "flativ";
                    $aNomes[] = "dtcada";

                    //dados da tabela
                    $aDados = array();
                    $aDados[] = $_POST["cdforn"];
                    $aDados[] = $_POST["deforn"];
                    $aDados[] = $_POST["cdtipo"];
                    $aDados[] = $_POST["nrinsc"];
                    $aDados[] = $_POST["nrccm"];
                    $aDados[] = $_POST["nrrg"];
                    $aDados[] = $_POST["deende"];
                    $aDados[] = $_POST["nrende"];
                    $aDados[] = $_POST["decomp"];
                    $aDados[] = $_POST["debair"];
                    $aDados[] = $_POST["decida"];
                    $aDados[] = $_POST["cdesta"];
                    $aDados[] = $_POST["nrcepi"];
                    $aDados[] = $_POST["nrtele"];
                    $aDados[] = $_POST["nrcelu"];
                    $aDados[] = $_POST["demail"];
                    $aDados[] = $_POST["deobse"];
                    $aDados[] = "S";
                    $aDados[] = $data;

                    if ($this->salvarFornecedor($aNomes, $aDados)) {

                        $demens = "Cadastro efetuado com sucesso!";

                    } else {
                        $demens = "Ocorreu um problema durante o cadastro. Se persistir contate o suporte!";
                    }
                }

                $detitu = "Gerenciador Financeiro | Cadastro de fornecedores";
                $devolt = "fornecedores.php";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu . '&devolt=' . $devolt);

            }
        }
    }

    //Pagina Usuarioscoes.php
    function pagUsuarios()
    {
        if (!$this->verificaSessao()) {
            header('Location: ../../index.php');
            exit;
        }

        $this->verificaInatividade();

        $acao = $_REQUEST['acao'];

        if ($acao == 'ver' or $acao == 'edita' or $acao == 'apaga') {

            $chave = $_REQUEST['chave'];
            $this->usuario = $this->buscarUsuario($chave);

        }

        if (isset($_REQUEST['editar'])) {

            $cdusua = $_POST["cdusua"];
            $defoto1 = $_POST["defoto1"];
            $desenha = $_POST['password'];

            //uploads
            $uploaddir = '../../templates/img/' . $cdusua . "_";
            $uploadfile1 = $uploaddir . basename($_FILES['defotom']['name']);

            #Move o arquivo para o diretório de destino
            move_uploaded_file($_FILES["defotom"]["tmp_name"], $uploadfile1);

            $defotom = basename($_FILES['defotom']['name']);

            if (empty($defotom) == true) {
                $defoto = $defoto1;
            } Else {
                $defoto = $uploadfile1;
            }

            //campos da tabela
            $aNomes = array();
            $aNomes[] = "deusua";
            $aNomes[] = "demail";
            $aNomes[] = "defoto";
            $aNomes[] = "cdtipo";
            $aNomes[] = "flativ";
            $aNomes[] = "nrtele";

            //dados da tabela
            $aDados = array();
            $aDados[] = $_POST["deusua"];
            $aDados[] = $_POST["demail"];
            $aDados[] = $defoto;
            $aDados[] = $_POST["cdtipo"];
            $aDados[] = $_POST["flativ"];
            $aDados[] = $_POST["nrtele"];

            if (!empty($desenha)) {
                $aNomes[] = "desenh";
                $aDados[] = md5($desenha);
            }

            if ($this->atualizaDadosUsuario($aNomes, $aDados, $cdusua)) {

                $demens = "Atualização efetuada com sucesso!";

            } else {

                $demens = "Ocorreu um problema na atualização/exclusão. Se persistir contate o Suporte!";
            }

            $detitu = "Gerenciador Financeiro | Cadastro de Usuários";
            $devolt = "usuarios.php";
            header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu . '&devolt=' . $devolt);
        }

        if (isset($_REQUEST['apagar']))
        {
            $cdusua = $_POST["cdusua"];

            if ($this->excluirUsuario($cdusua)) {
                $demens = "Exclusão efetuada com sucesso!";

            } else {

                $demens = "Ocorreu um problema na atualização/exclusão. Se persistir contate o suporte!";
            }

            $detitu = "Gerenciador Financeiro | Cadastro de Usuários";
            $devolt = "usuarios.php";
            header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu . '&devolt=' . $devolt);

        }

        if (isset($_REQUEST['salvar']))
        {
            $delogin = $_POST["login"];
            $demail = $_POST["demail"];
            $dtcada = date('Y-m-d');
            $flativ = "S";
            $Flag = true;

            if ($Flag == true) {

                //uploads
                $uploaddir = '../../templates/img/' . $delogin . "_";
                $uploadfile1 = $uploaddir . basename($_FILES['defotom']['name']);

                #Move o arquivo para o diretório de destino
                move_uploaded_file($_FILES["defotom"]["tmp_name"], $uploadfile1);

                $defoto1 = basename($_FILES['defotom']['name']);

                $desenh = md5($_POST["password"]);

                if (empty($defoto1) == true) {
                    $defoto = "img/semfoto.jpg";
                } Else {
                    $defoto = $uploadfile1;
                }

                //campos da tabela
                $aNomes = array();
                $aNomes[] = "deusua";
                $aNomes[] = "delogin";
                $aNomes[] = "desenh";
                $aNomes[] = "demail";
                $aNomes[] = "defoto";
                $aNomes[] = "cdtipo";
                $aNomes[] = "flativ";
                $aNomes[] = "dtcada";
                $aNomes[] = "nrtele";

                //dados da tabela
                $aDados = array();
                $aDados[] = $_POST["deusua"];
                $aDados[] = $delogin;
                $aDados[] = $desenh;
                $aDados[] = $demail;
                $aDados[] = $defoto;
                $aDados[] = $_POST["cdtipo"];
                $aDados[] = $flativ;
                $aDados[] = $dtcada;
                $aDados[] = $_POST["nrtele"];

                if ($this->salvarUsuario($aNomes, $aDados)) {

                    $demens = "Cadastro efetuado com sucesso!";

                } else {

                    $demens = "Ocorreu um problema durante o cadastro. Se persistir contate o suporte!";
                }

                $detitu = "Gerenciador Financeiro | Cadastro de usuarios";
                $devolt = "usuarios.php";
                header('Location: mensagem.php?demens=' . $demens . '&detitu=' . $detitu . '&devolt=' . $devolt);
            }
        }
    }

    //Pagina produtosacoes.php
    function pagProdutos()
    {
        if (!$this->verificaSessao()) {
            header('Location: ../../index.php');
            exit;
        }

        $this->verificaInatividade();

        $data = date('Y-m-d');
        $acao = $_REQUEST['acao'];

        $flag = true;
        $flag2 = false;

        if ($flag == true) {

            if ($acao == 'ver' or $acao == 'edita' or $acao == 'apaga') {

                $chave = $_REQUEST['chave'];
                $this->produto = $this->buscarProduto($chave);

            }

            if(isset($_REQUEST['editar']))
            {
                $data = date('Y-m-d');
                $cdprod = $_POST["cdpeca"];
                $deprod = $_POST["depeca"];
                $qtprod = $_POST["qtpeca"];
                $vlprod = $_POST["vlpeca"];

                $vlprod= str_replace(".","",$vlprod);
                $vlprod= str_replace(",",".",$vlprod);

                //campos da tabela
                $aNomes=array();

                $aNomes[]= "deprod";
                $aNomes[]= "qtprod";
                $aNomes[]= "vlprod";

                //dados da tabela
                $aDados=array();

                $aDados[]= $deprod;
                $aDados[]= $qtprod;
                $aDados[]= $vlprod;

                if($this->atualizaProduto($aNomes, $aDados, $cdprod)){

                    $demens = "Atualização efetuada com sucesso!";

                }else{

                    $demens = "Ocorreu um problema na atualização. Se persistir contate o suporte!";

                }

                $detitu = "Gerenciador Financeiro | Cadastro de Produtos";
                $devolt = "produtos.php";
                header('Location: mensagem.php?demens='.$demens.'&detitu='.$detitu.'&devolt='.$devolt);
            }

            if(isset($_REQUEST['apagar']))
            {
                $chave = $_REQUEST['chave'];

                if($this->excluirProduto($chave)){

                    $demens = "Exclusão efetuada com sucesso!";

                }else{

                    $demens = "Ocorreu um problema na exclusão. Se persistir contate o suporte!";

                }

                $detitu = "Gerenciador Financeiro | Cadastro de Produtos";
                $devolt = "produtos.php";
                header('Location: mensagem.php?demens='.$demens.'&detitu='.$detitu.'&devolt='.$devolt);

            }

            if(isset($_REQUEST['salvar']))
            {
                $data = date('Y-m-d');
                $cdprod = $_POST["cdpeca"];
                $deprod = $_POST["depeca"];
                $qtprod = $_POST["qtpeca"];
                $vlprod = $_POST["vlpeca"];

                $vlprod= str_replace(".","",$vlprod);
                $vlprod= str_replace(",",".",$vlprod);

                $Flag = true;

                $prod = $this->buscarProduto($cdprod);

                if ($prod) {
                    $demens = "Código de Produto já cadastrado!";
                    $detitu = "Gerenciador Financeiro | Cadastro de Produtos";
                    header('Location: mensagem.php?demens='.$demens.'&detitu='.$detitu);
                    $Flag=false;
                }

                if ($Flag == true) {

                    //campos da tabela
                    $aNomes=array();

                    $aNomes[]= "cdprod";
                    $aNomes[]= "deprod";
                    $aNomes[]= "qtprod";
                    $aNomes[]= "vlprod";

                    //dados da tabela
                    $aDados=array();
                    $aDados[]= $cdprod;
                    $aDados[]= $deprod;
                    $aDados[]= $qtprod;
                    $aDados[]= $vlprod;

                    if($this->salvarProduto($aNomes, $aDados))
                    {
                        $demens = "Cadastro efetuado com sucesso!";

                    }else{

                        $demens = "Ocorreu um problema durante o cadastro. Se persistir contate o suporte!";
                    }

                }

                $detitu = "Gerenciador Financeiro | Cadastro de Produtos";
                $devolt = "produtos.php";
                header('Location: mensagem.php?demens='.$demens.'&detitu='.$detitu.'&devolt='.$devolt);
            }
        }
    }

    //Pagina contasacoes.php
    function pagContas()
    {
        if (!$this->verificaSessao()) {
            header('Location: ../../index.php');
            exit;
        }

        $this->verificaInatividade();

        $data = date('Y-m-d');
        $acao = $_REQUEST['acao'];

        $flag = true;
        $flag2 = false;

        if ($flag == true) {

            if ($acao == 'ver' or $acao == 'edita' or $acao == 'apaga') {

                $chave = $_REQUEST['chave'];
                $this->conta = $this->buscaConta($chave);

            }
        }

        if(isset($_REQUEST['editar']))
        {
            $data = date('Y-m-d');
            $cdcont = $_POST["cdcont"];
            $vlcont = $_POST["vlcont"];
            $vlpago = $_POST["vlpago"];

            $vlcont= str_replace(".","",$vlcont);
            $vlcont= str_replace(",",".",$vlcont);
            $vlpago= str_replace(".","",$vlpago);
            $vlpago= str_replace(",",".",$vlpago);

            //campos da tabela
            $aNomes=array();

            $aNomes[]= "decont";
            $aNomes[]= "dtcont";
            $aNomes[]= "vlcont";
            $aNomes[]= "cdtipo";
            $aNomes[]= "vlpago";
            $aNomes[]= "dtpago";
            $aNomes[]= "cdquem";
            $aNomes[]= "cdorig";
            $aNomes[]= "deobse";
            $aNomes[]= "flativ";
            $aNomes[]= "dtcada";

            //dados da tabela
            $aDados=array();
            $aDados[]= $_POST["decont"];
            $aDados[]= $_POST["dtcont"];
            $aDados[]= $vlcont;
            $aDados[]= $_POST["cdtipo"];
            $aDados[]= $vlpago;
            $aDados[]= $_POST["dtpago"];
            $aDados[]= $_POST["cdquem"];
            $aDados[]= $_POST["cdorig"];
            $aDados[]= $_POST["deobse"];
            $aDados[]= "S";
            $aDados[]= date("Y-m-d");

            if($this->atualizaConta($aNomes, $aDados, $cdcont ))
            {
                $demens = "Atualização efetuada com sucesso!";

            }else{

                $demens = "Ocorreu um problema na atualização/exclusão. Se persistir contate o suporte!";

            }

            $detitu = "Gerenciador Financeiro | Cadastro de Contas a Pagar/Receber";
            $devolt = "contas.php";
            header('Location: mensagem.php?demens='.$demens.'&detitu='.$detitu.'&devolt='.$devolt);
        }

        if(isset($_REQUEST['apagar']))
        {
            $cdcont = $_POST["cdcont"];

            if($this->excluirConta($cdcont))
            {
                $demens = "Exclusão efetuada com sucesso!";

            }else{

                $demens = "1Ocorreu um problema na atualização/exclusão. Se persistir contate o suporte!";
            }

            $detitu = "Gerenciador Financeiros; | Cadastro de Contas a Pagar/Receber";
            $devolt = "contas.php";
            header('Location: mensagem.php?demens='.$demens.'&detitu='.$detitu.'&devolt='.$devolt);

        }

        if(isset($_REQUEST['salvar']))
        {
            $data = date('Y-m-d');
            $vlcont = $_POST["vlcont"];
            $vlpago = $_POST["vlpago"];

            $vlcont= str_replace(".","",$vlcont);
            $vlcont= str_replace(",",".",$vlcont);
            $vlpago= str_replace(".","",$vlpago);
            $vlpago= str_replace(",",".",$vlpago);

            //campos da tabela
            $aNomes=array();
            $aNomes[]= "decont";
            $aNomes[]= "dtcont";
            $aNomes[]= "vlcont";
            $aNomes[]= "cdtipo";
            $aNomes[]= "vlpago";
            $aNomes[]= "dtpago";
            $aNomes[]= "cdquem";
            $aNomes[]= "cdorig";
            $aNomes[]= "deobse";
            $aNomes[]= "flativ";
            $aNomes[]= "dtcada";

            //dados da tabela
            $aDados=array();
            $aDados[]= $_POST["decont"];
            $aDados[]= $_POST["dtcont"];
            $aDados[]= $vlcont;
            $aDados[]= $_POST["cdtipo"];
            $aDados[]= $vlpago;
            $aDados[]= $_POST["dtpago"];
            $aDados[]= $_POST["cdquem"];
            $aDados[]= $_POST["cdorig"];
            $aDados[]= $_POST["deobse"];
            $aDados[]= "S";
            $aDados[]= $data;

            if($this->salvarConta($aNomes, $aDados))
            {
                $demens = "Cadastro efetuado com sucesso!";

            }else{

                $demens = "Ocorreu um problema na atualização/exclusão. Se persistir contate o suporte!";
            }

            $detitu = "Gerenciador Financeiro | Cadastro de Contas a Pagar/Receber";
            $devolt = "contas.php";
            header('Location: mensagem.php?demens='.$demens.'&detitu='.$detitu.'&devolt='.$devolt);

        }
    }

    //Pagina paramentro.php
    function pagParamentros()
    {
        if (!$this->verificaSessao()) {
            header('Location: ../../index.php');
            exit;
        }

        $this->verificaInatividade();

        if(isset($_REQUEST['editar']))
        {
            $cod = $_POST["cdprop"];

            //campos da tabela
            $aNomes=array();
            $aNomes[]= "cdprop";
            $aNomes[]= "deprop";
            $aNomes[]= "nrinsc";
            $aNomes[]= "nrccm";
            $aNomes[]= "deende";
            $aNomes[]= "nrende";
            $aNomes[]= "decomp";
            $aNomes[]= "debair";
            $aNomes[]= "decida";
            $aNomes[]= "nrcepi";
            $aNomes[]= "cdesta";
            $aNomes[]= "nrtele";
            $aNomes[]= "nrcelu";
            $aNomes[]= "demail";

            //dados da tabela
            $aDados=array();
            $aDados[]= $_POST["cdprop"];
            $aDados[]= $_POST["deprop"];
            $aDados[]= $_POST["nrinsc"];
            $aDados[]= $_POST["nrccm"];
            $aDados[]= $_POST["deende"];
            $aDados[]= $_POST["nrende"];
            $aDados[]= $_POST["decomp"];
            $aDados[]= $_POST["debair"];
            $aDados[]= $_POST["decida"];
            $aDados[]= $_POST["nrcepi"];
            $aDados[]= $_POST["cdesta"];
            $aDados[]= $_POST["nrtele"];
            $aDados[]= $_POST["nrcelu"];
            $aDados[]= $_POST["demail"];

            if($this->atualizaInfoEmp($aDados, $aNomes, $cod))
            {
                $demens = "Parâmetros atualizados com sucesso!";

            }else{

                $demens = "Ocorreu um problema na atualização. Se persistir contate o suporte!";
            }

            $detitu = "Gerenciador Financeiro | Parâmetros do Sistema";
            $devolt = "home.php";
            header('Location: mensagem.php?demens='.$demens.'&detitu='.$detitu.'&devolt='.$devolt);
        }


    }

}