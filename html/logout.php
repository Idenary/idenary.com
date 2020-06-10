<?php 


require __DIR__ . '/vendor/autoload.php';

require "inc/config.inc.php";
require 'inc/idena.class.php';


$idena = new IdenaAuth($CONFIG);
$idena->logout(); // Will redirect
