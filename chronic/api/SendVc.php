<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';
require_once PATH_ROOT . 'lib/util/HpVerificationCode.php';

class SendVc extends BaseApi
{
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
        
        return true;
    }
    
    protected function execute()
    {
        $vc = HpVerificationCode::createFileNumericVC('Patient' . $this->param['patient_id']);
        if (empty($vc)) {
            return HpErrorMessage::getError(ERROR_OTHER);
        }
        $this->retSuccess['vc'] = $vc;
        return $this->retSuccess;
    }
}
