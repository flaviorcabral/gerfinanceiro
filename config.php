<?php

function __autoload($classe)
{
    if (file_exists("app/models/" . $classe . ".class.php")) {
        include_once("app/models/" . $classe . ".class.php");
    } elseif (file_exists("models/" . $classe . ".class.php")) {
        include_once("models/" . $classe . ".class.php");
    } elseif (file_exists("app/controller/" . $classe . ".class.php")) {
        include_once("app/controller/" . $classe . ".class.php");
    } elseif (file_exists("controller/" . $classe . ".class.php")) {
        include_once("controller/" . $classe . ".class.php");
    } elseif (file_exists("app/util/" . $classe . ".class.php")) {
        include_once("app/util/" . $classe . ".class.php");
    } elseif (file_exists("util/" . $classe . ".class.php")) {
        include_once("util/" . $classe . ".class.php");
    } elseif (file_exists("../app/models/" . $classe . ".class.php")) {
        include_once("../app/models/" . $classe . ".class.php");
    } elseif (file_exists("../app/controller/" . $classe . ".class.php")) {
        include_once("../app/controller/" . $classe . ".class.php");
    } elseif (file_exists("../app/util/" . $classe . ".class.php")) {
        include_once("../app/util/" . $classe . ".class.php");
    } elseif (file_exists("../controller/" . $classe . ".class.php")) {
        include_once("../controller/" . $classe . ".class.php");
    } elseif (file_exists("../models/" . $classe . ".class.php")) {
        include_once("../models/" . $classe . ".class.php");
    } elseif (file_exists("../util/" . $classe . ".class.php")) {
        include_once("../util/" . $classe . ".class.php");
    }
}