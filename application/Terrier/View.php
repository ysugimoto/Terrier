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
        $obj         = new \stdClass();
        $obj->post   = new Variable(Request::postAll());
        $obj->get    = new Variable(Request::getAll());
        $obj->server = new Variable(Request::serverAll());
        $obj->value  = new Variable(Validation::getValues());
        $obj->error  = new Variable(Validation::getErrors());

        // include user helper
        if ( file_exists(TEMPLATE_PATH . 'functions.php') )
        {
            require_once(TEMPLATE_PATH . 'functions.php');
        }

        echo $this->template->parse(new Variable($obj), '');
    }
}



