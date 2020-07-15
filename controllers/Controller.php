<?php

    namespace Controllers;

    use Models\Article;
    use Models\Category;

    abstract class Controller
    {
        protected $model;
        protected $categoryManager;
        protected $articleManager;

        protected $lastArticles;
        protected $categories;

        protected $filter;

        /**
         * Au moment de l'instanciation des class filles, le modèle correspondant est instancié, ainsi que les classes Article, Catégorie, Filter pour éviter de le faire de manière répétée car nécessaires pour l'affichage du layout.
         */
        public function __construct()
        {
            $this->model = new $this->modelName();
            
            $this->articleManager = new Article();
            $this->lastArticles = $this->articleManager->lastArticles();

            $this->categoryManager = new Category();
            $this->categories = $this->categoryManager->selectAll(['category_name']);

            $this->filter = new \Filter();
        }
    }

?>