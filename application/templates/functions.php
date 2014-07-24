<?php

namespace Terrier;

if ( ! function_exists('input_hidden_all') )
{
    function input_hidden_all()
    {
        $out    = array();
        $values = Validation::getValues();
        foreach ( $values as $field => $value )
        {
            $out[] = '<input type="hidden" name="' . Helper::escape($field) . '" value="' . Helper::escape($value) . '" />';
        }

        return implode("\n", $out);
    }
}

if ( ! function_exists('input_date_all') )
{
    function input_data_all()
    {
        $out    = array();
        $values = Validation::getValues();
        foreach ( $values as $field => $value )
        {
            $out[] = $field . ': ' . $value;
        }

        return implode("\n", $out);
    }
}
