<?php
use izv\app\App;
use izv\data\Usuario;
use izv\database\Database;
use izv\managedata\ManageUsuario;
use izv\tools\Reader;
use izv\tools\Util;
use izv\tools\Session;

require '../classes/autoload.php';
require '../classes/vendor/autoload.php';

/*Si intentas registrar un usuario y existe una sesion te devuelvo a index*/
session_start();
if(isset($_SESSION['email']) || isset($_SESSION['password'])){
    header('Location:index.php');
}

/*Obtengo el id y el correo codificado del enlace de activacion*/
$id = Reader::read('id');
$code = Reader::read('code');

/*Decodifico el correo*/
$sendedMail = \Firebase\JWT\JWT::decode($code, App::JWT_KEY, array('HS256'));

/*Obtengo el usuario con los datos de ese id obtenifo*/
$db = new Database();
$manager = new ManageUsuario($db);
$user = $manager->get($id);

/*Si el id existe y coinciden los correos activo a ese usuario*/

$resultado = 0;
if($user !== null && $user->getCorreo() === $sendedMail) {
    $sql = 'update usuario set activo = 1 where id = :id';
    $db = new Database();
    if($db->connect()) {
        $conexion = $db->getConnection();
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindValue('id', $user->getId());
        if($sentencia->execute()) {
            $resultado = $sentencia->rowCount();
        }
        $db->close();
    }
}
header('Location:../index.php');