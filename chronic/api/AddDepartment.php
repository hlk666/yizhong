<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddDepartment extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['hospital_id', 'name', 'tel', 'login_name', 'real_name', 'password'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['hospital_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedHospital($this->param['hospital_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'hospital_id.');
        }
        
        if (true === Dbi::getDbi()->existedDoctor($this->param['login_name'])) {
            return HpErrorMessage::getError(ERROR_USER_NAME_USED);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $ret = Dbi::getDbi()->addDepartment($this->param['hospital_id'], $this->param['name'], $this->param['tel'],
                $this->param['login_name'], $this->param['real_name'], md5($this->param['password']));
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
