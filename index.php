<?php

/**
 * Terrier Application Index file
 *
 * @author Yoshiaki Sugimoto <sugimoto@wnotes.net>
 * @copyright Yoshiaki Sugimoto
 */

// load bootstrap file
require_once(__DIR__ . '/application/boot.php');

// routing and processiong
$router   = new \Terrier\Router();
$action   = $router->process();
$response = new \Terrier\Response($action);

// Application need redirect?
if ( \Terrier\Router::isRedirect() )
{
    $response->redirect($action);
}
// display HTML
else
{
    $response->setView(new \Terrier\View($action));
    $response->displayHeader();
    echo $response->display();
}
