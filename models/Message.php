<?php

    namespace Models;

    class Message extends Model
    {
        /**
         * @var string $table nom de la table
         */
        protected $table = 'messages';

        /**
         * Créé un message
         * 
         * @param array $params
         * @return void
         */
        public function insert($params): void
        {
            extract($params);

            $sql = "INSERT INTO {$this->table} (firstName, lastName, email, content, created_at) VALUES (?, ?, ?, ?, NOW())";
            $query = $this->pdo->prepare($sql);
            $query->execute([$firstname, $lastname, $email, $content]);
        }
    }

?>