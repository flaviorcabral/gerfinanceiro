<?php

    include_once '../../config.php';

    ini_set ('display_errors', 1 );
    error_reporting ( E_ALL | E_STRICT );
    //error_reporting (0);

    // identificando dispositivo
    $iphone = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
    $ipad = strpos($_SERVER['HTTP_USER_AGENT'],"iPad");
    $android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");
    $palmpre = strpos($_SERVER['HTTP_USER_AGENT'],"webOS");
    $berry = strpos($_SERVER['HTTP_USER_AGENT'],"BlackBerry");
    $ipod = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");
    $symbian =  strpos($_SERVER['HTTP_USER_AGENT'],"Symbian");

    $eMovel="N";
    if ($iphone || $ipad || $android || $palmpre || $ipod || $berry || $symbian == true) {
        $eMovel="S";
    }

    $con = new Controller();
    $util = new Util();

    $chave = trim($_GET["chave"]);

    $aItem = $con->buscaItensPedido($chave);

    $aPedi = $con->buscarPedidoCIndice($chave);
    $pos = strpos($aPedi[0]["cdforn"], "-");
    $cdforn = substr($aPedi[0]["cdforn"], 0, $pos-1);

    $aForn = $con->buscaFornecedor($cdforn);
    $deForn = $aForn["deforn"];

    $dtpedi = $aPedi[0]["dtpedi"];
    $dtpedi = strtotime($dtpedi);
    $dtpedi = date("d-m-Y", $dtpedi);

    $dtentre = $aPedi[0]["dtentr"];
    $dtentre = strtotime($dtentre);
    $dtentre = date("d-m-Y", $dtentre);

    /*if (strtotime("1969-12-31") == strtotime($dtpago)){
        $dtpago="  ABERTA  ";
    }

    if (strtotime("0000-00-00") == "") {
        $dtpago="  ABERTA  ";
    }

    if (strtotime("") == "") {
        $dtpago="  ABERTA  ";
    }*/

    $aPara = $con->infoEmpresa();
    $cdprop=$aPara[0]["cdprop"];

    /*if (strlen($cdclie) > 11 ) {
        $cdclie=$util->formata($cdclie,"cnpj");
    } Else {
        $cdclie=$util->formata($cdclie,"cpf");
    }*/

    if (strlen($cdprop) > 11 ) {
        $cdprop=$util->formata($cdprop,"cnpj");
    } Else {
        $cdprop=$util->formata($cdprop,"cpf");
    }

?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>&nbsp;&nbsp;&nbsp;&nbsp;</title>

    <link href="../../templates/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../templates/font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="../../templates/css/animate.css" rel="stylesheet">
    <link href="../../templates/css/style.css" rel="stylesheet">

</head>

<body class="white-bg">
    <center><img src="../../templates/img/logomarca.jpg" alt="aliança logo" width="160px" heigth="160px"></center>
    <center><strong><?php echo $aPara[0]["deprop"]; ?></strong></center>
    <center><h2 class="text-navy"><?php echo 'Ordem de Pedido No. '.$aPedi[0]["cdpedi"]; ?></h2></center>

    <!--div class="wrapper wrapper-content p-xl"-->

        <div class="ibox-content p-xl">

            <div class = "row">
                <div class="col-sm-8">
                    <!--h5>From:</h5-->
                    <address>
                        <span><?php echo $cdprop; ?></span><br>
                        <?php echo $aPara[0]["deende"].", ".$aPara[0]["nrende"].", ".$aPara[0]["debair"]; ?><br>
                        <?php echo $aPara[0]["decida"].", ".$aPara[0]["cdesta"].", ".$aPara[0]["nrcepi"]; ?><br>
                        <span title="Telefone"></span> <?php echo $aPara[0]["nrtele"]; ?><br>
                        <span title="Celular"></span> <?php echo $aPara[0]["nrcelu"]; ?><br>
                        <span title="E-mail"></span> <?php echo $aPara[0]["demail"]; ?>                        
                    </address>
                </div>
                <div class="col-sm-6 text-right">
                    <span><strong>FORNECEDOR</strong></span>
                    <address>
                        <?php echo $aForn["deforn"]; ?><br>
                        <?php echo $cdforn; ?> <br>
                        <?php echo $aForn["deende"].", ".$aForn["nrende"].", ".$aForn["debair"]; ?><br>
                        <?php echo $aForn["decida"].", ".$aForn["cdesta"].", ".$aForn["nrcepi"]; ?><br>
                        <span title="E-mail"></span> <?php echo $aForn["demail"]; ?><br>
                        <span title="Telefone"></span> <?php echo $aForn["nrtele"]; ?><br>
                        <span title="Celular"></span> <?php echo $aForn["nrcelu"]; ?><br>
                    </address>
                    <p>
                        <span><strong>Data da Abertura do Pedido:  </strong><?php echo $dtpedi; ?></span><br/>
                        <span><strong>Data de Entrega do Pedido:  </strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $dtentre; ?></span>
                    </p>
                </div>
            </div>

            <div class="table-responsive m-t">
                <table class="table invoice-table">
                    <thead>
                        <th>Sequência</th>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Valor Unitário</th>
                        <th>Valor Total</th>
                    </thead>
                    <tbody>
                        <?php for ($f =1; $f <= 20; $f++) { ?>
                            <?php $cditem = "cditem[".trim($f)."]"; ?>
                            <?php $qtitem = "qtitem[".trim($f)."]"; ?>
                            <?php $vlitem = "vlitem[".trim($f)."]"; ?>
                            <?php $vltota = "vltota[".trim($f)."]"; ?>
                            <tr>
                                <?php if (isset($aItem[$f-1]["cdprod"])) {?>
                                    <td>
                                        <div>
                                            <strong><?php echo $f;?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <?php $exibeNome = $con->buscarProduto($aItem[$f-1]["cdprod"]); ?>
                                            <strong><small><?php echo $aItem[$f-1]["cdprod"]." - ".$exibeNome['deprod']; ?></small></strong>
                                        </div>
                                    </td>
                                    <td><?php echo number_format($aItem[$f-1]["qtprod"],0,",",".") ;?></td>
                                    <td><?php echo number_format($aItem[$f-1]["vlprod"],2,",",".") ;?></td>
                                    <td><?php echo number_format($aItem[$f-1]["vltota"],2,",",".") ;?></td>
                                <?php }?>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>                
            </div>
            <table class="table invoice-total">
                <tbody>
                    <tr>
                        <td>TOTAL   :</td>
                        <td><?php echo number_format($aPedi[0]["vlpedi"],2,",","."); ?></td>
                    </tr>
                </tbody>
            </table>
            <table class="table">
                <tbody>
                    <tr>
                        <td><strong>OBSERVAÇÕES:</strong></td>
                        <td><?php echo $aPedi[0]["deobse"]; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    <!--/div-->
    <!-- Mainly scripts -->
    <script src="../../templates/js/jquery-2.1.1.js"></script>
    <script src="../../templates/js/bootstrap.min.js"></script>
    <script src="../../templates/js/plugins/metisMenu/jquery.metisMenu.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="../../templates/js/inspinia.js"></script>

    <script type="text/javascript">
        window.print();
    </script>
</body>

</html>

