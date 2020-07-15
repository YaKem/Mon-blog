<?php

    namespace Controllers;

    use Models\Article;

    class About extends Controller
    {
        /**
         * @var string $modelName nom de la classe article
         */
        protected $modelName = Article::class;

        /**
         * Affiche la page About
         * 
         * @param void
         * @return void
         */
        public function showAbout(): void
        {
            \Renderer::render('blog/about', [
                'categories'   =>$this->categories,
                'lastArticles' => $this->lastArticles
            ]);
        }
    }

?>