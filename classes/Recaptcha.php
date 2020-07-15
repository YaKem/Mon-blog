<?php

    class Recaptcha
    {
        /**
         * Vérifie la réponse du recaptcha
         *
         * @return bool true si comportement normal, false sinon
         */
        public function check(): bool
        {              
            $url = "https://www.google.com/recaptcha/api/siteverify";

            $data = [
                'secret' => RECAPTCHA_SECRET_KEY,
                'response' => $_POST['token'],
                'remoteip' => $_SERVER['REMOTE_ADDR']
            ];

            $options = [
                'http' => [
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data)
                ]
            ];

            $context  = stream_context_create($options);
            
            $response = file_get_contents($url, false, $context);
            
            $res = json_decode($response, true);

            if($res['success'] == '1' && $res['score'] >= 0.5) {
                return true;
            }

            return false;
        }
    }

?>