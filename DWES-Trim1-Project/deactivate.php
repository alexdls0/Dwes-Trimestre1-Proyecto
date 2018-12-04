<?php
require 'classes/autoload.php';
require 'classes/vendor/autoload.php';

use izv\data\Usuario;
use izv\database\Database;
use izv\managedata\ManageUsuario;
use izv\tools\Mail;
use izv\tools\Util;
use izv\tools\Session;

session_start();

/*Modifico activo a 0 usando el correo almacenado en la sesion*/
$resultado = 0;
$sql = 'update usuario set activo = 0 where correo = :correo';
$db = new Database();
if($db->connect()) {
    $conexion = $db->getConnection();
    $sentencia = $conexion->prepare($sql);
    $sentencia->bindValue('correo', $_SESSION['email']);
    if($sentencia->execute()) {
        $resultado = $sentencia->rowCount();
    }
    $db->close();
}

/*Obtengo el usuario que tiene ese correo*/
$db = new Database();
$manager = new ManageUsuario($db);
$usuarios = $manager->getAll();
$db->close();
$user = null;

for($i = 0 ; $i < count($usuarios); $i++){
    if($usuarios[$i]->getCorreo() == $_SESSION['email']){
        $user = $usuarios[$i];
    }
}

/*Envio un correo de activacion usando ese usuario*/
$resultado2=false;
if($resultado > 0 && $user!=null){
    $resultado2 = Mail::sendActivation($user);
}

/*tras desactivar la cuenta puedo borrar la sesion o simplemente dejar que index.php maneje si el usurio
está o no está activo*/
unset($_SESSION['email']);
unset($_SESSION['password']);
session_destroy();

header('Location:index.php');