<?php
    session_start();
    
    require_once('autoload.php');
    
    // Défintion des constantes
    $root = $_SERVER['DOCUMENT_ROOT'];
    $host = $_SERVER['HTTP_HOST'];

    define('ROOT', $root . '/my-blog');
    define('HOST', 'http://' . $host . '/my-blog');
    
    define('CLASSES', ROOT . '/classes');
    define('CONTROLLERS', ROOT . '/controllers');
    define('MODELS', ROOT . '/models');
    
    define('VIEWS', ROOT . '/views');
    
    define('ASSETS', HOST . '/assets');
    
    define('RECAPTCHA_SITE_KEY', '6Le2FP8UAAAAAI4q2YfMF0vgiXJz3m0bY8dXLnF1');
    define('RECAPTCHA_SECRET_KEY', '6Le2FP8UAAAAAK4EZg7yehGkm7B5eAf8GAS41N2J');   

    $router = new Router();
    $router->renderController();