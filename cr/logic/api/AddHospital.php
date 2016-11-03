<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddHospital extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $ret = isset($this->param['hospital_name']) ? HpValidate::checkRequired($this->param['hospital_name']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'hospital_name');
        }
        
        $ret = isset($this->param['address']) ? HpValidate::checkRequired($this->param['address']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'address');
        }
        
        $ret = isset($this->param['tel']) ? HpValidate::checkRequired($this->param['tel']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'tel');
        }
        
        $ret = isset($this->param['sms_tel']) ? HpValidate::checkRequired($this->param['sms_tel']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'sms_tel');
        }
        
        $ret = HpValidate::checkPhoneNo($this->param['sms_tel']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_PHONE);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $ret = Dbi::getDbi()->addHospital($this->param['hospital_name'], 
                $this->param['tel'], $this->param['address'], $this->param['sms_tel']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
