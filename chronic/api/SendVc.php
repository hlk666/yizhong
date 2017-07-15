<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';
require_once PATH_ROOT . 'lib/util/HpVerificationCode.php';
require_once PATH_ROOT . 'lib/tool/HpShortMessageService.php';

class SendVc extends BaseApi
{
    private $tel = null;
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['patient_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['patient_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedPatient($this->param['patient_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'patient_id.');
        }
        $patient = Dbi::getDbi()->getPatientInfo($this->param['patient_id']);
        if (VALUE_DB_ERROR === $patient) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($patient['tel'])) {
            return HpErrorMessage::getError(ERROR_TEL_EMPTY);
        }
        $this->tel = $patient['tel'];
        
        return true;
    }
    
    protected function execute()
    {
        $vc = HpVerificationCode::createFileNumericVC('Patient' . $this->param['patient_id']);
        if (empty($vc)) {
            return HpErrorMessage::getError(ERROR_OTHER);
        }
        
        $ret = HpShortMessageService::send($this->tel, "您正在修改管理科室，验证码是【 $vc 】。如果未进行该操作，请无视本消息。");
        if (false === $ret) {
            return HpErrorMessage::getError(ERROR_SHORT_MESSAGE);
        }
        
        return $this->retSuccess;
    }
}
