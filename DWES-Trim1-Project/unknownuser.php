<?php
require_once("classes/vendor/autoload.php");
require 'classes/autoload.php';

use izv\data\Usuario;
use izv\database\Database;
use izv\managedata\ManageUsuario;
use izv\tools\Reader;

/*Obtengo todos los usuarios*/
$db = new Database();
$manager = new ManageUsuario($db);
$usuarios = $manager->getAll();
$db->close();

/*Accedo a la sesion para borrar sus datos y destruirla puesto que son incorrectos*/
session_start();
unset($_SESSION['email']);
unset($_SESSION['password']);
session_destroy();

/*Preparo el twig*/
$loader = new \Twig_Loader_Filesystem(__DIR__ . '/twig');
$twig = new \Twig_Environment($loader);

$titulo = 'Listado de usuarios';
$listado = $usuarios;

$lista = array();
$alias = '';
for($i = 0 ; $i < count($usuarios) ; $i ++){
    if(is_null($usuarios[$i]->getAlias())){
        $alias = '---';
    }
    else{
        $alias = $usuarios[$i]->getAlias();
    }
    $item = array('nombre' => $usuarios[$i]->getNombre(), 'correo' => $usuarios[$i]->getCorreo(), 'alias' => $alias );
    $lista[] = $item;
}

echo $twig->render('_nosession_landing.html', ['titulo' => $titulo,'lista' => $lista]);