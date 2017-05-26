<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddConsultationReply extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['consultation_id', 'department_id', 'doctor_id', 'diagnosis', 'advice'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['consultation_id', 'department_id', 'doctor_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedConsultation($this->param['consultation_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'consultation_id.');
        }
        
        if (false === Dbi::getDbi()->existedDepartment($this->param['department_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'department_id.');
        }
        
        if (false === Dbi::getDbi()->existedDoctorById($this->param['doctor_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'doctor_id.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $ret = Dbi::getDbi()->addConsultationReply($this->param['consultation_id'], 
                $this->param['doctor_id'], $this->param['diagnosis'], $this->param['advice']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
