<?php
require_once("../classes/vendor/autoload.php");
require '../classes/autoload.php';

use izv\tools\Session;
use izv\data\Usuario;
use izv\database\Database;
use izv\managedata\ManageUsuario;
use izv\tools\Reader;

/*Obtengo todos los usuarios*/
$db = new Database();
$manager = new ManageUsuario($db);
$usuarios = $manager->getAll();
$db->close();

/*Si hay algo de la sesion que no este seteado simplemente vuelvo a index.php*/
session_start();
if(!isset($_SESSION['email']) || !isset($_SESSION['password'])){
    header('Location:../index.php');
}

/*Preparo twig*/
$loader = new \Twig_Loader_Filesystem(__DIR__ . '/twig');
$twig = new \Twig_Environment($loader);

/*Preparo una lista con todos los usuarios menos el admin que tenga iniciada la sesion que es lo que se cargará en _users.html*/
$titulo = 'Listado de usuarios';
$listado = $usuarios;

$lista = array();
for($i = 0 ; $i < count($usuarios) ; $i ++){
    if($_SESSION['email'] != $usuarios[$i]->getCorreo()){
        $alias = '---';
        $admin = 'No';
        $activo = 'No';
        if(!is_null($usuarios[$i]->getAlias())){
            $alias = $usuarios[$i]->getAlias();
        }
        if($usuarios[$i]->getAdmin()!=0){
            $admin = 'Si';
        }
        if($usuarios[$i]->getActivo()!=0){
            $activo = 'Si';
        }
        $item = array('nombre' => $usuarios[$i]->getNombre(), 
                      'correo' => $usuarios[$i]->getCorreo(), 
                      'alias' => $alias,
                      'id' => $usuarios[$i]->getId(),
                      'activo' => $activo,
                      'admin' => $admin);
        $lista[] = $item;    
    }
    
}

echo $twig->render('_users.html', ['titulo' => $titulo,'lista' => $lista]);