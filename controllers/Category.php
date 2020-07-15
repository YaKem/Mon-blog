<?php

    namespace Controllers;

    use Models\Category as categoryModel;

    class Category extends Controller
    {
        /**
         * @var string $modelName nom de la classe categoryModel
         */
        protected $modelName = categoryModel::class;

        /**
         * Affichage du formulaire des catégories
         *
         * @param void
         * @return void
         */
        public function showForm(): void
        {
            // On vérifie si on est logué et si on a les droits administrateur
            if(!\Session::isLogged() || \Session::get('role') != 'admin') {
                \FlashMessage::setMessage('danger', 'Vous n’avez pas les droits pour accéder à cette page, veuillez vous connecter et réessayer.');
                \Http::redirectTo('home');
            }
            
            // On génère un token avec pour clef 'catégorie' contre faille csrf
            $csrfToken = \Csrf::generateToken('category');

            // On créé la vue du formulaire des catégories, on lui passe dans un tableau tous les paramètres nécessaires.
            \Renderer::render('blog/form-category', [
                'categories'   => $this->categories,
                'lastArticles' => $this->lastArticles,
                'csrfToken'    => $csrfToken
                ]);
            }
        
        public function showCategoryAjax()
        {
            // On vérifie si on est logué et si on a les droits administrateur
            if(!\Session::isLogged() || \Session::get('role') != 'admin') {
                \FlashMessage::setMessage('danger', 'Vous n’avez pas les droits pour accéder à cette page, veuillez vous connecter et réessayer.');
                \Http::redirectTo('home');
            }
        
            // On récupère toutes les catégories
            $categories = $this->categories;
    
            // On inclut le tableau des catégories
            include 'views/admin/table-category.phtml';
        }

        /**
         * Ajout d'une nouvelle catégorie
         *
         * @param array $params
         * @return void
         */
        public function addCategory(array $params): void
        {
            // On vérifie si on vient bien du formulaire de création d'une catégorie
            if(!\Csrf::checkToken(500, HOST . '/index.php?r=form_category', 'category')) {
                \FlashMessage::setMessage('danger', 'Vous êtes un robot !');
                \Http::redirectTo('home');
            }

            // On vérifie si on est logué et si on a les droits administrateur
            if(!\Session::isLogged() || \Session::get('role') != 'admin') {
                \FlashMessage::setMessage('danger', 'Vous n’avez pas les droits pour accéder à cette page, veuillez vous connecter et réessayer.');
                \Http::redirectTo('home');
            }

            // Pause de 1s, sécurité contre faille force brut
            sleep(1);
            
            // Si champs catégorie vide alors on affiche un message flash et on redirige vers le tableau d'administration
            if(empty($_POST['name'])) {

                \FlashMessage::setMessage('warning', 'Veuillez saisir un nom de catégorie');
                \Http::redirectTo('show_admin_board');

            }

            // On créé la catégorie
            $this->model->insertCategory($params);

            // On confirme via message flash la création de la catégorie
            \FlashMessage::setMessage('success', 'Catégorie ajoutée!');
   
            // On redirige vers la panneau d'administration
            \Http::redirectTo('show_admin_board');
        }

        /**
         * Suppression d'une catégorie
         *
         * @param array $params
         * @return void
         */
        public function delCategory(array $params): void
        {
            // On vérifie si on est logué et si on a les droits administrateur
            if(!\Session::isLogged() || \Session::get('role') != 'admin') {
                \FlashMessage::setMessage('danger', 'Vous n’avez pas les droits pour accéder à cette page, veuillez vous connecter et réessayer.');
                \Http::redirectTo('home');
            }

            // On supprime la catégorie
            $this->model->deleteCategory($params);

            // On affiche un message flash pour confirmer la suppression
            \FlashMessage::setMessage('warning', 'Catégorie supprimée!');

            // Redirection vers le panneau d'adminsitration
            \Http::redirectTo('show_admin_board');
        }
    }

?>
