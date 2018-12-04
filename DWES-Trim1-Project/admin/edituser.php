<?php
require_once("../classes/vendor/autoload.php");
require '../classes/autoload.php';

use izv\tools\Session;
use izv\data\Usuario;
use izv\database\Database;
use izv\managedata\ManageUsuario;
use izv\tools\Reader;
use izv\tools\Util;
use izv\tools\Render;

$id = Reader::read('id');

/*Compruebo la sesion, en caso de que tenga algún error simplemente vuelvo a index.php*/
session_start();
if(!isset($_SESSION['email']) || !isset($_SESSION['password'])){
    header('Location:../index.php');
}

/*Obtengo todos los usuarios*/
$db = new Database();
$manager = new ManageUsuario($db);
$usuarios = $manager->getAll();
$db->close();

/*Busco el que tiene ese id, si lo encuentro guardo ese id en la sesion*/
$user = null;
for($i = 0 ; $i < count($usuarios) ; $i++ ){
    if($id == $usuarios[$i]->getId()){
        $user = $usuarios[$i];
        $_SESSION['id'] = $id;
        $i = count($usuarios);
    }
}

/*Si no encuentra ese id en la bd vuelvo a index*/
if(is_null($user)){
    unset($_SESSION['email']);
    unset($_SESSION['password']);
    session_destroy();
    header('Location:../index.php');
}
/*Paso a _edit.html tanto titulo como la lista que contiene los datos de ese usuario*/

$loader = new \Twig_Loader_Filesystem(__DIR__ . '/twig');
$twig = new \Twig_Environment($loader);

$titulo = 'Edit Users';

echo $twig->render('_edituser.html', ['titulo' => $titulo]);