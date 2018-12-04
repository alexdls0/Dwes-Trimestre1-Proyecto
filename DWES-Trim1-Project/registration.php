<?php
require 'classes/autoload.php';
require 'classes/vendor/autoload.php';

use izv\data\Usuario;
use izv\database\Database;
use izv\tools\Mail;
use izv\tools\Util;

/*Si intentas registrar un usuario y existe una sesion te devuelvo a index*/
session_start();
if(isset($_SESSION['email']) || isset($_SESSION['password'])){
    header('Location:index.php');
}

/*Aqui se evaluarán primeramente si los datos introducidos en el registro son
correctos o no. Si no son correctos simplemente volveremos al index.php*/

//En el html ya he comprobardo que todos tienen contenido usando required, me ahorro codigo
$nombre = $_POST['nombre'];
$alias = $_POST['alias'];
$email = $_POST['email'];/*No hace falta evaluarlo puesto que con el html ya me aseguro que es correcto*/
$clave = $_POST['clave'];
$claveRep = $_POST['claveRep'];

/*Comprobamos que el nombre sea solo alfabeto, sin espacios y no mayor a 50 caracteres*/
if(strlen($nombre) > 50 || !ctype_alpha($nombre)){
    header('Location:index.php');
}

/*Comprobamos que el alias no tenga mas de 30 caracteres y no tenga espacios*/
if($alias != null){
    if(strlen($alias) > 30 || strpos($alias, ' ')){
        header('Location:index.php');
    }
}

/*Comprobamos que la clave tenga al menos 8 caracteres, no pase los 40, sin espacios y que contenga numeros y letras*/
if(strlen($clave) < 8 || strlen($clave) > 40|| ctype_digit($clave) || ctype_alpha($clave) || strpos($clave, ' ')){
    header('Location:index.php');
}

if($claveRep != $clave){
    header('Location:index.php');
}

/*Si son correctos añadimos a la base de datos dicho usuario, sin ser admin (por defecto 0) ni estar activado (por defecto 0),
con la fecha  actual del servidor (por defecto la actual) y la clave encriptada.*/

//$sql = 'insert into usuario values(null, :nombre, :precio, :observaciones)';
$sql = 'insert into usuario values(null, :correo, :alias, :nombre , :clave, 0, 0, CURRENT_TIMESTAMP)';

$db = new Database();
$resultado = 0;
if($db->connect()) {
    $conexion = $db->getConnection();
    $sentencia = $conexion->prepare($sql);
    $sentencia->bindValue('correo', $email);
    $sentencia->bindValue('alias', $alias);
    $sentencia->bindValue('nombre', $nombre);
    $sentencia->bindValue('clave', Util::encriptar($clave));
    if($sentencia->execute()) {
        $resultado = $conexion->lastInsertId();
    } 
}
$db->close();

/*Se le envia un correo de activacion, para ello necesitamos un usuario*/
if($resultado > 0) {
    $usuario = new Usuario();
    $usuario->setId($resultado);
    $usuario->setAlias($alias);
    $usuario->setNombre($nombre);
    $usuario->setCorreo($email);
    $usuario->setClave(Util::encriptar($clave));
    $resultado2 = Mail::sendActivation($usuario);
}

header('Location:index.php');