<?php
$path = '/var/www';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'SimpleApi/Test.php';
$api = new SimpleApi_Test;
$api->handle();