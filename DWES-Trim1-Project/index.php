<?php
require_once("classes/vendor/autoload.php");
require 'classes/autoload.php';

use izv\data\Usuario;
use izv\database\Database;
use izv\managedata\ManageUsuario;
use izv\tools\Reader;
use izv\tools\Util;
//use izv\tools\Session;

/*Obtengo todos los usuarios*/
$db = new Database();
$manager = new ManageUsuario($db);
$usuarios = $manager->getAll();
$db->close();

/*Preparo twig*/
$loader = new \Twig_Loader_Filesystem(__DIR__ . '/twig');
$twig = new \Twig_Environment($loader);

/*Preparo las variables que usaré en el html*/
$titulo = '';
$user = '';

/*Accedo a la sesión para comprobar el email y la clave introducidos*/
session_start();
if(isset($_SESSION['email']) && isset($_SESSION['password'])) {
    $coincidencia = false;
    /*compruebo que está en la db*/
    for($i = 0 ; $i < count($usuarios) ; $i++){
        if($_SESSION['email'] == $usuarios[$i]->getCorreo() && $_SESSION['password'] ==$usuarios[$i]->getClave()){
            /*veo si es un usuarios normal*/
            if($usuarios[$i]->getAdmin() == 0){
                $titulo = 'Bienvenido '.$usuarios[$i]->getNombre();
                /*array con la info del user*/
                $activo = 'Si';
                $alias = '----';
                /*Si no está activo borro la sesion y recargo la pagina (solo pueden ver su contenido los usuarios activos)*/
                if($usuarios[$i]->getActivo() == 0){
                    unset($_SESSION['email']);
                    unset($_SESSION['password']);
                    session_destroy();
                    header('Location: index.php');
                }
                if(!is_null($usuarios[$i]->getAlias())){
                    $alias = $usuarios[$i]->getAlias();
                }
                $item = array('nombre' => $usuarios[$i]->getNombre(), 
                              'correo' => $usuarios[$i]->getCorreo(), 
                              'alias' => $alias,
                              'activo' => $activo);
                echo $twig->render('_sessionuser_landing.html', ['titulo' => $titulo, 'user' => $item]);    
            }
            /*veo si es admin*/
            else{
                $titulo = 'Bienvenido Admin '.$usuarios[$i]->getNombre();
                /*array con la info del user*/
                $activo='Si';
                $admin='Si';
                $alias = '----';
                if(!is_null($usuarios[$i]->getAlias())){
                    $alias = $usuarios[$i]->getAlias();
                }
                if($usuarios[$i]->getActivo() == 0){
                    unset($_SESSION['email']);
                    unset($_SESSION['password']);
                    session_destroy();
                    header('Location: index.php');
                }
                $item = array('nombre' => $usuarios[$i]->getNombre(), 
                              'correo' => $usuarios[$i]->getCorreo(), 
                              'alias' => $alias,
                              'activo' => $activo,
                              'admin' => $admin,
                              'id' => $usuarios[$i]->getId(),
                              'fechalta' => $usuarios[$i]->getFechaalta());
                echo $twig->render('_sessionadmin_landing.html', ['titulo' => $titulo, 'admin' => $item]);
            }
            $coincidencia = true;
            $i = count($usuarios);
        }
    }
    /*si no está*/
    if(!$coincidencia){
        unset($_SESSION['email']);
        unset($_SESSION['password']);
        session_destroy();
        header('Location:/DWES-Trim1-Project/unknownuser.php');
    }
}
/*si no hay sesion*/
else{
    header('Location:/DWES-Trim1-Project/unknownuser.php');
}