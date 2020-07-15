<?php

    namespace Controllers;

    use Models\Article as articleModel;
    use Models\Comment;


    class Article extends Controller
    {
        /**
         * @var string $modelName nom de la classe articleModel
         */
        protected $modelName = articleModel::class;

        /**
         * Affiche un article et les articles relatés par ordre antéchronologique
         * 
         * @param array $params
         * @return void
         */
        public function showArticle(array $params): void
        {   
            extract($params);

            // On récupère l'article d'identifiant id.
            $article = $this->model->selectOne($id);

            // On récupère tous les commentaires de cet article, classer du plus récent au plus ancien.
            $commentManager = new Comment();
            $comments = $commentManager->selectAllForArticle($id, "DESC");

            // On récupère les articles de la même catégorie, l'article lui-même étant exclu
            $relatedArticles = $this->model->getRelatedArticles($id);

            // On créé la vue de l'article, on lui passe dans un tableau tous les paramètres nécessaires.
            \Renderer::render('blog/article', [
                'article'         => $article,
                'comments'        => $comments,
                'categories'      => $this->categories,
                'lastArticles'    => $this->lastArticles,
                'relatedArticles' => $relatedArticles
            ]);
        }

        /**
         * Affiche en AJAX la liste d'articles dans le panneau administrateur
         *
         * @return void
         */
        public function showArticleAjax(): void
        {
            // On vérifie si on est logué et si on a les droits administrateur.
            if(!\Session::isLogged() || \Session::get('role') != 'admin') {
                \FlashMessage::setMessage('danger', 'Vous n’avez pas les droits pour accéder à cette page, veuillez vous connecter et réessayer.');
                \Http::redirectTo('home');
            }
    
            // On récupère tous les articles
            $articles = $this->model->selectAll(['created_at DESC']);

            // On inclut la table des articles
            include 'views/admin/table-article.phtml';
        }

        /**
         * Affiche le résultat de la recherche d'un mot (tout en miniscule ou avec la première lettre en majuscule en le surlignant)
         * 
         * @param array $params
         * @return void
         */
        public function search(array $params): void
        {
                extract($params);
                // On filtre le terme de la recherche
                $keyword = $this->filter->filterString($keyword);
                // On récupère les résultats de la recherche du terme dans le corps des articles ainsi que leur titre
                $res = $this->model->searchInArticles($keyword);

                extract($res);

                // On créé la vue des résultats de la recherche du terme, on lui passe dans un tableau tous les paramètres nécessaires.
                \Renderer::render('blog/search', [
                    'results'      => $results,
                    'keyword'      => $keyword,
                    'categories'   => $this->categories,
                    'lastArticles' => $this->lastArticles
                ]);
        }

        /**
         * Affiche les catégories existantes dans la bdd
         * 
         * @param array $params
         * @return void
         */
        public function showArticlesForOneCategory(array $params): void
        {
            // On récupère les articles de la catégorie
            $results = $this->model->findCategory($params);

            // On créé la vue des articles de la catégorie choisie, on lui passe dans un tableau tous les paramètres nécessaires.
            \Renderer::render('blog/category', [
                'results'      => $results,
                'categories'   => $this->categories,
                'lastArticles' => $this->lastArticles
            ]);
        }

        /**
         * Affiche le formulaire d'articles
         * 
         * @param array $params || null
         * @return void
         */
        public function showForm(array $params = []): void
        {
            // On vérifie si on est logué et si on a les droits administrateur.
            if(!\Session::isLogged() || \Session::get('role') != 'admin') {
                \FlashMessage::setMessage('danger', 'Vous n’avez pas les droits pour accéder à cette page, veuillez vous connecter et réessayer.');
                \Http::redirectTo('home');
            }

            // On génère un token avec pour clef 'article' contre faille csrf
            $csrfToken = \Csrf::generateToken('article');

            extract($params);

            // S'il existe un identifiant, un article existe alors on se emt en mode mise à jour (on change le titre, on récupère l'article de cet id et on change le texte du bouton). Sinon, on passe en mode création d'un nouvel article.
            if(isset($id) && !empty($id)) {
                $title = 'Modifier article';
                $articleToUpdate = $this->model->selectOne($id);
                $btnLabel = 'Modifier';
            } else {
                $title = 'Créer article';
                $articleToUpdate = '';
                $btnLabel = 'Créer';
            }

            // On créé la vue du formulaire de l'article, on lui passe dans un tableau tous les paramètres nécessaires.
            \Renderer::render('blog/form-article', [
                'title'        => $title,
                'article'      => $articleToUpdate,
                'label'        => $btnLabel,
                'categories'   => $this->categories,
                'lastArticles' => $this->lastArticles,
                'csrfToken'    => $csrfToken
            ]);
        }

        /**
         * Permet la création d'un nouvel article si identifiant n'existe pas sinon met à jour un article existant
         * 
         * @param array $params
         * @return void
         */
        public function createArticle($params): void
        {
            // On vérifie si on vient bien du formulaire de création/màj d'un article
            if(!\Csrf::checkToken(500, HOST . '/index.php?r=form_article', 'article')) {
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

            extract($params);
            
            // Si au moins l'un des champs est vide alors on stocke les valeurs des inputs dans un tableau de session
            if(empty($params['author']) || empty($params['title']) || empty($params['content']) || empty($params['category'])) {           

                $_SESSION['inputs']['author'] = $this->filter->filterString($params['author']);
                $_SESSION['inputs']['title'] = $this->filter->filterString($params['title']);
                $_SESSION['inputs']['content'] = $this->filter->filterString($params['content']);
                $_SESSION['inputs']['category'] = $this->filter->filterString($params['category']);

                \FlashMessage::setMessage('warning', 'Veuillez compléter tous les champs');
                \Http::redirectTo('form_article');
            }

            // un fichier a-t-il été envoyé ?
            if(empty($_FILES['fichier']['tmp_name'])) {             
                $this->model->updateArticle($params, 'no-file');
                
                \FlashMessage::setMessage('success', 'Votre article a été mis à jour');

                \Http::redirectTo('show_admin_board');
            }

            // Le fichier a-t-il été correctement uploadé ?
            if(!is_uploaded_file($_FILES['fichier']['tmp_name'])) {
                \FlashMessage::setMessage('warning', 'Un problème a eu lieu lors de l\'upload');
            }
            
            // Le type du fichier est-il autorisé ?
            $typeMime = $_FILES['fichier']['type'];
            $type = ['jpg', 'jpeg', 'png'];
            
            $extensionMime = substr(strrchr($typeMime, '//'), 1);
            
            if(!in_array($extensionMime, $type)) {
                \FlashMessage::setMessage('warning', 'Le fichier doit être obligatoirement de type jpg, jpeg ou png');
            }

            // Le fichier respecte-t-il la taille limite ?
            $size = $_FILES['fichier']['size'];
            
            if($size > 8000000) {
                \FlashMessage::setMessage('warning', 'Le fichier ne doit pas dépasser 8 Mo');
                \Http::redirectTo('show_admin_board');
            }

            // Nettoyage du nom de fichier saisi
            $name = $_POST['fileName'];
            $name = $this->filter->filterNameFile($name);

            // Chemin complet de destination, extension comprise
            $extension = substr(strrchr($_FILES['fichier']['name'], '.'), 1);
            $path = 'assets/images/' . $name . '.' . $extension;
            $params['image'] = $name . '.' . $extension;

            // On peut maintenant déplacer le fichier avec le nouveau nom
            $moveIsOK = move_uploaded_file($_FILES['fichier']['tmp_name'], $path);
            
            if(!$moveIsOK) {
                \FlashMessage::setMessage('warning', 'Suite à une erreur, le fichier n\'a pas été uploadé');
            }

            // On supprime l'ancien fichier
            $nameFile = $params['ancientFileName'];

            if(!empty($nameFile)) {
                unlink('assets/images/' . $nameFile);               
            }

            // Si id existe et a une valeur alors un article existe et donc on le met à jour
            if(isset($id) && !empty($id)) {

                $this->model->updateArticle($params);

                \FlashMessage::setMessage('success', 'Votre article a été mis à jour');
                
                \Http::redirectTo('show_admin_board');
                
            }

            // Le formulaire est vide car pas d'id, on créé alors un nouvel article
            $this->model->insertArticle($params);

            // On affiche un messsage flash pour confirmer la création avec succès de l'article
            \FlashMessage::setMessage('success', 'Votre article a été créé avec succès');         
            
            // Redirection vers le panneau d'administration
            \Http::redirectTo('show_admin_board');            
        }
        
        /**
         * Supprime un article qui a pour identifiant $id
         * @param array $params
         * @return void
         */
        public function delArticle($params): void
        {
            // On vérifie si on est logué et si on a les droits administrateur
            if(!\Session::isLogged() || \Session::get('role') != 'admin') {
                \FlashMessage::setMessage('warning', 'Vous n’avez pas les droits pour accéder à cette page, veuillez vous connecter et réessayer.');
                \Http::redirectTo('home');
            }
            
            extract($params);

            // On récupère le nom du fichier à supprimer
            $nameFile = $this->model->getNameFile($id);

            // On supprime l'image avec pour nom $nameFile
            unlink('assets/images/' . $nameFile);

            // On supprime les commentaires associés à l'article d'identifiant id
            $commentManager = new Comment();
            $commentManager->deleteFromArticle($id);

            // On supprime l'article d'identifiant id
            $this->model->delete($id);

            // On affiche un message flash pour confirmer la suppression de l'article
            \FlashMessage::setMessage('warning', 'Votre article a été supprimé');

            // On se redirige vers le tableau d'administration
            \Http::redirectTo('show_admin_board');
        }
    }

?>