<?php

namespace Terrier;

class View
{
    protected $template;

    public function __construct($action)
    {
        $this->template = Template::make($action);
    }

    public function render() {
        $obj             = new \stdClass();
        $obj->post       = new Variable(Request::postAll());
        $obj->get        = new Variable(Request::getAll());
        $obj->server     = new Variable(Request::serverAll());
        $obj->value      = new Variable(Validation::getValues());
        $obj->error      = new Variable(Validation::getErrors());
        $obj->confirmUrl = Request::buildURL('confirm');
        $obj->sendUrl    = Request::buildURL('send');

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



