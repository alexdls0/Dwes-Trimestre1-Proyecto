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
    $sql = 'update usuario set nombre = :nombre where id = :id';
    $db = new Database();
    if($db->connect()) {
        $conexion = $db->getConnection();
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindValue('nombre', $nombrechecked);
        $sentencia->bindValue('id', $_SESSION['id']);
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
    $sql = 'update usuario set alias = :alias where id = :id';
    $db = new Database();
    if($db->connect()) {
        $conexion = $db->getConnection();
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindValue('alias', $aliaschecked);
        $sentencia->bindValue('id', $_SESSION['id']);
        $sentencia->execute();
    }
    $db->close();
}

if(isset($_POST['vaciaralias'])){
    $sql = 'update usuario set alias = NULL where id = :id';
    $db = new Database();
    if($db->connect()) {
        $conexion = $db->getConnection();
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindValue('id', $_SESSION['id']);
        $sentencia->execute();
    }
    $db->close();
}

/*Si se escribe la contraseña se debe validar la nueva clave*/
$clavechecked = null;
if($_POST['claveRep'] !=null && $_POST['claveRep2'] !=null){
    //Compruebo que ambas claves repetidas coinciden
    if($_POST['claveRep'] === $_POST['claveRep2']){
        //Compruebo que cumplen unos requisitos
        if(strlen($_POST['claveRep']) > 8 && strlen($_POST['claveRep']) < 40 && !ctype_digit($_POST['claveRep']) 
        && !ctype_alpha($_POST['claveRep']) && !strpos($_POST['claveRep'], ' ')){
            $clavechecked = $_POST['claveRep'];
        }
    }    
}

/*Si la clave es correcta se procede a su modificacion en la bd, pero primeramente se debe encriptar*/
if($clavechecked != null){
    $sql = 'update usuario set clave = :clavenueva where id = :id';
    $db = new Database();
    if($db->connect()) {
        $conexion = $db->getConnection();
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindValue('clavenueva', Util::encriptar($clavechecked));
        $sentencia->bindValue('id', $_SESSION['id']);
        $sentencia->execute();
    }
    $db->close();
}


/*Si se selecciona que no sea admin, admin = 0*/
if($_POST['admin'] == 'noadmin'){
    $sql = 'update usuario set admin = 0 where id = :id';
    $db = new Database();
    if($db->connect()) {
        $conexion = $db->getConnection();
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindValue('id', $_SESSION['id']);
        $sentencia->execute();
    }
    $db->close();
}

/*Si se selecciona que sea admin, admin = 1*/
if($_POST['admin'] == 'admin'){
    $sql = 'update usuario set admin = 1 where id = :id';
    $db = new Database();
    if($db->connect()) {
        $conexion = $db->getConnection();
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindValue('id', $_SESSION['id']);
        $sentencia->execute();
    }
    $db->close();
}


/*El correo ya se valida en el html. Simplente cambio el correo y si está o no activado*/
if(!isset($_POST['activar'])){
    $resultado=0;
    if(isset($_POST['email']) && $_POST['email'] != null){
        $sql = 'update usuario set correo = :correo, activo = 0 where id = :id';
        $db = new Database();
        if($db->connect()) {
            $conexion = $db->getConnection();
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue('correo', $_POST['email']);
            $sentencia->bindValue('id', $_SESSION['id']);
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
            if($usuarios[$i]->getId() == $_SESSION['id']){
                $user = $usuarios[$i];
            }
        }
        //Envio un correo de activacion usando ese usuario
        $resultado2=false;
        if($resultado > 0 && $user!=null){
            $resultado2 = Mail::sendActivation($user);
        }
    }
}else{
    if(isset($_POST['email']) && $_POST['email'] != null){
        $sql = 'update usuario set correo = :correo where id = :id';
        $db = new Database();
        if($db->connect()) {
            $conexion = $db->getConnection();
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue('correo', $_POST['email']);
            $sentencia->bindValue('id', $_SESSION['id']);
            $sentencia->execute();
        }
        $db->close();   
    }
}


/*Desactivar o no*/
if(isset($_POST['activar'])){
    $sql = 'update usuario set activo = 1 where id = :id';
    $db = new Database();
    if($db->connect()) {
        $conexion = $db->getConnection();
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindValue('id', $_SESSION['id']);
        $sentencia->execute();
    }
    $db->close();    
}else{
    $sql = 'update usuario set activo = 0 where id = :id';
    $db = new Database();
    if($db->connect()) {
        $conexion = $db->getConnection();
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindValue('id', $_SESSION['id']);
        $sentencia->execute();
    }
    $db->close();
}

/*Vuelvo a la lista de usuarios*/
header('Location:users.php');