<?php

    class Router
    {
        /**
         * @var string $path chemin initialisé à home, page d'accueil
         */
        private $path = 'home';

        /**
         * @var array tableau associatif de tableaux associatifs de tous les chemins, contrôleurs et méthodes
         */
        private $paths = [
            'home'                   => ['controller' => 'Home',       'method' => 'showHome'                  ],
            'article'                => ['controller' => 'Article',    'method' => 'showArticle'               ],
            'form_article'           => ['controller' => 'Article',    'method' => 'showForm'                  ],
            'create_article'         => ['controller' => 'Article',    'method' => 'createArticle'             ],
            'delete_article'         => ['controller' => 'Article',    'method' => 'delArticle'                ],
            'article_category'       => ['controller' => 'Article',    'method' => 'showArticlesForOneCategory'],
            'article_search'         => ['controller' => 'Article',    'method' => 'search'                    ],
            'form_message'           => ['controller' => 'Message',    'method' => 'showForm'                  ],
            'create_message'         => ['controller' => 'Message',    'method' => 'addMessage'                ],
            'delete_message'         => ['controller' => 'Message',    'method' => 'delMessage'                ],
            'comment'                => ['controller' => 'Comment',    'method' => 'showComment'               ],
            'create_comment'         => ['controller' => 'Comment',    'method' => 'addComment'                ],
            'delete_comment'         => ['controller' => 'Comment',    'method' => 'delComment'                ],
            'about'                  => ['controller' => 'About',      'method' => 'showAbout'                 ],
            'show_admin_board'       => ['controller' => 'Admin',      'method' => 'showBoard'                 ],
            'form_category'          => ['controller' => 'Category',   'method' => 'showForm'                  ],
            'create_category'        => ['controller' => 'Category',   'method' => 'addCategory'               ],
            'delete_category'        => ['controller' => 'Category',   'method' => 'delCategory'               ],
            'error_404'               => ['controller' => 'Error',      'method' => 'showError'                 ],
            'login_form'             => ['controller' => 'Security',   'method' => 'loginForm'                 ],
            'connection'             => ['controller' => 'Security',   'method' => 'login'                     ],
            'deconnection'           => ['controller' => 'Security',   'method' => 'logout'                    ],
            'recovery_form'          => ['controller' => 'Security',   'method' => 'recoveryForm'              ],
            'recovery_password'      => ['controller' => 'Security',   'method' => 'recoveryPassword'          ],
            'newsletter_inscription' => ['controller' => 'Newsletter', 'method' => 'create'                    ],
            'pssw_change'            => ['controller' => 'Security',   'method' => 'changePssw'                ],
            'pssw_update'            => ['controller' => 'Security',   'method' => 'updatePssw'                ],
            'article_ajax'           => ['controller' => 'Article',    'method' => 'showArticleAjax'           ],
            'comment_ajax'           => ['controller' => 'Comment',    'method' => 'showCommentAjax'           ],
            'category_ajax'          => ['controller' => 'Category',   'method' => 'showCategoryAjax'          ],
            'message_ajax'           => ['controller' => 'Message',    'method' => 'showMessageAjax'           ],
            'privacy'                => ['controller' => 'Privacy',    'method' => 'showPrivacy'               ]
        ];

        /**
         * Récupère toutes les données request, get, post, puis instancie le contrôleur correspondant au chemin et passe un tableau des paramètres en argument dans sa méthode
         *
         * @return void
         */
        public function renderController(): void
        {
            $params = [];

            if(!empty($_GET['r'])) {
                $this->path = $_GET['r'];
                $this->path = strtolower($this->path);
                $elements = explode("/", $this->path);
                $this->path = $elements[0];
                unset($elements[0]);
            }

            if(isset($elements[1]) && isset($elements[2])) {
                $elements[2] = intval($elements[2]);
                $params = [$elements[1] => $elements[2]];
                $elements = [];
            }

            if(!empty($_GET['d']) && !empty($_GET['e'])) {
                $params['d'] = $_GET['d'];
                $params['email'] = $_GET['e'];
            }

            if(!empty($_POST)) {
                foreach($_POST as $k => $v) {
                    $params[$k] = $v;
                }
            }
            
            if(array_key_exists($this->path, $this->paths)) {
                $controller = $this->paths[$this->path]['controller'];
                $method = $this->paths[$this->path]['method'];

                $controllerName = "\Controllers\\".$controller;

                $controller = new $controllerName();
                $controller->$method($params);
            } else {
                \Http::redirectTo('error_404');
            }
        }
    }

?>