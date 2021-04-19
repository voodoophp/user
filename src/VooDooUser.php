<?php

namespace VooDoo\User;

use Closure;

class VooDooUser
{
    public static $questionsCallBacks = [];

    public static function path($path = "")
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . ltrim($path, '/');
    }

    /**
     * @param Closure $questionCallBack
     */
    public static function askFor(Closure $questionCallBack)
    {
        array_push(self::$questionsCallBacks, $questionCallBack);
    }

}
