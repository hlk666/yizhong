<?php
define('AUTHORITY_SUPER_ADMIN', 0);
define('AUTHORITY_HOSPITAL_ADMIN', 1);
define('AUTHORITY_HOSPITAL_USER', 2);
define('AUTHORITY_HOSPITAL_PATIENT', 3);
define('AUTHORITY_OTHER', 9);

define('SESSION_TIME', 14400);

class HpAuthority
{
    private static $classAuthority = [
                'GetHospitalList' => AUTHORITY_HOSPITAL_USER, 
                'Login' => AUTHORITY_OTHER,
                
    ];
    
    public static function getClassAuthority($class)
    {
        if (array_key_exists($class, self::$classAuthority)) {
            return self::$classAuthority[$class];
        } else {
            HpLogger::writeCommonLog('Class authority type does not exist:' . $errorName);
            return self::$classAuthority[AUTHORITY_SUPER_ADMIN];
        }
    }
}
