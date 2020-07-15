<?php

    class FlashMessage
    {

        /**
         * Met en place un message flash
         *
         * @param string $type success/warning/danger
         * @param string $message texte à afficher
         * @return void
         */
        static function setMessage(string $type, string $message): void
        {
            $_SESSION['flash'] = [];
            $_SESSION['flash']['type'] = $type;
            $_SESSION['flash']['content'] = $message;        
        }

        /**
         * Récupère le type du message flash
         *
         * @return string
         */
        static function getType(): string
        {
            return $_SESSION['flash']['type'];
        }

        /**
         * Récupère le contenu du message flash
         *
         * @return string
         */
        static function getContent(): string
        {
            return $_SESSION['flash']['content'];
        }

        /**
         * Détruit un message flash
         *
         * @return void
         */
        static function unsetMessage(): void
        {
            unset($_SESSION['flash']);
        }
    }
?>