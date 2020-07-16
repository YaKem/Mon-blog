<?php

    class Csrf
    {
        /**
         * Génère un token et le stocke ainsi que l'heure courante (UNIX) dans des variables de session
         *
         * @param string $name
         * @return string $token
         */
        static public function generateToken(string $name): string
        {
            $token = uniqid(rand(), true);
            $_SESSION[$name . '_token'] = $token;
            $_SESSION[$name . '_token_time'] = time();

            return $token;           
        }

        /**
         * Vérifie si le token stocké dans la variable de session est le même que celui de l'input hidden du formulaire avant le délai $duration et l'URL de la page qui a conduit à celle courante.
         *
         * @param int $duration
         * @param string $referer
         * @param string $name
         * @return bool
         */
        static public function checkToken(int $duration, string $referer, string $name): bool
        {
            if(isset($_SESSION[$name . '_token']) && isset($_SESSION[$name . '_token_time']) && isset($_POST['csrf-token'])) {
                if($_SESSION[$name . '_token'] == $_POST['csrf-token']) {
                    if(strstr($_SERVER['HTTP_REFERER'], 'id')) {
                        $http_referer = substr($_SERVER['HTTP_REFERER'], 0, strrpos($_SERVER['HTTP_REFERER'], 'id')-1);
                    } else {
                        $http_referer = $_SERVER['HTTP_REFERER'];
                    }
                    if($http_referer == $referer) {
                        if($_SESSION[$name . '_token_time'] - time() < $duration) {
                            return true;
                        }
                    }
                }
            }
            return false;
        }
    }

?>