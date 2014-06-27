<?php

namespace Terrier;

class View
{
    protected $template;

    public function __construct($action)
    {
        $this->template = Template::compile($action);
    }

    public function render() {
        $arguments = array(
            Validation::values(),
            Validation::errors(),
            Request::postAll(),
            Request::getAll(),
            Request::serverAll()
        );

        return call_user_func_array(array($this, 'template'), $arguments);
    }
}



