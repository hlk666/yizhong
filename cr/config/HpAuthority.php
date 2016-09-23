<?php
define('AUTHORITY_SUPER_ADMIN', 0);
define('AUTHORITY_HOSPITAL_ADMIN', 1);
define('AUTHORITY_HOSPITAL_USER', 2);
define('AUTHORITY_HOSPITAL_PATIENT', 3);
define('AUTHORITY_OTHER', 9);

class HpAuthority
{
    private static $classAuthority = [
                    'Test' => AUTHORITY_OTHER,
                    'AddHospital' => AUTHORITY_SUPER_ADMIN,
                    'GetHospitalList' => AUTHORITY_HOSPITAL_USER, 
                    'AddHospitalRelation' => AUTHORITY_SUPER_ADMIN,
                    'AddUser' => AUTHORITY_HOSPITAL_ADMIN,
                    'Login' => AUTHORITY_OTHER,
                    'AddCase' => AUTHORITY_HOSPITAL_USER,
                    'GetParentHospital' => AUTHORITY_HOSPITAL_USER,
                    'ApplyConsultation' => AUTHORITY_HOSPITAL_USER,
                    'GetConsultationApply' => AUTHORITY_HOSPITAL_USER,
                    'ReplyConsultation' => AUTHORITY_HOSPITAL_USER,
                    'GetConsultationReply' => AUTHORITY_HOSPITAL_USER,
                    'GetCase' => AUTHORITY_HOSPITAL_USER,
                    'ApplyReferral' => AUTHORITY_HOSPITAL_USER,
                    'GetReferralApply' => AUTHORITY_HOSPITAL_USER,
                    'ReplyReferral' => AUTHORITY_HOSPITAL_USER,
                    'FinishReferral' => AUTHORITY_HOSPITAL_USER,
                    'GetReferralInfo' => AUTHORITY_HOSPITAL_USER,
                    'AddFollow' => AUTHORITY_HOSPITAL_USER,
                    'GetFollow' => AUTHORITY_HOSPITAL_USER,
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
