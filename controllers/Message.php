<?php

    namespace Controllers;

    use Models\Message as messageModel;

    class Message extends Controller
    {
        /**
         * @var string $modelName Nom de la classe messageModel
         */
        protected $modelName = messageModel::class;
        
        public function showMessageAjax()
        {
            if(!\Session::isLogged() || \Session::get('role') != 'admin') {
                \FlashMessage::setMessage('danger', 'Vous n’avez pas les droits pour accéder à cette page, veuillez vous connecter et réessayer.');
                \Http::redirectTo('home');
            }
                 
            $messages = $this->model->selectAll(['created_at DESC', 'lastname']);
    
            include 'views/admin/table-message.phtml';
        }


        /**
         * Affiche le formulaire de contact
         * 
         * @param void
         * @return void
         */
        public function showForm(): void
        {     
            $csrfToken = \Csrf::generateToken('message');

            \Renderer::render('blog/form-message', [
                'categories'   => $this->categories,
                'lastArticles' => $this->lastArticles,
                'csrfToken'    => $csrfToken
            ]);
        }

        /**
         * Ajoute un nouveau contact
         * @param array $params
         * @return void
         */
        public function addMessage(array $params): void
        {       
            // On vérifie si on vient bien du formulaire de création d'un message
            if(!\Csrf::checkToken(500, HOST . '/index.php?r=form_message', 'message')) {
                \FlashMessage::setMessage('danger', 'Vous êtes un robot !');
                \Http::redirectTo('home');
            }

            $recaptcha = new \Recaptcha();

            // On vérifie la réponse du recaptcha
            if($recaptcha->check() == false) {
                \FlashMessage::setMessage('warning', 'Un problème s\'est produit lors de l\'envoi de votre message, veuillez rééssayer!');
                \Http::redirectTo('form_message');
            }

            // Pause de 1s, sécurité contre faille force brut
            sleep(1);
            
            // On stocke les entrées du formulaire dans un tableau de session $_SESSION['inputs'] les restaurer ensuite 
            $_SESSION['inputs'] = $params;
            
            // On vérifie chaque champs, si vide on stocke le message d'erreur dans un tableau de session $_SESSION['errors']
            if(empty($params['firstname'])) {
                $_SESSION['errors']['firstname'] = "Veuillez saisir un nom";
            }
            if(empty($params['lastname'])) {
                $_SESSION['errors']['lastname'] = "Veuillez saisir un prénom";
            }
            if(empty($params['email'])) {
                $_SESSION['errors']['email'] = "Veuillez saisir un email";
            }
            if(!$this->filter->checkEmail($params['email'])) {
                $_SESSION['errors']['email'] = "Veuillez saisir un email valide";
            }
            if(empty($params['content'])) {
                $_SESSION['errors']['content'] = "Veuillez saisir un message";
            }

            // Si le tableau d'erreurs contient au moins un message alors on redirige vers le formulaire
            if(!empty($_SESSION['errors'])) {
                \Http::redirectTo('form_message');
            }
            
            // Création d'un nouveau message
            $this->model->insert($params);

            // Affichage d'un message flash confirmant la création du message
            \FlashMessage::setMessage('success', 'Votre message a été envoyé avec succès, nous vous répondrons dès que possible');
            
            // Redirection vers la page d'accueil
            \Http::redirectTo('home');
        }
        
        /**
         * Suppression d'un message
         *
         * @param array $params
         * @return void
         */
        public function delMessage(array $params): void
        {
            // On vérifie si on est logué et si on a les droits administrateur
            if(!\Session::isLogged() || \Session::get('role') != 'admin') {
                // Affichage message flash indiquant l'absence de droits pour exécuter l'action
                \FlashMessage::setMessage('danger', 'Vous n’avez pas les droits pour accéder à cette page, veuillez vous connecter et réessayer.');
                
                // Redirection vers la page d'accueil
                \Http::redirectTo('home');
            }
            
            extract($params);
            
            // Suppression du message d'identifiant id
            $this->model->delete($id);
            
            // Affichage message flash confirmant la suppression du message
            \FlashMessage::setMessage('warning', 'Message supprimé!');
            
            // Redirection vers le panneau d'administration
            \Http::redirectTo('show_admin_board');
        }
    }

?>