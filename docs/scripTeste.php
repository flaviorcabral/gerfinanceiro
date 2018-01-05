<?php
/**
 * Created by PhpStorm.
 * User: flavio.pereira
 * Date: 25/08/2017
 * Time: 11:07
 */

include '../config.php';

//session_start();

$con = new Controller();
$usu = new Conta();

$result = $con->buscarItensVenda(33);
$result1 = $con->buscaQtdProdutoEstoque(1);
$con->atualizarEstoque(1,5);
$result2 = $con->buscaQtdProdutoEstoque(1);

foreach ($result as $item){
    $qtd = $item['qtpeca'];
}

echo $qtd;
$result1 += $qtd;
//$result = $usu->listaUsuarios();
echo "<pre>";
var_dump($result);
var_dump($result1);
var_dump($result2);
echo "<pre/>";

