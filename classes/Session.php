<?php

    class Session
    {
        /**
         * Démarre une session si pas de session ouverte
         *
         * @return void
         */
        public static function init(): void
        {
            if(session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        }

        /**
         * Créé une variable de session
         *
         * @param string $key
         * @param string||int $val
         * @return void
         */
        public static function set(string $key, $val): void
        {
            $_SESSION[$key] = $val;
        }

        /**
         * Récupère la valeur d'une variable de session d'indice $key, si la variable n'existe pas retourne false
         *
         * @param string $key
         * @return int||string||boolean
         */
        public static function get(string $key)
        {
            if(isset($_SESSION[$key])) {
                return $_SESSION[$key];
            } else {
                return false;
            }
        }

        /**
         * Détruit une variable de session de clef $key
         *
         * @param [type] $key
         * @return void
         */
        public static function unset($key): void
        {
            if(isset($_SESSION[$key])) {
                unset($_SESSION[$key]);
            }
        }

        /**
         * Détruit une session
         *
         * @return void
         */
        public static function destroy(): void
        {
            session_destroy();
            \Http::redirectTo('home');
        }

        /**
         * Si utilisateur logué retourne true, sinon false
         *
         * @return bool
         */
        public static function isLogged(): bool
        {
            if(self::get("login") == true) {
                return true;
            } else {
                return false;
            }
        }
    }

?>