<?php

require_once("classes/vendor/autoload.php");
require 'classes/autoload.php';

use izv\tools\Session;

/*destruye las sesiones y redirige a index.php*/
session_start();
unset($_SESSION['email']);
unset($_SESSION['password']);
session_destroy();

header('Location:index.php');