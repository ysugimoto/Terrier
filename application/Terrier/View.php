<?php

namespace Terrier;

/**
 *
 * Terrier Mailform application
 * Application View class
 *
 * @namespace Terrier
 * @class View
 * @author Yoshiaki Sugimoto <sugimoto@wnotes.net>
 */
class View
{
    /**
     * Stack Template instance
     *
     * @property $template
     * @type \Terrier\Template
     */
    protected $template;


    // ----------------------------------------


    /**
     * Constructor
     *
     * @constrctor
     * @param string $action
     */
    public function __construct($action)
    {
        $this->template = Template::make($action);
    }


    // ----------------------------------------


    /**
     * Render view
     *
     * @method render
     * @public
     * @return string
     */
    public function render() {
        $obj             = new \stdClass();
        $obj->post       = new Variable(Request::postAll());
        $obj->get        = new Variable(Request::getAll());
        $obj->server     = new Variable(Request::serverAll());
        $obj->value      = new Variable(Validation::getValues());
        $obj->error      = new Variable(Validation::getErrors());
        $obj->action     = new Variable(array(
            'input'   => Request::buildURL(Router::MODE_INPUT),
            'confirm' => Request::buildURL(Router::MODE_CONFIRM),
            'send'    => Request::buildURL(Router::MODE_SEND)
        ));

        $buffer = $this->template->parse(new Variable($obj), '');

        // set onetime token
        $token = Session::oneTimeToken();
        $buffer = str_replace(
            '</form>',
            '<input type="hidden" name="token" value="' . $token . '"></form>',
            $buffer
        );

        return $buffer;
    }
}
