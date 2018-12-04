<?php
require_once("../classes/vendor/autoload.php");
require '../classes/autoload.php';

use izv\tools\Session;

/*Compruebo si existe alguna sesion, en caso de que no exista simplemente vuelvo a index.php*/
session_start();
if(!isset($_SESSION['email']) || !isset($_SESSION['password'])){
    header('Location:../index.php');
}

$loader = new \Twig_Loader_Filesystem(__DIR__ . '/twig');
$twig = new \Twig_Environment($loader);

$titulo = 'Edit';

echo $twig->render('_edit.html', ['titulo' => $titulo]);