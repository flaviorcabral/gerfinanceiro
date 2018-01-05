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
    $con->pagParamentros();

    //$acao = $_GET["acao"];
    $acao = "edita";
    //$chave = trim($_GET["chave"]);
    $chave="";

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
    default:
        header('Location: index.php');
    }

    include "layouts/cookiesSessao.php";

    $titulo="Atualização";
    $parametros = $con->infoEmpresa();
    $estados = $con->listarEstadosBra();

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
                <!--div class="col-lg-12"-->
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <button type="button" class="btn btn-warning btn-lg btn-block"><i
                                                        class="fa fa-info-circle"></i> Parâmetros do Sistema - <small><?php echo $titulo; ?></small>
                            </button>
                        </div>

                        <div class="ibox-content">
                            <form class="form-horizontal" method="POST" enctype="multipart/form-data">
                                <div class="row">
                                        <center><h2><span class="text-warning"><strong>DADOS DA EMPRESA</strong></span></h2></center>
                                        </br>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label" for="textinput">Cnpj</label>
                                            <div class="col-md-3">
                                                <input id="cdprop" name="cdprop" value="<?php echo $parametros[0]["cdprop"]; ?>" type="text" placeholder="" class="form-control" maxlength = "14" autofocus>
                                            </div>
                                            <span><small>sem formatação</small></span>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label" for="textinput">Razão Social</label>
                                            <div class="col-md-8">
                                                <input id="deprop" name="deprop" value="<?php echo $parametros[0]["deprop"]; ?>" type="text" placeholder="" class="form-control" maxlength = "100" autofocus>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label" for="textinput">Inscrição Estadual</label>
                                            <div class="col-md-3">
                                                <input id="nrinsc" name="nrinsc" value="<?php echo $parametros[0]["nrinsc"]; ?>" type="text" placeholder="" class="form-control" maxlength = "20" autofocus>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label" for="textinput">Inscrição Municipal - CCM</label>
                                            <div class="col-md-3">
                                                <input id="nrccm" name="nrccm" value="<?php echo $parametros[0]["nrccm"]; ?>" type="text" placeholder="" class="form-control" maxlength = "20" autofocus>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label" for="textinput">Endereço</label>
                                            <div class="col-md-8">
                                                <input id="deende" name="deende" value="<?php echo $parametros[0]["deende"]; ?>" type="text" placeholder="" class="form-control" maxlength = "100">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label" for="textinput">Número</label>
                                            <div class="col-md-2">
                                                <input id="nrende" name="nrende" value="<?php echo $parametros[0]["nrende"]; ?>" type="number" placeholder="" class="form-control" maxlength = "10">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label" for="textinput">Complemento</label>
                                            <div class="col-md-4">
                                                <input id="decomp" name="decomp" value="<?php echo $parametros[0]["decomp"]; ?>" type="text" placeholder="" class="form-control" maxlength = "50">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label" for="textinput">Bairro</label>
                                            <div class="col-md-4">
                                                <input id="debair" name="debair" value="<?php echo $parametros[0]["debair"]; ?>" type="text" placeholder="" class="form-control" maxlength = "50">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label" for="textinput">Municipio</label>
                                            <div class="col-md-4">
                                                <input id="decida" name="decida" value="<?php echo $parametros[0]["decida"]; ?>" type="text" placeholder="" class="form-control" maxlength = "50">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label" for="textinput">Cep</label>
                                            <div class="col-md-2">
                                                <input id="nrcepi" name="nrcepi" value="<?php echo $parametros[0]["nrcepi"]; ?>" type="text" placeholder="" class="form-control" maxlength = "08">
                                            </div>
                                            <span><small>sem formatação</small></span>
                                        </div>

                                        <div class="form-group">
                                            <?php $cdesta = $parametros[0]["cdesta"] ;?>
                                            <label class="col-md-2 control-label" for="textinput">Estado</label>
                                            <div class="col-md-4">
                                                <select name="cdesta" id="cdesta">
                                                    <?php for($i=0;$i < count($estados);$i++) { ?>
                                                      <?php if ($parametros[0]["cdesta"] == str_pad($estados[$i]["cdesta"],02," ",STR_PAD_LEFT)." - ".$estados[$i]["deesta"]) {?>
                                                        <option selected =""><?php echo str_pad($estados[$i]["cdesta"],02," ",STR_PAD_LEFT)." - ".$estados[$i]["deesta"];?></option>
                                                      <?php } Else {?>
                                                        <option><?php echo str_pad($estados[$i]["cdesta"],02," ",STR_PAD_LEFT)." - ".utf8_encode($estados[$i]["deesta"]);?></option>
                                                      <?php }?>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label" for="textinput">Telefone</label>
                                            <div class="col-md-2">
                                                <input id="nrtele" name="nrtele" value="<?php echo $parametros[0]["nrtele"]; ?>" type="text" placeholder="" class="form-control" maxlength = "20">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label" for="textinput">Celular</label>
                                            <div class="col-md-2">
                                                <input id="nrcelu" name="nrcelu" value="<?php echo $parametros[0]["nrcelu"]; ?>" type="text" placeholder="" class="form-control" maxlength = "20">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label" for="textinput">E-Mail</label>
                                            <div class="col-md-8">
                                                <input id="demail" name="demail" value="<?php echo $parametros[0]["demail"]; ?>" type="text" placeholder="" class="form-control" maxlength = "255">
                                            </div>
                                        </div>

                                    <!--/div-->
                                </div>
                                    <!--/form-->
                                    <!--/div-->
                                <!--/div-->

                                <div>
                                    <center>
                                        <?php if($acao == "edita") {?>
                                            <button class="btn btn-sm btn-primary" name = "editar" type="submit"><strong>Salvar</strong></button>
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
                    {extend: 'csv'},
                    {extend: 'excel', title: 'ExampleFile'},
                    {extend: 'pdf', title: 'ExampleFile'},

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
