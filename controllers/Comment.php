<?php

    namespace Controllers;

    use Models\Comment as commentModel;

    class Comment extends Controller
    {
        /**
         * @var string $modelName Nom de la classe commentModel
         */
        protected $modelName = commentModel::class;

        /**
         * Affiche tous les commentaires par order antéchronologique
         * @param array $params
         * @return void
         */
        public function showComment(array $params): void
        {
            // On créé un tableau vide qui stockera les commentaires ordonnés
            $data = [];

            extract($params);

            // On récupère tous les commentaires de l'article, rangé du plus récent au plus ancien
            $comments_desc = $this->model->selectAllForArticle($id_article, 'DESC');
            
            // On stocke les commentaires par ordre antéchronologique et leur réponse par ordre chronologique dans le même tableau 
            foreach($comments_desc as $item_desc) {
                
                $data[] = $item_desc;

                $comments_asc = $this->model->selectAllForArticle($id_article, 'ASC', $item_desc['id']);

                if(!empty($comments_asc)) {
                    $data = array_merge($data, $comments_asc);
                }

            }

            // On dénombre les commentaires
            $count = $this->model->getCountComments($id_article);

            // On inclut l'affichage des commentaires, récupérée en AJAX
            include(VIEWS . '/blog/get-comment.phtml');
        }

        public function showCommentAjax()
        {
            // On vérifie si on est logué et si on a les droits administrateur
            if(!\Session::isLogged() || \Session::get('role') != 'admin') {
                \FlashMessage::setMessage('danger', 'Vous n’avez pas les droits pour accéder à cette page, veuillez vous connecter et réessayer.');
                \Http::redirectTo('home');
            }
        
            // On récupère tous les commentaires
            $comments = $this->model->selectAllComments(['article', 'created_at']);
    
            // On inclut le tableau à afficher en AJAX
            include 'views/admin/table-comment.phtml';
        }

        /**
         * Ajout d'un nouveau commentaire
         * 
         * @param array $params
         * @return void
         */
        public function addComment(array $params): void
        {
            $recaptcha = new \Recaptcha();
            
            // On vérifie la réponse du recaptcha
            if($recaptcha->check() == true) {
                // Pause de 1s, sécurité contre faille force brut
                sleep(1);
                
                // On créé un nouveau commentaire
                $this->model->insert($params);

                // On confirme sa création via message flash
                \FlashMessage::setMessage('success', 'Votre commentaire a été ajouté !');                                             
            } else {
                // On affiche message flash pour indiquer un comportement suspect
                \FlashMessage::setMessage('danger', 'Vous ne semblez pas être un humain !');
                
                // On redirige vers la page d'accueil
                \Http::redirectTo('home');
            }
        }

        /**
         * Suppression un commentaire qui a pour identifiant $id
         * 
         * @param array $params
         * @return void
         */
        public function delComment(array $params): void
        {
            // On vérifie si on est logué et si on a les droits administrateur
            if(!\Session::isLogged() || \Session::get('role') != 'admin') {
                \FlashMessage::setMessage('danger', 'Vous n’avez pas les droits pour accéder à cette page, veuillez vous connecter et réessayer.');
                \Http::redirectTo('home');
            }
            
            extract($params);
            
            // Suppression du commentaire d'identifiant id
            $this->model->delete($id);

            // Confirmation de la suppression
            \FlashMessage::setMessage('warning', 'Commentaire supprimé!');

            // Redirection vers le panneau d'administration
            \Http::redirectTo('show_admin_board');
        }
    }

?>