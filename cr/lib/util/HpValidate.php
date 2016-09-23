<?php
class HpValidate
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
    
    public static function checkPhoneNo($value)
    {
        $pattern = '/^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/';
        if (preg_match($pattern, $value)) {
            return true;
        }
        return false;
    }
}