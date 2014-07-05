<?php

require_once(__DIR__ . '/application/boot.php');

\Terrier\Env::set('default_charset', 'UTF-8');

$router   = new \Terrier\Router();
$action   = $router->process();
$response = new \Terrier\Response($action);

if ( $response->isRedirect() )
{
    $response->redirect();
}
else
{
    $response->setView(new \Terrier\View($action));
    $response->displayHeader();
    $response->display();
}
