<?php

if (!function_exists('queryStringToArray')) {
    /**
     * 将常见的query形式的字符串转成数组.
     *
     * @param $string
     * @param bool $urlDecode
     *
     * @return array
     */
    function queryStringToArray($string, $urlDecode = true)
    {
        $string = $urlDecode ? urldecode($string) : $string;

        $array = [];
        $pieces = explode('&', $string);
        foreach ($pieces as $piece) {
            list($k, $v) = explode('=', $piece);
            $array[$k] = $v;
        }

        return $array;
    }
}
