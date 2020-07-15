<?php

    namespace Controllers;

    use Models\Home as homeModel;

    class Home extends Controller
    {
        /**
         * @var string $modelName Nom de la classe homeModel
         */
        protected $modelName = homeModel::class;

        /**
         * Afficher les Articles du plus récent au plus ancien
         * 
         * @param void
         * @return void
         */
        public function showHome(): void
        {
            // On récupère tous les articles
            $articles = $this->model->selectAll();

            // On créé la vue des articles, on lui passe dans un tableau tous les paramètres nécessaires.
            \Renderer::render('blog/home', [
                'articles'     => $articles,
                'categories'   => $this->categories,
                'lastArticles' => $this->lastArticles
            ]);
        }
    }

?>