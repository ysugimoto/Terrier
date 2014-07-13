<?php

namespace Terrier;

class Response
{
    protected $view;
    protected $mode;

    public function __construct($mode)
    {
        $this->mode = $mode;
        ob_start();
    }

    public function setView(View $view)
    {
        $this->view = $view;
    }

    public function isRedirect()
    {
        return ( $this->mode === Router::MODE_REDIRECT ) ? true : false;
    }

    public function redirect()
    {
        header('Location: ' . Request::buildURL(Router::MODE_REDIRECT));
        return;
    }

    public function displayHeader()
    {
        if ( $this->mode === Router::MODE_REDIRECT )
        {
            header('Location: ' . Request::buildURL(Router::MODE_INPUT));
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

        $contents = ob_get_contents();
        ob_end_clean();

        echo $contents;
    }
}


