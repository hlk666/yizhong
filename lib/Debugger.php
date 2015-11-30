<?php
class Debugger
{
    public static function displayTime()
    {
        echo '<br />******************************<br />';
        echo self::getCurrentTime();
        echo '<br />******************************<br />';
    }
    
    private static function getCurrentTime()
    {
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }
}
