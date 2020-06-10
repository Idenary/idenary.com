<?php 


require __DIR__ . '/vendor/autoload.php';

require "inc/config.inc.php";
require 'inc/idena.class.php';


$idena = new IdenaAuth($CONFIG);
$token = $idena->get_token();
$url = $idena->get_dna_url($token);
header('Location: '.$url);
# print("<a href='$url'>Sign in with Idena</a>");
die();
