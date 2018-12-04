<?php

session_start();
require_once '../classes/vendor/autoload.php';
$cliente = new Google_Client();
$cliente->setApplicationName('DWES');
$cliente->setClientId('541578754925-qrgl49jchrgea84tkgpfk9mnetbp4l6v.apps.googleusercontent.com');
$cliente->setClientSecret('KVtm7NvGA26KurfXO5kgWDbG');
$cliente->setRedirectUri('https://dwese-alexdls0.c9users.io/proyecto/gmail/obtenercredenciales.php');
$cliente->setScopes('https://www.googleapis.com/auth/gmail.compose');
$cliente->setAccessType('offline');
if (!$cliente->getAccessToken()) {
    $auth = $cliente->createAuthUrl();
    header("Location: $auth");
}