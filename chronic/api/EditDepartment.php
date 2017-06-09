<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class EditDepartment extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['department_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['department_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedDepartment($this->param['department_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'department_id.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $data = array();
        if (isset($this->param['name'])) {
            $data['name'] = $this->param['name'];
        }
        if (isset($this->param['tel'])) {
            $data['tel'] = $this->param['tel'];
        }
        if (empty($data)) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED);
        }
        
        $ret = Dbi::getDbi()->editDepartment($this->param['department_id'], $data);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
