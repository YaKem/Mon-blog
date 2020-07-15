<?php

    // 
    spl_autoload_register(function($class)
    {	
        if(str_word_count($class) > 1) {
            $class = str_replace('\\', '/', $class);
            $class = lcfirst($class);
        }

        if(file_exists(ROOT . '/' . $class . '.php')) {
            require_once(ROOT . '/' . $class .'.php');
        } else if(file_exists(CLASSES . '/' . $class . '.php')) {
            require_once(CLASSES . '/' .$class .'.php');
        }                
    });

?>