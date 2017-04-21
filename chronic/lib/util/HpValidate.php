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
    
    public static function checkRequiredArray(array $valueList)
    {
        foreach ($valueList as $key => $value) {
            if (null === $value || '' == trim($value) || 'null' == trim($value)) {
                return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, "$key.");
            } else {
                return true;
            }
        }
    }
    
    public static function checkRequiredParam(array $keys, array $values)
    {
        foreach ($keys as $key) {
            if (!isset($values[$key]) 
                    || null === $values[$key] 
                    || '' == trim($values[$key]) 
                    || 'null' == trim($values[$key])) {
                return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, "$key.");
            }
        }
        return true;
    }
    public static function checkNumeric(array $keys, array $values)
    {
        foreach ($keys as $key) {
            if (isset($values[$key]) && !is_numeric($values[$key])) {
                return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, "$key.");
            }
        }
        return true;
    }
    public static function checkRange(array $keys, array $values, array $ranges)
    {
        foreach ($keys as $key) {
            if (!in_array($values[$key], $ranges)) {
                return HpErrorMessage::getError(ERROR_PARAM_RANGE, "$key.");
            }
        }
        return true;
    }
    
    public static function checkMaxLength($value, $maxLength)
    {
        return (strlen($value) <= $maxLength);
    }
    
    public static function checkPhoneNo($value)
    {
        $pattern = '/^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/';
        if (preg_match($pattern, $value)) {
            return true;
        }
        return false;
    }
    
    public static function checkStructalData($value)
    {
        $pattern = '/^((.+:.+,)*.+:.+;)*(.+:.+,)*.+:.+$/';
        if (preg_match($pattern, $value)) {
            return true;
        }
        return false;
    }
    
    public static function checkBirthYear($value)
    {
        $pattern = '/^\d{4}$/';
        if (!preg_match($pattern, $value)) {
            return false;
        }
        if ($value < 1900) {
            return false;
        }
        if ($value > date('Y')) {
            return false;
        }
        return true;
    }
    
    public static function checkTime($value)
    {
        $pattern = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/';
        if (preg_match($pattern, $value)) {
            return true;
        }
        return false;
    }
}