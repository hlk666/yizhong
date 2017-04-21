<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddDoctor extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['login_name', 'real_name', 'password', 'type', 'tel', 'department_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['type', 'department_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        $checkRange = HpValidate::checkRange(['type'], $this->param, ['0', '1', '2', '3']);
        if (true !== $checkRange) {
            return $checkRange;
        }
        
        if (false === Dbi::getDbi()->existedDepartment($this->param['department_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'department_id.');
        }
        
        if (true === Dbi::getDbi()->existedDoctor($this->param['login_name'])) {
            return HpErrorMessage::getError(ERROR_USER_NAME_USED);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $phone = isset($this->param['phone']) ? $this->param['phone'] : '0';
        $ret = Dbi::getDbi()->addDoctor($this->param['login_name'], $this->param['real_name'], 
                md5($this->param['login_name']), $this->param['type'], $this->param['tel'], $phone, $this->param['department_id']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
