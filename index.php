<?php

require __DIR__ . '/vendor/autoload.php';

$app = new Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'db' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'blog_salman',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => ''
        ]
    ]
]);

$container = $app->getContainer();
// CONTAINER UNTUK VIEW
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/templates', [
        'cache' => false
    ]);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    return $view;
};


// CONTAINER UNTUK DB
// $container['db'] = function () {
//     return new PDO('mysql:host=localhost; dbname=blog_salman', 'root', '');
// };

// CONTAINER UNTUK DB ELOQUENT
$container['db'] = function ($container) {
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($container['settings']['db']);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
    return $capsule;
};

//CONTAINER NOT FOUND HANDLER
$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        return $container->view->render($response, '404.twig');
    };
};



$app->get('/', function ($request, $response) {
    return $this->view->render($response, 'home.twig');
});


$app->get('/forum', function ($request, $response, $args) {
    var_dump($this->db->table('news')->get());

    // $datas = $this->db->query("SELECT * FROM news")->fetchAll(PDO::FETCH_OBJ);
    die();
    return $this->view->render($response, 'forum.twig', []);
})->setName('single');


$app->run();
