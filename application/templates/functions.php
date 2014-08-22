<?php

/**
 * Terrier Template helper functions
 *
 * @author Yoshiaki Sugimoto <sugimoto@wnotes.net>
 * @copyright Yoshiaki Sugimoto
 */

namespace Terrier;

if ( ! function_exists('input_hidden_all') )
{
    /**
     * Input value set to hidden field
     * @return string
     */
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
    /**
     * Input value set to key:value
     * @return string
     */
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
