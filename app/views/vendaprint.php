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

    $aItem = $con->buscarItensVenda($chave);

    $aOrde = $con->buscarVendaCindice($chave);
    $pos = strpos($aOrde[0]["cdclie"], "-");
    $cdclie = substr($aOrde[0]["cdclie"], 0, $pos-1);

    $aClie = $con->buscarCliente($cdclie);
    $declie = $aClie["declie"];

    $dtorde = $aOrde[0]["dtvenda"];
    $dtorde = strtotime($dtorde);
    $dtorde = date("d-m-Y", $dtorde);

    $dtpago = $aOrde[0]["dtpago"];
    $dtpago = strtotime($dtpago);
    $dtpago = date("d-m-Y", $dtpago);

    if (strtotime("1969-12-31") == strtotime($dtpago)){
        $dtpago="  ABERTA  ";
    }

    if (strtotime("0000-00-00") == "") {
        $dtpago="  ABERTA  ";
    }

    if (strtotime("") == "") {
        $dtpago="  ABERTA  ";
    }

    $aPara = $con->infoEmpresa();
    $cdprop=$aPara[0]["cdprop"];

    if (strlen($cdclie) > 11 ) {
        $cdclie=$util->formata($cdclie,"cnpj");
    } Else {
        $cdclie=$util->formata($cdclie,"cpf");
    }

    if (strlen($cdprop) > 11 ) {
        $cdprop=$util->formata($cdprop,"cnpj");
    } Else {
        $cdprop=$util->formata($cdprop,"cpf");
    }

var_dump($aClie);
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
    <center><h2 class="text-navy"><?php echo 'Ordem de venda No. '.$aOrde[0]["cdvenda"]; ?></h2></center>

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
                    <span><strong>CLIENTE</strong></span>
                    <address>
                        <?php echo $aClie["declie"]; ?><br>
                        <?php echo $cdclie; ?> <br>
                        <?php echo $aClie["deende"].", ".$aClie["nrende"].", ".$aClie["debair"]; ?><br>
                        <?php echo $aClie["decida"].", ".$aClie["cdesta"].", ".$aClie["nrcepi"]; ?><br>
                        <span title="E-mail"></span> <?php echo $aClie["demail"]; ?><br>
                        <span title="Telefone"></span> <?php echo $aClie["nrtele"]; ?><br>
                        <span title="Celular"></span> <?php echo $aClie["nrcelu"]; ?><br>
                    </address>
                    <p>
                        <span><strong>Data da Abertura da Venda  :  </strong><?php echo $dtorde; ?></span><br/>
                        <span><strong>Data de Fechamento da Venda:  </strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $dtpago; ?></span>
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
                        <td><?php echo number_format($aOrde[0]["vlvenda"],2,",","."); ?></td>
                    </tr>
                </tbody>
            </table>
            <table class="table">
                <tbody>
                    <tr>
                        <td><strong>OBSERVAÇÕES:</strong></td>
                        <td><?php echo $aOrde[0]["deobse"]; ?></td>
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

