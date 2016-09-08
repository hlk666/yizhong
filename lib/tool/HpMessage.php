<?php
require_once PATH_ROOT . 'lib/db/DbiMaster.php';
require_once PATH_ROOT . 'lib/ShortMessageService.php';

class HpMessage
{
    private static $defaultEmptyPhone = '0';
    
    /**
     * send message to one or more telephone.
     * @param string $message message need to send.
     * @param string|null
     * @param array $phones
     */
    public static function sendTelMessage($message, $hospitalId = null, array $phones = array())
    {
        $telNumbers = array();
        
        if (null !== $hospitalId) {
            $hospital = DbiMaster::getDbi()->getHospitalInfo($hospitalId);
            if (VALUE_DB_ERROR !== $hospital && self::$defaultEmptyPhone != $hospital['sms_tel']) {
                $telNumbers[] = $hospital['sms_tel'];
            }
        }
        
        if (!empty($phones)) {
            $telNumbers = array_merge($telNumbers, $phones);
        }
        
        foreach ($telNumbers as $tel) {
            ShortMessageService::send($tel, $message);
        }
    }
}