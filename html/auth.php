<?php 


require __DIR__ . '/vendor/autoload.php';

require "inc/config.inc.php";
require 'inc/idena.class.php';

$body = file_get_contents('php://input');

$idena = new IdenaAuth($CONFIG);
$response = $idena->get_authentication_response($body, true);
//error_log("auth response");
//error_log($response);
print($response);
    