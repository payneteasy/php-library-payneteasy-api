<?php

namespace PaynetEasy\PaynetEasyApi\Utils;

class String
{
    /**
     * Convert string from format <this_is_the_string> or
     * <this-is-the-string> to format <ThisIsTheString>
     *
     * @param       string      $string         String to coversion
     *
     * @return      string                      Converted string
     */
    static public function camelize($string)
    {
        return implode('', array_map('ucfirst', preg_split('/_|-/', $string)));
    }

    /**
     * Convert string from format <ThisIsTheString> to format
     * <this-is-the-string> or <this-is-the-string> or etc
     *
     * @param       string      $string         String to coversion
     * @param       string      $delimeter      Delimeter for string chunks
     *
     * @return      string                      Converted string
     */
    static public function uncamelize($string, $delimeter = '_')
    {
        return strtolower(implode($delimeter, preg_split('/(?=[A-Z])/', lcfirst($string))));
    }
}