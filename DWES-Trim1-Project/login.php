<?php
require_once("classes/vendor/autoload.php");
require 'classes/autoload.php';

use izv\data\Usuario;
use izv\database\Database;
use izv\managedata\ManageUsuario;
use izv\tools\Session;

/*Compruebo si existe alguna sesion, en caso de que exista simplemente vuelvo a index.php que maneja las sesiones*/
session_start();
if(isset($_SESSION['email']) || isset($_SESSION['password'])){
    header('Location:index.php');
}

$db = new Database();
$manager = new ManageUsuario($db);
$usuarios = $manager->getAll();
$db->close();

$loader = new \Twig_Loader_Filesystem(__DIR__ . '/twig');
$twig = new \Twig_Environment($loader);

$titulo = 'Login';

echo $twig->render('_login.html', ['titulo' => $titulo]);