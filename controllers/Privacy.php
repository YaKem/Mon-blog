<?php

    namespace Controllers;

    use Models\Article;

    class Privacy extends Controller
    {
        /**
         * @var string $modelName nom de la classe article
         */
        protected $modelName = Article::class;

        /**
         * Affiche la page Privacy
         * 
         * @param void
         * @return void
         */
        public function showPrivacy(): void
        {
            \Renderer::render('blog/privacy', [
                'categories'   =>$this->categories,
                'lastArticles' => $this->lastArticles
            ]);
        }
    }

?>