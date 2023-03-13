<?php


require __DIR__ . "/vendor/autoload.php";

session_start();
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('VIEW_PATH', __DIR__ . '/App/views/');

$router = new App\Router();
try {
    $router
        ->get('/',[\App\Controllers\HomeController::class, 'index'])
        ->get('/invoices',[\App\Controllers\InvoiceController::class, 'index'])
        ->get('/invoices/create',[\App\Controllers\InvoiceController::class, 'create'])
        ->post('/invoices/create',[\App\Controllers\InvoiceController::class, 'store']);


    echo $router->resolve($_SERVER['REQUEST_URI'], strtolower($_SERVER['REQUEST_METHOD']));
}catch (\App\Exceptions\RouteNotFoundException $err){
    header('HTTP/1.1 404 Not Found');//Output buffering
    echo \App\View::make('error/404');
}

