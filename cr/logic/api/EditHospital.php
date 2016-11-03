<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class EditHospital extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['hospital_id', 'hospital_name', 'tel', 'address', 'sms_tel'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['hospital_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        $ret = HpValidate::checkPhoneNo($this->param['tel']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_PHONE);
        }
        $ret = HpValidate::checkPhoneNo($this->param['sms_tel']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_PHONE);
        }
        
        $ret = Dbi::getDbi()->existedHospital($this->param['hospital_id']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_NOT_EXIST_ID);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $ret = Dbi::getDbi()->editHospital($this->param['hospital_id'], 
                $this->param['hospital_name'], $this->param['tel'], $this->param['address'], $this->param['sms_tel']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
