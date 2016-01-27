<?php
class Validate
{
    /**
     * check if value was provided.
     * 
     * @param string $value
     * @return boolean
     */
    public static function checkRequired($value)
    {
        if (null === $value) {
            return false;
        } else if ('' == trim($value)) {
            return false;
        } else if ('null' == trim($value)) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * check if params are provided.
     * 
     * @param array $params
     * @return boolean
     */
    public static function checkRequiredParam(array $params)
    {
        foreach ($params as $param) {
            if (false === self::checkRequired($param)) {
                return false;
            }
        }
        return true;
    }
}