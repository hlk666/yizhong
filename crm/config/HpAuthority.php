<?php
define('AUTHORITY_ADMIN', '0');
define('AUTHORITY_USER', '1');
define('AUTHORITY_OTHER', '9');

class HpAuthority
{
    private static $classAuthority = [
                    'Login' => AUTHORITY_OTHER,
                    'User' => AUTHORITY_ADMIN,
                    'AddUser' => AUTHORITY_ADMIN,
                    'Schedule' => AUTHORITY_USER,
                    'AddSchedule' => AUTHORITY_USER,
                    'EditSchedule' => AUTHORITY_USER,
                    'DelSchedule' => AUTHORITY_USER,
                    'Hospital' => AUTHORITY_USER,
                    'AddHospital' => AUTHORITY_USER,
                    'EditHospital' => AUTHORITY_USER,
                    'Agency' => AUTHORITY_USER,
                    'AddAgency' => AUTHORITY_USER,
                    'EditAgency' => AUTHORITY_USER,
    ];
    
    public static function getClassAuthority($class)
    {
        if (array_key_exists($class, self::$classAuthority)) {
            return self::$classAuthority[$class];
        } else {
            HpLogger::writeCommonLog('Class authority type does not exist:' . $class);
            return AUTHORITY_SUPER_ADMIN;
        }
    }
}
