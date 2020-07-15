<?php

    namespace Models;

    class User extends Model
    {
        /**
         * @var string $table nom de la table
         */
        protected $table = 'users';

        /**
         * Récupère les champs d'un utilisateur
         *
         * @param string $username pseudo de l'utilisateur
         * @param string $password mdp de l'utilisateur
         * @return array||bool $result champs de l'utilisateur
         */
        public function selectOneUser(string $username, string $password)
        {
            $sql = "SELECT id, username, role FROM {$this->table} WHERE username = ? AND password = ?";
            $query = $this->pdo->prepare($sql);
            $query->execute([$username, $password]);
            $result = $query->fetch(\PDO::FETCH_ASSOC);
            return $result;    
        }

        /**
         * Récupère le rôle de l'utilisateur
         *
         * @param string $username
         * @param string $password
         * @return string $role
         */
        public function getRole(string $username, string $password): string
        {
            $sql = "SELECT role FROM {$this->table} WHERE username = ? AND password = ?";
            $query = $this->pdo->prepare($sql);
            $query->execute([$username, $password]);
            $result = $query->fetch(\PDO::FETCH_ASSOC);
            $role = $result['role'];
            return $role;    
        }

        /**
         * Récupère le nom de l'utilisateur d'email $email
         *
         * @param string $email
         * @return string $name
         */
        public function getName(string $email): string
        {
            $sql = "SELECT `username` FROM {$this->table} WHERE email = ?";
            $query = $this->pdo->prepare($sql);
            $query->execute([$email]);
            $result = $query->fetch(\PDO::FETCH_ASSOC);
            $name = $result['username'];
            return $name;    
        }

        /**
         * Enregistre un token associé à un utilisateur d'email $email
         *
         * @param string $email
         * @param string $token
         * @return bool retourne un boolean, true si réalisé avec succès, false sinon
         */
        public function setToken(string $email, string $token): bool
        {
            $sql = "UPDATE {$this->table} SET token = ? WHERE email = ?";
            $query = $this->pdo->prepare($sql);

            if(!$query->execute([$token, $email])) {
                return false;
            }
            return true;
        }

        /**
         * Récupère le token associé à un utilisateur d'email $email
         *
         * @param string $email
         * @return string $res token
         */
        public function getToken(string $email): string
        {
            $sql = "SELECT token FROM {$this->table} WHERE email = ?";
            $query = $this->pdo->prepare($sql);
            $query->execute([$email]);
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            $res = $res['token'];

            return $res;
        }

        /**
         * Remplace l'ancien mdp par le nouveau
         *
         * @param string $email
         * @param string $hashed_password
         * @return void
         */
        public function updatePssw(string $email, string $hashed_password): void
        {
            $sql = "UPDATE {$this->table} SET password = ? WHERE email = ?";
            $query = $this->pdo->prepare($sql);
            $query->execute([$hashed_password, $email]);
        }

        /**
         * Vérifie si un email existe dans la bdd
         *
         * @param string $email
         * @return bool
         */
        public function checkEmail(string $email): bool
        {
            $sql = "SELECT id FROM {$this->table} WHERE email = ?";
            $query = $this->pdo->prepare($sql);
            $query->execute([$email]);

            if(!$res = $query->fetch(\PDO::FETCH_ASSOC)) {
                return false;
            }

            return true;
        }
    }

?>