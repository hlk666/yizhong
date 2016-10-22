<?php
define('AUTHORITY_SUPER_ADMIN', 0);
define('AUTHORITY_HOSPITAL_ADMIN', 1);
define('AUTHORITY_HOSPITAL_USER', 2);
define('AUTHORITY_HOSPITAL_PATIENT', 3);
define('AUTHORITY_OTHER', 9);

class HpAuthority
{
    private static $classAuthority = [
                    /* part1 */
                    'AddHospital' => AUTHORITY_SUPER_ADMIN,
                    'AddUser' => AUTHORITY_HOSPITAL_ADMIN,
                    'EditHospital' => AUTHORITY_SUPER_ADMIN,
                    'EditUser' => AUTHORITY_SUPER_ADMIN,
                    'GetHospitalList' => AUTHORITY_HOSPITAL_USER,
                    'GetUserList' => AUTHORITY_HOSPITAL_USER,
                    'AddHospitalRelation' => AUTHORITY_SUPER_ADMIN,
                    'DelHospitalRelation' => AUTHORITY_SUPER_ADMIN,
                    
                    /* part2 */
                    'Test' => AUTHORITY_OTHER,
                    'Index' => AUTHORITY_OTHER,
                    'Login' => AUTHORITY_OTHER,
                    'GetInfo' => AUTHORITY_HOSPITAL_USER,
                    'GetHolter' => AUTHORITY_HOSPITAL_USER,
                    'UploadImage' => AUTHORITY_HOSPITAL_USER,
                    
                    /* part3 */
                    'GetHospitalParent' => AUTHORITY_HOSPITAL_USER,
                    'AddCase' => AUTHORITY_HOSPITAL_USER,
                    'GetCase' => AUTHORITY_HOSPITAL_USER,
                    'EditCase' => AUTHORITY_HOSPITAL_USER,
                    
                    /* part4 */
                    'ApplyConsultation' => AUTHORITY_HOSPITAL_USER,
                    'GetConsultationApply' => AUTHORITY_HOSPITAL_USER,
                    'ReplyConsultation' => AUTHORITY_HOSPITAL_USER,
                    'GetConsultationReply' => AUTHORITY_HOSPITAL_USER,
                    'GetConsultationInfo' => AUTHORITY_HOSPITAL_USER,
                    'DelConsultation' => AUTHORITY_HOSPITAL_USER,
                    
                    /* part5 */
                    'ApplyReferral' => AUTHORITY_HOSPITAL_USER,
                    'GetReferralApply' => AUTHORITY_HOSPITAL_USER,
                    'ReplyReferral' => AUTHORITY_HOSPITAL_USER,
                    'GetReferralReply' => AUTHORITY_HOSPITAL_USER,
                    'ConfirmHospitalize' => AUTHORITY_HOSPITAL_USER,
                    'GetReferralInfo' => AUTHORITY_HOSPITAL_USER,
                    'Discharge' => AUTHORITY_HOSPITAL_USER,
                    'DelReferral' => AUTHORITY_HOSPITAL_USER,
                    
                    /* part6 */
                    'AddFollow' => AUTHORITY_HOSPITAL_USER,
                    'ReplyFollow' => AUTHORITY_HOSPITAL_USER,
                    'GetFollowReply' => AUTHORITY_HOSPITAL_USER,
                    'GetFollowInfo' => AUTHORITY_HOSPITAL_USER,
                    'DelFollow' => AUTHORITY_HOSPITAL_USER,
                    
                    /* part7 */
                    'GetCaseList' => AUTHORITY_HOSPITAL_USER,
                    'GetConsultationList' => AUTHORITY_HOSPITAL_USER,
                    'GetReferralList' => AUTHORITY_HOSPITAL_USER,
                    'GetFollowList' => AUTHORITY_HOSPITAL_USER,
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
