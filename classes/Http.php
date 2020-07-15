<?php

    class Http
    {
        /**
         * Redirige vers url
         * 
         * @param string $url
         * @return void
         */
        public static function redirectTo(string $path_name)
        {
            header("Location: index.php?r=$path_name");
            exit();
        }

        /**
         * Récupère en POST valeur variable
         * 
         * @param string $field
         * @return int||string
         */
        public static function getPost(string $field)
        {
            return $_POST[$field];
        }
    }

?>