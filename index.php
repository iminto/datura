<?php
define ('_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);// 网站根目录
define ('_DTR_PATH', _ROOT . 'Datura' . DIRECTORY_SEPARATOR);// 系统目录
require _DTR_PATH.'Datura.php';
$app=new Datura;
$app->run();
