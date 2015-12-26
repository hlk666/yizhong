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
        } else {
            return true;
        }
    }
}