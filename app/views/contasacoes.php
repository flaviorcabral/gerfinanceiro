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
    $con->pagContas();

    $acao = $_GET["acao"];

    switch ($acao) {
    case 'ver':
        $titulo = "Consulta";
        break;
    case 'edita':
        $titulo = "Alteração";
        break;
    case 'apaga':
        $titulo = "Exclusão";
        break;
    case 'nova':
        $titulo = "Inclusão";
        break;
    default:
        header('Location: home.php');
    }

    include "layouts/cookiesSessao.php";

    if($acao != "nova")
    {
        $conta = $con->conta;
    }

    if($acao == "nova")
    {
        $conta = $con->conta;
    }

    $clientes = $con->listarClientes();
    $fornecedores = $con->listaFornecedores();
    $pedidos = $con->listaPedidos();

?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gerenciador Financeiro | Principal </title>

    <link href="../../templates/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../templates/font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="../../templates/css/animate.css" rel="stylesheet">
    <link href="../../templates/css/style.css" rel="stylesheet">

</head>

<body>
    <div id="wrapper">

        <?php include "layouts/meusdados.php"; ?>

        <div id="page-wrapper" class="gray-bg">

            <?php include "layouts/cabecalho.php"; ?>

            <div class="wrapper wrapper-content">

                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <button type="button" class="btn btn-warning btn-lg btn-block"><i
                                                        class="fa fa-money"></i> Contas a <?php echo $_SESSION['tipo']; ?> - <small><?php echo $titulo; ?></small>
                            </button>
                        </div>

                        <div class="ibox-content">
                            <form class="form-horizontal" method="POST" enctype="multipart/form-data">

                                <div>
                                    <center>
                                        <?php if($acao == "edita") {?>
                                            <button class="btn btn-sm btn-primary" name = "editar" type="submit"><strong>Alterar</strong></button>
                                        <?php }?>
                                        <?php if($acao == "apaga") {?>
                                            <button class="btn btn-sm btn-danger" name = "apagar" type="submit"><strong>Apagar</strong></button>
                                        <?php }?>
                                        <?php if($acao == "nova") {?>
                                            <button class="btn btn-sm btn-danger" name = "salvar" type="submit"><strong>Salvar</strong></button>
                                        <?php }?>
                                        <button class="btn btn-sm btn-warning " type="button" onClick="history.go(-1)"><strong>Retornar</strong></button>
                                    </center>
                                </div>

                                    <div class="row">
                                            <div class="form-group">
                                                <label class="col-md-2 control-label" for="textinput">Nº OS/Pedido</label>
                                                <div class="col-md-3">
                                                    <input id="cdcont" name="cdorig" value="<?php echo $conta[0]["cdorig"];?>" type="text" placeholder="" class="form-control" maxlength = "15" readonly="">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-2 control-label" for="textinput">Descrição</label>
                                                <div class="col-md-6">
                                                    <input id="decont" name="decont" value="<?php echo $conta[0]["decont"];?>" type="text" placeholder="" class="form-control" maxlength = "50" <?php if($acao != 'nova'): ?>readonly=""<?php endif; ?>>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-2 control-label" for="textinput">Data de Vencimento</label>
                                                <div class="col-md-3">
                                                    <input id="dtcont" name="dtcont" value="<?php echo $conta[0]["dtcont"];?>" type="date" placeholder="" class="form-control" maxlength = "10" <?php if($acao == 'ver' or $acao == 'apaga'): ?>readonly=""<?php endif; ?>>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-2 control-label" for="textinput">Valor</label>
                                                <div class="col-md-3">
                                                    <input id="vlcont" name="vlcont" value="<?php echo number_format($conta[0]["vlcont"],2,",",".");?>" type="text" placeholder="" class="form-control" maxlength = "15" <?php if($acao != 'nova'): ?>readonly=""<?php endif; ?>>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-2 control-label" for="textinput">Tipo</label>
                                                <div class="col-md-2">
                                                    <select name="cdtipo" id="cdtipo" <?php if($acao == 'ver' or $acao == 'apaga'): ?>disabled<?php endif; ?>>
                                                        <option value=<?php echo $_SESSION['tipo']; ?>><?php echo $_SESSION['tipo']; ?></option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-2 control-label" for="textinput">Cliente/Fornecedor</label>
                                                <div class="col-md-2">
                                                    <select name="cdquem" id="cdquem" style="width:250%" <?php if($acao == 'ver' or $acao == 'apaga'): ?>disabled<?php endif; ?>>
                                                        <?php if($_SESSION['tipo'] == 'Receber'): ?>
                                                            <option>CLIENTES</option>
                                                            <?php for($i=0;$i < count($clientes);$i++) { ?>
                                                                <?php if (trim($conta[0]["cdquem"]) == $clientes[$i]["cdclie"]." - ".$clientes[$i]["declie"] ) {?>
                                                                    <option selected=""><?php echo $clientes[$i]["cdclie"]." - ".$clientes[$i]["declie"];?></option>
                                                                <?php } Else {?>
                                                                    <option><?php echo $clientes[$i]["cdclie"]." - ".$clientes[$i]["declie"];?></option>
                                                                <?php }?>
                                                            <?php }?>
                                                        <?php endif; ?>
                                                        <?php if($_SESSION['tipo'] == 'Pagar'): ?>
                                                            <option>FORNECEDORES</option>
                                                            <?php for($i=0;$i < count($fornecedores);$i++) { ?>
                                                                <?php if (trim($conta[0]["cdquem"]) == $fornecedores[$i]["cdforn"]." - ".$fornecedores[$i]["deforn"]) {?>
                                                                    <option selected=""><?php echo $fornecedores[$i]["cdforn"]." - ".$fornecedores[$i]["deforn"];?></option>
                                                                <?php } Else {?>
                                                                    <option><?php echo $fornecedores[$i]["cdforn"]." - ".$fornecedores[$i]["deforn"];?></option>
                                                                <?php }?>
                                                            <?php }?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-2 control-label" for="textinput">Data Pagamento</label>
                                                <div class="col-md-3">
                                                    <input id="dtpago" name="dtpago" value="<?php echo $conta[0]["dtpago"];?>" type="date" placeholder="" class="form-control" maxlength = "10" <?php if($acao == 'ver' or $acao == 'apaga'): ?>readonly=""<?php endif; ?>>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-2 control-label" for="textinput">Valor Pago</label>
                                                <div class="col-md-3">
                                                    <input id="vlpago" name="vlpago" value="<?php echo number_format($conta[0]["vlpago"],2,",",".");?>" type="text" placeholder="" class="form-control" maxlength = "10" <?php if($acao == 'ver' or $acao == 'apaga'): ?>readonly=""<?php endif; ?>>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-2 control-label" for="textinput">Observações</label>
                                                <div class="col-md-8">
                                                    <textarea class="form-control" id="deobse" wrap="physical" cols=50 rows=3 name="deobse" placeholder="" <?php if($acao == 'ver' or $acao == 'apaga'): ?>readonly=""<?php endif; ?>><?php echo $conta[0]["deobse"];?></textarea>
                                                </div>
                                            </div>

                                            <input type="hidden" name="cdcont" value="<?php echo $conta[0]["cdcont"];?>">
                                    </div>
                                <br>
                                <div>
                                    <center>
                                        <?php if($acao == "edita") {?>
                                            <button class="btn btn-sm btn-primary" name = "editar" type="submit"><strong>Alterar</strong></button>
                                        <?php }?>
                                        <?php if($acao == "apaga") {?>
                                            <button class="btn btn-sm btn-danger" name = "apagar" type="submit"><strong>Apagar</strong></button>
                                        <?php }?>
                                        <?php if($acao == "nova") {?>
                                            <button class="btn btn-sm btn-danger" name = "salvar" type="submit"><strong>Salvar</strong></button>
                                        <?php }?>
                                        <button class="btn btn-sm btn-warning " type="button" onClick="history.go(-1)"><strong>Retornar</strong></button>
                                    </center>
                                </div>

                            </form>
                        </div>
                    </div>
                <!--/div-->
            </div>
        </div>
    </div>
    <!-- Mainly scripts -->
    <script src="../../templates/js/jquery-2.1.1.js"></script>
    <script src="../../templates/js/bootstrap.min.js"></script>
    <script src="../../templates/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="../../templates/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <script src="../../templates/js/plugins/jeditable/jquery.jeditable.js"></script>
    <script src="../../templates/js/plugins/dataTables/datatables.min.js"></script>

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

    <script>

        $(document).ready(function(){
            $('.dataTables-example').DataTable({
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                    { extend: 'copy'},
                    {extend: 'csv', title: 'Gerenciador Financeiro'},
                    {extend: 'excel', title: 'Gerenciador Financeiro'},
                    {extend: 'pdf', title: 'Gerenciador Financeiro'},

                    {extend: 'print',
                     customize: function (win){
                            $(win.document.body).addClass('white-bg');
                            $(win.document.body).css('font-size', '10px');

                            $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                    }
                    }
                ]

            });

            /* Init DataTables */
            var oTable = $('#editable').DataTable();

            /* Apply the jEditable handlers to the table */
            oTable.$('td').editable( '../example_ajax.php', {
                "callback": function( sValue, y ) {
                    var aPos = oTable.fnGetPosition( this );
                    oTable.fnUpdate( sValue, aPos[0], aPos[1] );
                },
                "submitdata": function ( value, settings ) {
                    return {
                        "row_id": this.parentNode.getAttribute('id'),
                        "column": oTable.fnGetPosition( this )[2]
                    };
                },

                "width": "90%",
                "height": "100%"
            } );


        });

        $(document).ready(function() {
            $('.chart').easyPieChart({
                barColor: '#f8ac59',
//                scaleColor: false,
                scaleLength: 5,
                lineWidth: 4,
                size: 80
            });

            $('.chart2').easyPieChart({
                barColor: '#1c84c6',
//                scaleColor: false,
                scaleLength: 5,
                lineWidth: 4,
                size: 80
            });

            var data2 = [
                [gd(2016, 1, 1), 400], [gd(2016, 2, 1), 300], [gd(2016, 3, 1), 180], [gd(2016, 4, 1), 150],
                [gd(2016, 5, 1), 88], [gd(2016, 6, 1), 455], [gd(2016, 7, 1), 93]
            ];

            var data3 = [
                [gd(2016, 1, 1), 800], [gd(2016, 2, 1), 500], [gd(2016, 3, 1), 600], [gd(2016, 4, 1), 700],
                [gd(2016, 5, 1), 178], [gd(2016, 6, 1), 555], [gd(2016, 7, 1), 993]
            ];

            var dataset = [
                {
                    label: "Receita Prevista",
                    data: data3,
                    color: "#1ab394",
                    bars: {
                        show: true,
                        align: "center",
                        barWidth: 24 * 60 * 60 * 600,
                        lineWidth:0
                    }

                }, {
                    label: "Receita Realizada",
                    data: data2,
                    yaxis: 2,
                    color: "#1C84C6",
                    lines: {
                        lineWidth:1,
                            show: true,
                            fill: true,
                        fillColor: {
                            colors: [{
                                opacity: 0.2
                            }, {
                                opacity: 0.4
                            }]
                        }
                    },
                    splines: {
                        show: false,
                        tension: 0.6,
                        lineWidth: 1,
                        fill: 0.1
                    },
                }
            ];


            var options = {
                xaxis: {
                    mode: "time",
                    tickSize: [3, "month"],
                    tickLength: 0,
                    axisLabel: "Date",
                    axisLabelUseCanvas: true,
                    axisLabelFontSizePixels: 12,
                    axisLabelFontFamily: 'Arial',
                    axisLabelPadding: 10,
                    color: "#d5d5d5"
                },
                yaxes: [{
                    position: "left",
                    max: 1070,
                    color: "#d5d5d5",
                    axisLabelUseCanvas: true,
                    axisLabelFontSizePixels: 12,
                    axisLabelFontFamily: 'Arial',
                    axisLabelPadding: 3
                }, {
                    position: "right",
                    clolor: "#d5d5d5",
                    axisLabelUseCanvas: true,
                    axisLabelFontSizePixels: 12,
                    axisLabelFontFamily: ' Arial',
                    axisLabelPadding: 67
                }
                ],
                legend: {
                    noColumns: 1,
                    labelBoxBorderColor: "#000000",
                    position: "nw"
                },
                grid: {
                    hoverable: false,
                    borderWidth: 0
                }
            };

            function gd(year, month, day) {
                return new Date(year, month - 1, day).getTime();
            }

            var previousPoint = null, previousLabel = null;

            $.plot($("#flot-dashboard-chart"), dataset, options);

            var mapData = {
                "US": 298,
                "SA": 200,
                "DE": 220,
                "FR": 540,
                "CN": 120,
                "AU": 760,
                "BR": 550,
                "IN": 200,
                "GB": 120,
            };

            $('#world-map').vectorMap({
                map: 'world_mill_en',
                backgroundColor: "transparent",
                regionStyle: {
                    initial: {
                        fill: '#e4e4e4',
                        "fill-opacity": 0.9,
                        stroke: 'none',
                        "stroke-width": 0,
                        "stroke-opacity": 0
                    }
                },

                series: {
                    regions: [{
                        values: mapData,
                        scale: ["#1ab394", "#22d6b1"],
                        normalizeFunction: 'polynomial'
                    }]
                },
            });
        });
    </script>
</body>
</html>
