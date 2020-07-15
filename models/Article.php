<?php

    namespace Models;

    class Article extends Model
    {
        protected $table = 'articles';

        /**
         * Récupère tous les articles
         *
         * @param array $order || void
         * @return array $results
         */
        public function selectAll(array $order = []): array
        {
            $sql = "SELECT
                    articles.id AS id, title, content, image, author, category_id, category_name, created_at
                FROM
                    {$this->table}
                INNER JOIN categories
                    ON articles.category_id = categories.id";

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
         * Recherche dans les articles (titre + contenu) le mot $keyword
         * @param array $params
         * @return array
         */
        public function searchInArticles(string $keyword): array
        {
            $keyword = strtolower($keyword);
            $ucfKeyword = ucfirst($keyword);
            
            $sql = "SELECT id, title, content FROM articles WHERE CONCAT(title, ' ', content) LIKE '% $keyword %' AND CONCAT(title, ' ', content) LIKE '% $ucfKeyword %'";
            $query = $this->pdo->prepare($sql);
            $query->execute();
            $results = $query->fetchAll(\PDO::FETCH_ASSOC);

            $params = compact('results', 'keyword');

            return $params;
        }

        /**
         * Retourne la liste des commentaires d'un article $id du plus récent au plus ancien
         * @param array $params
         * @return array
         */
        public function findCategory(array $params): array
        {
            extract($params);

            $sql = "SELECT
                        title, content, author, categories.category_name AS category, created_at, articles.id AS article_id
                    FROM
                        articles
                    INNER JOIN categories
                        ON articles.category_id = categories.id
                    WHERE category_id = ?
                    ORDER BY created_at DESC";

            $query = $this->pdo->prepare($sql);
            $query->execute([$id]);
            $results = $query->fetchAll(\PDO::FETCH_ASSOC);     

            return $results;
        }

        /**
         * Retourne les 3 derniers articles
         * @return array
         */
        public function lastArticles(): array
        {
            $sql = "SELECT id, title, image, content FROM articles ORDER BY created_at DESC LIMIT 3";

            $query = $this->pdo->prepare($sql);
            $query->execute();
            $results = $query->fetchAll(\PDO::FETCH_ASSOC);

            return $results;
        }

                /**
         * Insère un item
         * @param array $params
         * @return void
         */
        public function insertArticle(array $params): void
        {
            extract($params);

            $sql = "INSERT INTO {$this->table} (title, content, image, author, category_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
            $query = $this->pdo->prepare($sql);
            $query->execute([$title, $content, $image, $author, $category]);
        }
    
        /**
         * Met à jour un item avec ou sans nouveau fichier
         * @param array $params
         * @return void
         */
        public function updateArticle(array $params, string $mode = null): void
        {
            extract($params);

            $sql = "UPDATE {$this->table} SET title = ?, content = ?, ";
            $variables = [$title, $content];

            if($mode != 'no-file') {
                $sql .= "image = ?, ";
                $variables[] = $image;
            }

            $sql .= "author = ?, category_id = ? WHERE id = ?";
            $table = array_merge($variables, [$author, $category, intval($id)]);

            $query = $this->pdo->prepare($sql);
            $query->execute($table);
        }

        /**
         * Récupère le nom du fichier de l'article d'identifiant $id
         *
         * @param integer $id
         * @return string $res['image']
         */
        public function getNameFile(int $id): string
        {
            $sql = "SELECT image FROM {$this->table} WHERE id = ?";
            $query = $this->pdo->prepare($sql);
            $query->execute([$id]);
            $res = $query->fetch(\PDO::FETCH_ASSOC);

            return $res['image'];
        }

        /**
         * Récupère tous les articles relatés à un article d'identifiant $id
         *
         * @param [type] $article_id
         * @return void
         */
        public function getRelatedArticles($id)
        {
            $sql = "SELECT id, image
                    FROM {$this->table}
                    WHERE category_id = (SELECT category_id FROM {$this->table} WHERE id = ?) AND id != ?";
            $query = $this->pdo->prepare($sql);
            $query->execute([$id, $id]);
            $res = $query->fetchAll(\PDO::FETCH_ASSOC);

            return $res;
        }
    }

?>