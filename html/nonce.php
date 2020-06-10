<?php 


require __DIR__ . '/vendor/autoload.php';

require "inc/config.inc.php";
require 'inc/idena.class.php';

$body = file_get_contents('php://input');

$idena = new IdenaAuth($CONFIG);
$nonce = $idena->get_nonce_response($body, true);
//error_log($nonce);
print($nonce);
