<?php

include_once 'config.php';

ini_set ('display_errors', 1 );
error_reporting ( E_ALL | E_STRICT );
//error_reporting (0);

$con = new Controller();
$con->pagLogin();

?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gerenciador Financeiro | Acesso </title>

    <link href="templates/css/bootstrap.min.css" rel="stylesheet">
    <link href="templates/font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="templates/css/animate.css" rel="stylesheet">
    <link href="templates/css/style.css" rel="stylesheet">

</head>

<body class="gray-bg">

    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <h3>Bem vindo</h3>
            <div class="m-b-sm">
                <center>
                    <img class="img-square img-responsive" src="templates/img/print_calculator.png" style="width: 110px; height: 100px">
                </center>
            </div>
            <p>Gerenciador Financeiro</p>
            <p><strong>Informe os dados de acesso.</strong></p>
            <form class="m-t" role="form" method="post">
                <div class="form-group">
                    <input id="login" name = "delogin" type="text" class="form-control" placeholder="login" required="">
                </div>
                <div class="form-group">
                    <input id="desenh" name = "desenh" type="password" class="form-control" placeholder="senha" required="">
                </div>
                <button type="submit" name="login" class="btn btn-warning block full-width m-b">Acessar</button>
            </form>
            <p class="m-t"> <small>Gerenciador Financeiro 2017</small> </p>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="templates/js/jquery-2.1.1.js"></script>
    <script src="templates/js/bootstrap.min.js"></script>

</body>

</html>
