<?php

    namespace Controllers;

    use Models\User;

    class Security extends Controller
    {
        protected $modelName = User::class;

        /**
         * Affiche le formulaire de login
         * 
         * @param void
         * @return void
         */
        public function loginForm(): void
        {
            // On génère un token avec pour clef 'login' contre faille csrf
            $csrfToken = \Csrf::generateToken('login');

            // On créé la vue du formulaire de login, on lui passe dans un tableau tous les paramètres nécessaires.
            \Renderer::render('security/form-login', [
                'categories'   => $this->categories,
                'lastArticles' => $this->lastArticles,
                'csrfToken'    => $csrfToken
            ]);
        }

        /**
         * Vérification des identifiant et mdp pour se connecter à son compte
         * 
         * @param void
         * @return void
         */
        public function login(): void
        {
            // On vérifie si on vient bien du formulaire de login
            if(!\Csrf::checkToken(500, HOST . '/index.php?r=login_form', 'login')) {
                \FlashMessage::setMessage('danger', 'Vous êtes un robot !');
                \Http::redirectTo('home');
            }

            if($_SERVER["REQUEST_METHOD"] == "POST") {

                // Pause de 1s, sécurité contre faille force brut
                sleep(1);

                // On vérifie si les champs sont vides
                if(empty($_POST['username']) || empty($_POST['password'])) {

                    // On stocke la valeur du champs username dans un tableau de session $_SESSION['inputs']
                    $_SESSION['inputs']['username'] = $this->filter->filterString($_POST['username']);

                    // On affiche un message flash pour inviter à compléter tous les champs
                    \FlashMessage::setMessage('warning', 'Veuillez compléter tous les champs');

                    // On redirige vers formulaire de login
                    \Http::redirectTo('login_form');
                }

                // On récupère l'identifiant et le mdp en post
                $username = $this->filter->filterString(\Http::getPost('username'));
                $password = $this->filter->filterString(\Http::getPost('password'));

                // On hash le mdp
                $hashed_password = hash('sha3-512', $password);

                // On instancie les class User et Recaptcha
                $user = new User();
                $recaptcha = new \Recaptcha();

                // On vérifie la réponse du recaptcha
                if($recaptcha->check() == true) {

                    // On vérifie si l'identifiant et le mdp existent et correspondent dans la bdd
                    if($value = $user->selectOneUser($username, $hashed_password)) {

                        // Si oui, récupère le profile de l'utilisateur et on stocke ses informations dans des variables de session
                        \Session::set('login', true);
                        \Session::set('username', $value['username']);
                        \Session::set('userId', $value['id']);
                        \Session::set('role', $value['role']);

                        // Si l'utilisateur est administrateur on redirige vers le panneau d'administration
                        if($user->getRole($username, $hashed_password) == 'admin') {
                            \Http::redirectTo('show_admin_board');
                        }

                        //  Si non administrateur alors on redirige vers la page d'accueil
                        \Http::redirectTo('home');

                    } else {

                        // Si l'identiant et le mdp ne sont pas reconnus alors on affiche un message flash pour l'indiquer
                        \FlashMessage::setMessage('warning', 'L\'identifiant ou le mot de passe est erroné!');

                        // On redirige vers le formulaire de login
                        \Http::redirectTo('login_form');
                    }
                } else {

                    // On affiche un message flash pour inviter à ressaisir les identifiants et mdp car action suspecte
                    \FlashMessage::setMessage('warning', 'Veuillez ressaisir vos identifiants');

                    // Redirection vers formulaire de login
                    \Http::redirectTo('login_form');
                }
            }
        }
        
        /**
         * Permet de se déconnecter de son compte
         * 
         * @param void
         * @return void
         */
        public function logout(): void
        {
            // On détruit une session
            \Session::destroy();

            // On affiche message flash pour confirmé la déconnection
            \FlashMessage::setMessage('success', 'Logout réussi');

            // Redirection vers la page d'accueil
            \Http::redirectTo('home');
        }

        /**
         * Affiche le formulaire de récupération d'un compte utilisateur
         * 
         * @param void
         * @return void
         */
        public function recoveryForm(): void
        {
            // On créé la vue du formulaire de récupération de mot de passe, on lui passe dans un tableau tous les paramètres nécessaires.
            \Renderer::render('security/form-recovery', [
                'categories'   => $this->categories,
                'lastArticles' => $this->lastArticles
            ]);
        }

        /**
         * Envoi email avec lien contenant un token crée momentanément et permettant d'accéder au formulaire de création d'un nouveau mdp
         * 
         * @param void
         * @return void
         */
        public function recoveryPassword(): void
        {
            // Pause de 1s, sécurité contre faille force brut
            sleep(1);

            $params = [];

            $email = $_POST['email'];

            // On vérifie si l'email existe dans la bdd
            if($this->model->checkEmail($email) == false) {

                // Si non on affiche un message flash
                \FlashMessage::setMessage('warning', 'Votre email est inconnu !');

                // Redirection vers le formulaire de login
                \Http::redirectTo('login_form');
            }

            // On stocke l'email dans le tableau $params
            $params['email'] = $email;

            // On récupère le nom associé à l'email, on le stocke dans $params
            $name = $this->model->getName($email);
            $params['name'] = $name;

            // On créé un token, qui est stocké dans $params et dans la bdd
            $token = bin2hex(random_bytes(64));
            $params['token'] = $token;
            $this->model->setToken($email, $token);

            // On stocke le contenu html du message envoyé par mail, on le stocke dans $params
            $content = VIEWS . '/security/msg-recovery.html';
            $params['content'] = $content;

            // On instancie la classe Email de PHPMailer, puis on envoie l'email dans lequel on passe l'ensemble des variables dans le tableau $params
            $mail = new \PhpMailer\Email();
            $mail->send($params);

            // On décrément le compteur de tentative de changement de mdp
            $_SESSION['counter'] = 2;

            // On affiche un message flash pour indiquer l'envoi d'un email de recouvrement de mdp
            \FlashMessage::setMessage('success', 'Un email vient de vous être envoyé !');

            // Redirection vers le formulaire de login
            \Http::redirectTo('login_form');
        }

        /**
         * Vérification du token envoyé avec celui associé au compte
         * 
         * @param array $params
         * @return void
         */
        public function changePssw($params): void
        {
            // Pause de 1s, sécurité contre faille force brut
            sleep(1);

            extract($params);

            if(!isset($email) || !isset($d)) {
                \Http::redirectTo('error_404');
            }

            // On vérifie si l'email existe dans la bdd
            if($this->model->checkEmail($email) == false) {

                // Si non on affiche un message flash
                \FlashMessage::setMessage('warning', 'Email inconnu !');

                // Redirection vers la page d'accueil
                \Http::redirectTo('home');
            }

            // On récupère le token généré juste avant l'envoi du mail et stocké dans la bdd
            $token = $this->model->getToken($email);
            
            // Vérification si token passé en get via lien dans mail est identique à celui généré juste avant envoi mail
            if($token ==! $d) {

                // Si token incorrect, on affiche message flash pour indiquer l'échec
                \FlashMessage::setMessage('warning', 'Vous ne pouvez réinitialiser votre mot de passe, veuillez contacter l\'administrateur');

                // Redirection vers la page d'accueil
                \Http::redirectTo('home');
            }

            // On supprime le token dans la bdd
            $isSetToken = $this->model->setToken($email, '');
            
            // On s'assure que la suppression a réussi
            if($isSetToken == true) {
                extract($params);
            
                // On créé la vue du formulaire de création d'un nouveau mdp, on lui passe dans un tableau tous les paramètres nécessaires.
                \Renderer::render('security/form-pssw', [
                    'categories'   => $this->categories,
                    'lastArticles' => $this->lastArticles,
                    'email'        => $email
                ]);
            }
        }

        /**
         * Vérification si mdp saisis identiques, si oui le nouveau mdp remplace l'ancien, sinon redirection vers le formulaire de création d'un nouveau mdp
         * 
         * @param array $params
         * @return void
         */
        public function updatePssw($params): void
        {
            if($_SESSION['counter'] == 0) {
                unset($_SESSION['counter']);
                \FlashMessage::setMessage('warning', 'Nombre de tentatives atteints, veuillez réessayer ultérieurement');
                \Http::redirectTo('home');
            }
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                
                // Pause de 1s, sécurité contre faille force brut
                sleep(1);
                
                // On vérifie si données réçues en post
                if(empty($_POST)) {
                    
                    // On affiche un message flash pour l'indiquer si non
                    \FlashMessage::setMessage('warning', 'Informations non envoyées');
                }
                
                // On vérifie la réponse du recaptcha
                $recaptcha = new \Recaptcha();
                if($recaptcha->check() == false) {
                    // Si false, on redirige vers formulaire de login
                    \Http::redirectTo('login_form');
                }
                
                extract($params);
                
                // On vérifie si il y a email et si valide
                if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    
                    // Si email non valide alors redirection vers formulaire de login
                    \Http::redirectTo('login_form');
                }
                
                // On vérifie si les deux emails saisis sont identiques
                if($password != $password2 || strlen($password) < 8) {
                    if(strlen($password) < 8) {
                        // Si taille mdp < 8 caractère, on affiche message flash
                        \FlashMessage::setMessage('warning', 'Veuillez saisir un mot de passe avec au moins 8 caractères');
                    } else {
                        // Si les deux mdp identiques, on affiche un message flash
                        \FlashMessage::setMessage('warning', 'Les mots de passe sont différents');
                    }

                    // On génère un token
                    $token = bin2hex(random_bytes(64));

                    // On stocke le token dans le tableau $params
                    $params['token'] = $token;

                    // On stocke une copie du token dans la bdd
                    $this->model->setToken($email, $token);

                    // On décrémente le compteur de tentative de saisie d'un mdp valide
                    $_SESSION['counter'] -= 1;

                    // Redirection vers le formulaire de création d'un nouveau mdp, on passe en get le token ainsi que l'email du compte
                    \Http::redirectTo('pssw_change&d=' . $token . '&e=' . $email);
                }

                // On hashe le mdp
                $hashed_password = hash('sha3-512', $password);

                // On met à jour le mdp hashé
                $this->model->updatePssw($email, $hashed_password);

                // On affiche message flash pour confirmé la màj
                \FlashMessage::setMessage('success', 'Modification pssw réussie');
            
                // Redirection vers le formulaire de login
                \Http::redirectTo('login_form');

            }
        }
    }

?>