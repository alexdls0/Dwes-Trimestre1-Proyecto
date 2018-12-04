<?php
require_once("../classes/vendor/autoload.php");
require '../classes/autoload.php';

use izv\tools\Session;
use izv\database\Database;
use izv\tools\Util;
use izv\data\Usuario;
use izv\managedata\ManageUsuario;
use izv\tools\Mail;

session_start();

/*Esta pagina se encarga de ir cambiando los datos que son correctos en la base de datos*/
/*Si se pasa un nombre, se comprueba*/
$nombrechecked = null;
if($_POST['nombre'] !=null){
    if(strlen($_POST['nombre']) <= 50 && ctype_alpha($_POST['nombre'])){
        $nombrechecked = $_POST['nombre'];
    }
}

/*Si el nombre es correcto se procede a su modificacion en la bd*/
if($nombrechecked != null){
    $sql = 'update usuario set nombre = :nombre where correo = :correo AND clave=:clave';
    $db = new Database();
    if($db->connect()) {
        $conexion = $db->getConnection();
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindValue('nombre', $nombrechecked);
        $sentencia->bindValue('correo', $_SESSION['email']);
        $sentencia->bindValue('clave', $_SESSION['password']);
        $sentencia->execute();
    }
    $db->close();
}

/*Si se pasa un alias, se comprueba*/
$aliaschecked = null;
if($_POST['alias'] != null){
    if(strlen($_POST['alias']) <= 30 && !strpos($_POST['alias'], ' ')){
        $aliaschecked = $_POST['alias'];
    }
}

/*Si el alias es correcto se procede a su modificacion en la bd*/
if($aliaschecked != null){
    $sql = 'update usuario set alias = :alias where correo = :correo AND clave=:clave';
    $db = new Database();
    if($db->connect()) {
        $conexion = $db->getConnection();
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindValue('alias', $aliaschecked);
        $sentencia->bindValue('correo', $_SESSION['email']);
        $sentencia->bindValue('clave', $_SESSION['password']);
        $sentencia->execute();
    }
    $db->close();
}

/*Si se escribe la contraseña se debe validar tanto la clave como la nueva clave*/
$clavevieja = $_SESSION['password'];
$clavechecked = null;
if($_POST['clave'] !=null && $_POST['claveRep'] !=null && $_POST['claveRep2'] !=null){
    //Compruebo que la clave introducida sea la correcta
    if(Util::verificarClave($_POST['clave'], $_SESSION['password'])){
        //Compruebo que ambas claves repetidas coinciden
        if($_POST['claveRep'] === $_POST['claveRep2']){
            //Compruebo que cumplen unos requisitos
            if(strlen($_POST['claveRep']) > 8 && strlen($_POST['claveRep']) < 40 && !ctype_digit($_POST['claveRep']) 
            && !ctype_alpha($_POST['claveRep']) && !strpos($_POST['claveRep'], ' ')){
                $clavechecked = $_POST['claveRep'];
                $clavevieja = Util::encriptar($clavechecked);
            }
        }    
    }
}

/*Si la clave es correcta se procede a su modificacion en la bd, pero primeramente se debe encriptar*/
if($clavechecked != null){
    $sql = 'update usuario set clave = :clavenueva where correo = :correo AND clave = :clave';
    $db = new Database();
    if($db->connect()) {
        $conexion = $db->getConnection();
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindValue('clavenueva', $clavevieja);
        $sentencia->bindValue('correo', $_SESSION['email']);
        $sentencia->bindValue('clave', $_SESSION['password']);
        $sentencia->execute();
    }
    $db->close();
}
/*El correo ya se valida en el html, aqui simplemente se cambia el estado de activo a 0 y se envia
un correo de activacion solo si se introduce un correo nuevo*/
$correoviejo = $_SESSION['email'];
$resultado=0;
if($_POST['email'] !=null){
    $correoviejo = $_POST['email'];
    $sql = 'update usuario set correo = :correonuevo, activo = 0 where correo = :correo AND clave = :clave';
    $db = new Database();
    if($db->connect()) {
        $conexion = $db->getConnection();
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindValue('correonuevo', $_POST['email']);
        $sentencia->bindValue('correo', $_SESSION['email']);
        $sentencia->bindValue('clave', $clavevieja);
        //$sentencia->execute();
        if($sentencia->execute()){
            $resultado = 1;    
        }
    }
    
    $db->close();
    //Obtengo el usuario que tiene ese correo y esa clave
    $db = new Database();
    $manager = new ManageUsuario($db);
    $usuarios = $manager->getAll();
    $db->close();
    $user = null;
 
    for($i = 0 ; $i < count($usuarios); $i++){
        if($usuarios[$i]->getCorreo() == $_POST['email'] && $usuarios[$i]->getClave() == $clavevieja){
            $user = $usuarios[$i];
        }
    }
    //Envio un correo de activacion usando ese usuario
    $resultado2=false;
    if($resultado > 0 && $user!=null){
        $resultado2 = Mail::sendActivation($user);
    }
}

if($_POST['vaciaralias'] != null){
    $sql = 'update usuario set alias = NULL where correo = :correo AND clave=:clave';
    $db = new Database();
    if($db->connect()) {
        $conexion = $db->getConnection();
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindValue('correo', $correoviejo);
        $sentencia->bindValue('clave', $clavevieja);
        $sentencia->execute();
    }
    $db->close();
}


/*Borro la sesion y vuelvo a index*/
unset($_SESSION['email']);
unset($_SESSION['password']);
session_destroy();

header('Location:../index.php');