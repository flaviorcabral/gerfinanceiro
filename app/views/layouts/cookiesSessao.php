<?php

    //codigo do usuario
    if (isset($_COOKIE['cdusua'])) {
        $cdusua = $_COOKIE['cdusua'];
    }

    //tipo de usuario
    if (isset($_COOKIE['cdtipo'])) {
        $cdtipo = $_COOKIE['cdtipo'];
    }

    //localização da foto
    if (isset($_COOKIE['defoto'])) {
        $defoto = $_COOKIE['defoto'];
    }

    //tipo de usuario
    if (isset($_COOKIE['cdtipo'])) {
        $cdtipo = $_COOKIE['cdtipo'];
    }

    //email de usuario
    if (isset($_COOKIE['demail'])) {
        $demail = $_COOKIE['demail'];
    }

    $detipo="Tipo Não Identificado";
    if ($cdtipo == "A") {
        $detipo="Administrador";
    }
    if ($cdtipo == "F") {
        $detipo="Funcionário";
    }
    if ($cdtipo == "O") {
        $detipo="Oficina";
    }
    if ($cdtipo == "M") {
        $detipo="Mecânico";
    }
    if ($cdtipo == "C") {
        $detipo="Cliente";
    }

    // reduzir o tamanho do nome do usuario
    $deusua = $_SESSION['login'];
    $deusua = substr($deusua, 0,15);

?>