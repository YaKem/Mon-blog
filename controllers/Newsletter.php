<?php

    namespace Controllers;

    use Models\Newsletter as newsletterModel;

    class Newsletter extends Controller
    {
        protected $modelName = newsletterModel::class;

        /**
         * Enregistrement dans la bdd du mail de l'utilisateur qui s'abonne à la newsletter
         * 
         * @param array $params
         * @return void
         */
        public function create($params): void
        {
            // Pause de 1s, sécurité contre faille force brut
            sleep(1);

            extract($params);

            // On vérifie si entrée correspond à un email
            if(!$this->filter->checkEmail($email)) {

                // On affiche message flash pour indiquer un problème de saisie
                \FlashMessage::setMessage('warning', 'Email incorrect');

                // On redirige vers page d'accueil
                \Http::redirectTo('home');
            }

            // On ajoute l'email dans la bdd
            $this->model->insertEmail($email);

            // On affiche un message flash pour indiqué que l'ajout a réussi
            \FlashMessage::setMessage('success', 'Enregistrement réussi!');

            // Redirection vers la page d'accueil
            \Http::redirectTo('home');
        }
    }

?>