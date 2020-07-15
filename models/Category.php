<?php

    namespace Models;

    class Category extends Model
    {
        
        /**
         * @var string $table nom de la table
         */
        protected $table = 'categories';

        /**
         * Créé une catégorie
         *
         * @param array $params
         * @return void
         */
        public function insertCategory(array $params): void
        {
            extract($params);

            $sql = "INSERT INTO {$this->table} (category_name) VALUES (?)";
            $query = $this->pdo->prepare($sql);
            $query->execute([$name]);
        }
        
        /**
         * Supprime la catégorie d'identifiant id
         *
         * @param array $params
         * @return void
         */
        public function deleteCategory(array $params): void
        {
            extract($params);

            $sql = "DELETE FROM {$this->table} WHERE id = ?";
            $query = $this->pdo->prepare($sql);
            $query->execute([$id]);
        }
    }

?>