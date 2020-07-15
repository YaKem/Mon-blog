<?php

    class Database
    {
        /**
         * @var object instance de PDO, initialisée à null
         */
        private $instance = null;
        
        /**
         * Retourne une connexion à la base de données
         * 
         * @return PDO
         */
        public function getPdo(): PDO
        {
            if($this->instance === null)
            {
                $this->instance = new PDO('mysql:host=localhost;dbname=my-blog;charset=utf8', 'root', '', [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            }
            return $this->instance;
        }
    }

?>