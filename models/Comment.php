<?php

    namespace Models;

    class Comment extends Model
    {
        protected $table = 'comments';

        /**
         * Insère un commentaire
         * @param array $params
         * @return void
         */
        public function insert(array $params): void
        {
            extract($params);

            $parent_id = intval($parent_id);
            $article_id = intval($article_id);
            
            $sql = "INSERT INTO {$this->table} (comment_parent_id, name, email, content, article_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
            $query = $this->pdo->prepare($sql);
            $query->execute([$parent_id, $name, $mail, $content, $article_id]);
        }

        /**
         * Retourne tous les commentaires d'un article donné par ordre antéchronologique ou leurs réponses
         * @param int $article_id
         * @param string $order
         * @param int $comment_parent_id
         * @return array
         */
        public function selectAllForArticle(int $article_id, string $order, int $comment_parent_id = 0): array
        {
            $sql = "SELECT id, name, content, created_at, comment_parent_id FROM {$this->table} WHERE article_id = ? AND comment_parent_id = ?";

            if($order) {
                $sql .= " ORDER BY created_at ".$order;
            }

            $query = $this->pdo->prepare($sql);
            $query->execute([$article_id, $comment_parent_id]);
            $results = $query->fetchAll();

            return $results;
        }

        /**
         * Donne le nombre de commentaire ajouté à un article d'identifiant $id
         *
         * @param integer $id
         * @return integer
         */
        public function getCountComments(int $id): int
        {
            $sql = "SELECT id FROM {$this->table} WHERE article_id = ?";

            $query = $this->pdo->prepare($sql);
            $query->execute([$id]);
            $count = $query->rowCount();

            return $count;
        }

        /**
         * Retourne tous les articles par ordre antéchronologique
         * @param array $order
         * @return array
         */
        public function selectAllComments(array $order = []): array
        {
            $sql = "SELECT comments.id as id,comments.name as name, comments.content as content, articles.title as article, comments.created_at as created_at, email FROM {$this->table} INNER JOIN articles ON comments.article_id = articles.id";

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
         * Supprime tous les commentaires d'un article d'identifiant $id
         *
         * @param int $id
         * @return void
         */
        public function deleteFromArticle($id): void
        {
            $sql = "DELETE FROM {$this->table} WHERE article_id = ?";

            $query = $this->pdo->prepare($sql);
            $query->execute([$id]);
        }
    }
    
?>