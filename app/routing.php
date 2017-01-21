<?php
//***************************************
// Montage des contrôleurs sur le routeur
$app->mount("/", new App\controller\IndexController($app));
$app->mount("/activite", new App\controller\ActiviteController($app));
$app->mount("/lieu", new App\controller\LieuController($app));
?>