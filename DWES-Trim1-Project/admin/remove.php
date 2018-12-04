<?php
require_once("../classes/vendor/autoload.php");
require '../classes/autoload.php';

use izv\data\Usuario;
use izv\database\Database;
use izv\managedata\ManageUsuario;
use izv\tools\Reader;
use izv\tools\Util;
use izv\tools\Session;

/*Elimino el usuario de la base de datos usando la sesion*/
$db = new Database();
$manager = new ManageUsuario($db);
$resultado = 0;

/*Si sigue viva la sesion procedo a borrar*/
session_start();
if(isset($_SESSION['email']) && isset($_SESSION['password'])){
    $resultado = $manager->removeEmail($_SESSION['email']);    
}

/*Borro los datos de la sesion y la destruyo*/
unset($_SESSION['email']);
unset($_SESSION['password']);
session_destroy();
header('Location: index.php');
