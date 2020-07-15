<?php

    namespace Models;

    abstract class Model
    {
        /**
         * @var object $pdo instance de la classe PDO
         */
        protected $pdo;

        /**
         * @var string $table nom de la table
         */
        protected $table;

        /**
         * Au moment de l'instanciation des class filles, connection à la bdd
         */
        public function __construct()
        {
            $database = new \Database();
            $this->pdo = $database->getPdo();
        }

        /**
         * Retourne tous les items dans l'ordre croissant des champs inclus dans le tableau $order s'il existe
         * 
         * @param array $order
         * @return array $results
         */
        public function selectAll(array $order = []): array
        {
            $sql = "SELECT * FROM {$this->table}";

            if($order) {
                $sql .= " ORDER BY " . $order[0];
                for($i = 1; $i < count($order); $i++) {
                    $sql .= " ," . $order[$i];
                }
            }
       
            $query = $this->pdo->prepare($sql);
            $query->execute();
            $results = $query->fetchAll();
            return $results;
        }

        /**
         * Retourne l'item id
         * 
         * @param int $id identifiant de l'item
         * @return array $result champs de l'item
         */
        public function selectOne(int $id): array
        {            
            $sql = "SELECT * FROM {$this->table} WHERE id = ?";
            $query = $this->pdo->prepare($sql);
            $query->execute([$id]);
            $result = $query->fetch();
            return $result;
        }        
    
        /**
         * Supprime un item d'identifiant id
         * 
         * @param int $id identifiant de l'item
         * @return void
         */
        public function delete(int $id): void
        {
            $sql = "DELETE FROM {$this->table} WHERE id = ?";
            $query = $this->pdo->prepare($sql);
            $query->execute([$id]);
        }
    }

?>