<?php

namespace Terrier;

class Response
{
    protected $view;
    protected $mode;

    public function __construct($mode)
    {
        $this->mode = $mode;
    }

    public function setView(View $view)
    {
        $this->view = $view;
    }

    public function isRedirect()
    {
        return $this->mode === Router::MODE_REDIRECT;
    }

    public function redirect()
    {
        header('Location: ' . Config::get('base_url', '/') . 'index.php');
        return;
    }

    public function displayHeader()
    {
        if ( $this->mode === Router::MODE_REDIRECT )
        {
            header('Location: ' . Config::get('base_url') . 'index.php');
            return;
        }

        header('Content-Type: text/html; charset=utf-8');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
    }

    public function display()
    {
        if ( $this->mode === Router::MODE_REDIRECT )
        {
            return;
        }

        echo $this->view->render();
    }
}


