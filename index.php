<?php 
    require_once 'runtime.php';
$useTime = new runtime();
$useTime->start();

session_start();
//网站入口文件
require_once 'lib/app.php';
$app = new app();
$app->run();

$useTime->stop();
$useTime->spent();
?>