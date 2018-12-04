<?php
require_once("classes/vendor/autoload.php");
require 'classes/autoload.php';

use izv\tools\Session;

$loader = new \Twig_Loader_Filesystem(__DIR__ . '/twig');
$twig = new \Twig_Environment($loader);

/*Si intentas registrar un usuario y existe una sesion te devuelvo a index*/
session_start();
if(isset($_SESSION['email']) || isset($_SESSION['password'])){
    header('Location:index.php');
}

$titulo = 'Sign In';
echo $twig->render('_signin.html', ['titulo' => $titulo]);