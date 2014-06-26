<?php

namespace Terrier\Basis;

abstract class Injector
{
    public function inject($propertyName, $instance)
    {
        $this->{$propertyName} = $instance;
    }
}

