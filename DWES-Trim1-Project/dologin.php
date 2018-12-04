<?php
require('classes/autoload.php');

use izv\data\Usuario;
use izv\database\Database;
use izv\managedata\ManageUsuario;
use izv\tools\Util;

/*Si existe una sesion y trata de hacer login simplemente vuelvo a index, que es quien maneja las sesiones*/
session_start();
if(isset($_SESSION['email']) || isset($_SESSION['password'])){
    header('Location: index.php');
}

/*Obtengo ese usuario a partir del correo y la contraseña para guardar en la sesion
ese correo y principalmente para guardar la contraseña cifrada de la bd*/
/*Obtengo todos los usuarios*/
$db = new Database();
$manager = new ManageUsuario($db);
$usuarios = $manager->getAll();
$db->close();

for($i = 0 ; $i < count($usuarios) ; $i++){
    if($_POST['email'] == $usuarios[$i]->getCorreo() && Util::verificarClave($_POST['clave'],$usuarios[$i]->getClave())){
        /*Guardo en la sesion el correo y la clave encriptada*/
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['password'] = $usuarios[$i]->getClave();
        $i = count($usuarios);
    }
}

header('Location: index.php');