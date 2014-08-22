<?php

namespace Terrier;

/**
 *
 * Terrier Mailform application
 * Application Response manager
 *
 * @namespace Terrier
 * @class Response
 * @author Yoshiaki Sugimoto <sugimoto@wnotes.net>
 */
class Response
{
    /**
     * View instance
     *
     * @property $view
     * @protected
     * @type \Terrier\View
     */
    protected $view;


    /**
     * Routing mode
     *
     * @property $mode
     * @protected
     * @type string
     */
    protected $mode;

    /**
     * Constructor
     *
     * @constructor
     * @param string $mode
     */
    public function __construct($mode)
    {
        $this->mode = $mode;
        ob_start();
    }


    // ----------------------------------------


    /**
     * Set view instance
     *
     * @method setView
     * @public
     * @param \Terrier\View $view
     */
    public function setView(View $view)
    {
        $this->view = $view;
    }


    // ----------------------------------------


    /**
     * Send redirect header
     *
     * @method redirect
     * @public
     * @param string $action
     */
    public function redirect($action)
    {
        header('Location: ' . Request::buildURL($action));
    }


    // ----------------------------------------


    /**
     * Send display headers
     *
     * @method displayHeader
     * @public
     */
    public function displayHeader()
    {
        header('Content-Type: text/html; charset=utf-8');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
    }


    // ----------------------------------------


    /**
     * Send Get display buffer
     *
     * @method display
     * @public
     * @return string
     */
    public function display()
    {
        echo $this->view->render();

        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }
}
