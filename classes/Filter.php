<?php

    class Filter
    {
        /**
         * Filtre les noms de fichiers
         *
         * @param string $name nom quelconque
         * @return string $name nom nettoyé des caractères indésirables, accents
         */
        public function filterNameFile(string $name): string
        {
            // Remplacer les caractères qui ne sont ni des lettres ni des nombres par un tiret
            $name = preg_replace('%[^\\pL\d]+%u', '-', $name);
            // Retirer les tirets en début et fin de chaîne
            $name = trim($name, '-');
            // Passer d'un encodage UTF-8 à un encodage ASCII pour nettoyer les accents (é,è deviennent e)
            $name = iconv('utf-8', 'us-ascii//TRANSLIT', $name);
            // Mettre le nom en minuscule
            $name = strtolower($name);
            // Suppression des caractères indésirables
            $name = preg_replace('%[^-\w]+%', '', $name);

            return $name;
        }

        /**
         * Filtre variables string
         *
         * @param string $name nom quelconque
         * @return string $name nom nettoyé des caractères indésirables
         */
        public function filterString(string $name): string
        {
            // Remplacer les caractères qui ne sont ni des lettres ni des nombres par un tiret
            $name = preg_replace('%[^\\pL\d]+%u', '-', $name);
            // Retirer les tirets en début et fin de chaîne
            $name = trim($name, '-');
            // Suppression des caractères indésirables
            $name = preg_replace('%[^-\w]+%', '', $name);

            return $name;
        }
        
        /**
         * Vérifie si email
         *
         * @param string $email
         * @return bool true si forme requise, false sinon
         */
        public function checkEmail(string $email): bool
        {
            $pattern = '%^[a-zA-Z0-9\.]+@{1}[a-zA-Z0-9]+\.{1}[a-zA-Z0-9]{2,}$%';
            if(preg_match($pattern, $email)) {
                return true;
            } else {
                return false;
            }
        }
    }

?>