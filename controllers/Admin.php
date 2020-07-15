<?php

    namespace Controllers;

    use Models\Article;
    use Models\Comment;
    use Models\Message;

    class Admin extends Controller
    {
        /**
         * @var string $modelName nom de la classe article
         */
        protected $modelName = Article::class;

        /**
         * Affiche la page d'administration
         * 
         * @param void
         * @return void
         */
        public function showBoard(): void
        {
            if(!\Session::isLogged() || \Session::get('role') != 'admin') {
                \FlashMessage::setMessage('danger', 'Vous n’avez pas les droits pour accéder à cette page, veuillez vous connecter et réessayer.');
                \Http::redirectTo('home');
            }

            $articles = $this->model->selectAll();

            $postManager = new Comment();
            $comments = $postManager->selectAllComments(['article', 'created_at']);

            $messageManager = new Message();
            $messages = $messageManager->selectAll(['created_at DESC', 'lastname']);

            \Renderer::render('admin/adminBoard', [
                'articles' => $articles,
                'comments' => $comments,
                // 'categoriesTable'    => $categoriesTables,
                'messages'     => $messages,
                'categories'   => $this->categories,
                'lastArticles' => $this->lastArticles
            ]);
        }
    }

?>