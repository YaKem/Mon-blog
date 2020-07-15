<?php

    namespace Models;

    class Newsletter extends Model
    {
        /**
         * @var string $table nom de la table
         */
        protected $table = 'newsletter';

        /**
         * Créé un email
         *
         * @param string $email
         * @return void
         */
        public function insertEmail($email): void
        {
            $sql = "INSERT INTO {$this->table} (email, created_at, is_active) VALUES (?, NOW(), 1)";
            $query = $this->pdo->prepare($sql);
            $query->execute([$email]);
        }
    }

?>