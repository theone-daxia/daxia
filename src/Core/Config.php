<?php

namespace Daxia\Core;

class Config
{
    /**
     * Get the specified configuration value.
     *
     * @param  string  $key
     * @return mixed
     */
    public static function get($key)
    {
        $file = dirname(__DIR__) . '/Config/' . $key . '.php';

        if (file_exists($file)) {
            return require($file);
        } else {
            echo 'file not exists';
        }

        return [];
    }
}
