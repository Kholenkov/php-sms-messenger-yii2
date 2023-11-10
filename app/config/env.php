<?php

if (!function_exists('env')) {
    function env($key, $default = null)
    {
        if (isset($_SERVER[$key])) {
            $value = $_SERVER[$key];
        } elseif (isset($_ENV[$key])) {
            $value = $_ENV[$key];
        } else {
            $value = getenv($key);
        }
        if (false === $value) {
            return $default;
        }
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'null':
            case '(null)':
                return null;
            case 'empty':
            case '(empty)':
                return '';
        }
        return $value;
    }
}
