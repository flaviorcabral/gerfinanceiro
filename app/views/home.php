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
    $con->pagHome();

    include "layouts/cookiesSessao.php";

    $demails="flaviorcabral@gmail.com";
    $deteles="83 98157-7582";

?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gerenciador Financeiro</title>

    <link href="../../templates/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../templates/font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="../../templates/css/animate.css" rel="stylesheet">
    <link href="../../templates/css/style.css" rel="stylesheet">

</head>

<body>
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element"> <span>
                                <img alt="foto" width="80" height="80" class="img-circle" src="<?php echo $defoto; ?>" />
                                 </span>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"><?php echo $deusua; ?></strong>
                                 </span> <span class="text-muted text-xs block"><?php echo $detipo; ?><b class="caret"></b></span> </span> </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li><a href="meusdados.php">Atualizar Meus Dados</a></li>
                                <li><a href="minhasenha.php">Alterar Minha Senha</a></li>
                                <li class="divider"></li>
                                <li><a href="../../index.php?logoff">Sair</a></li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        <a href="venda.php"><i class="fa fa-edit"></i><span class="nav-label">Vendas</span></a>
                    </li>

                    <li>
                        <a href="pedidos.php"><i class="fa fa-pencil-square-o"></i><span class="nav-label">Pedidos</span></a>
                    </li>

                    <li>
                        <a href="cliente.php"><i class="fa fa-users"></i> <span class="nav-label">Clientes</span></a>
                    </li>

                    <li>
                        <a href="produtos.php"><i class="fa fa-wrench"></i><span class="nav-label">Produtos/Estoque</span></a>
                    </li>


                    <?php if ($cdtipo == 'A'){?>
                        <li> <!--class="special_link"-->
                            <a href="contas.php?pagar"><i class="fa fa-money"></i> <span class="nav-label">Contas a Pagar</span></a>
                        </li>

                        <li> <!--class="special_link"-->
                            <a href="contas.php?receber"><i class="fa fa-money"></i> <span class="nav-label">Contas a Receber</span></a>
                        </li>
                    <?php }?>

                    <?php if ($cdtipo == 'A'){?>
                        <li>
                            <a href="home.php"><i class="fa fa-calculator"></i> <span class="nav-label">Fluxo de Caixa</span><span class="caret"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="fluxoresumido.php">Pagar/Receber Resumido</a></li>
                                <li><a href="fluxoformpag.php">Vendas a Receber por Forma de Pagamento</a></li>
                            </ul>
                        </li>
                    <?php }?>

                    <?php if ($cdtipo == 'A'){?>

                        <li>
                               <a href="fornecedores.php"><i class="fa fa-users"></i><span class="nav-label">Fornecedores</span></a>
                        </li>

                        <li>
                            <a href="usuarios.php"><i class="fa fa-users"></i> <span class="nav-label">Usuários</span></a>
                        </li>

                       <li ><!--class="special_link"-->
                           <a href="parametros.php"><i class="fa fa-info-circle"></i> <span class="nav-label">Parâmetros</span></a>
                       </li>

                       <li>
                           <a href="historico.php"><i class="fa fa-eye"></i> <span class="nav-label">Histórico</span></a>
                       </li>

                    <?php }?>

                </ul>
            </div>
        </nav>
        <br>
        <br>

        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-warning " href="#"><i class="fa fa-bars"></i> </a>
                    </div>
                    <ul class="nav navbar-top-links navbar-left">
                        <br>
                        <li>
                            <span><?php echo  $deusua; ?></span>
                        </li>
                    </ul>
                    <ul class="nav navbar-top-links navbar-right">
                        <li>
                            <span class="m-r-sm text-muted welcome-message">Benvindo ao <strong>Gerenciador Financeiro</strong></span>
                        </li>
                        <li>
                            <a href="../../index.php?logoff">
                                <i class="fa fa-sign-out"></i> Sair
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <button type="button" class="btn btn-warning btn-lg btn-block"><i
                                                             class="fa fa-home"></i> Menu Principal 
                                </button>
                            </div>
                            <br>
                            <div class="ibox-content">
                                <div class="col-md-1"></div>
                                <div class="m-b-sm">
                                    <center>
                                        <img alt="Opção: Colocar imagem aqui" class="img-square" src=""
                                                                             style="width: 582px">
                                    </center>
                                </div>
                                <!--h1 class="logo-name">Nova Demonstração</h1-->
                                <br>
                                <br>
                                <strong>Suporte</strong><br>
                                <small><?php echo $demails; ?></small><br>
                                <small><?php echo $deteles; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Mainly scripts -->
    <script src="../../templates/js/jquery-2.1.1.js"></script>
    <script src="../../templates/js/bootstrap.min.js"></script>
    <script src="../../templates/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="../../templates/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <!-- Flot -->
    <script src="../../templates/js/plugins/flot/jquery.flot.js"></script>
    <script src="../../templates/js/plugins/flot/jquery.flot.tooltip.min.js"></script>
    <script src="../../templates/js/plugins/flot/jquery.flot.spline.js"></script>
    <script src="../../templates/js/plugins/flot/jquery.flot.resize.js"></script>
    <script src="../../templates/js/plugins/flot/jquery.flot.pie.js"></script>
    <script src="../../templates/js/plugins/flot/jquery.flot.symbol.js"></script>
    <script src="../../templates/js/plugins/flot/jquery.flot.time.js"></script>

    <!-- Peity -->
    <script src="../../templates/js/plugins/peity/jquery.peity.min.js"></script>
    <script src="../../templates/js/demo/peity-demo.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="../../templates/js/inspinia.js"></script>
    <script src="../../templates/js/plugins/pace/pace.min.js"></script>

    <!-- jQuery UI -->
    <script src="../../templates/js/plugins/jquery-ui/jquery-ui.min.js"></script>

    <!-- Jvectormap -->
    <script src="../../templates/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="../../templates/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>

    <!-- EayPIE -->
    <script src="../../templates/js/plugins/easypiechart/jquery.easypiechart.js"></script>

    <!-- Sparkline -->
    <script src="../../templates/js/plugins/sparkline/jquery.sparkline.min.js"></script>

    <!-- Sparkline demo data  -->
    <script src="../../templates/js/demo/sparkline-demo.js"></script>

</body>
</html>
