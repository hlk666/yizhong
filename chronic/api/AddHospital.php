<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddHospital extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['name', 'level', 'tel', 'area', 'province', 'city', 'address'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['level'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        $checkRange = HpValidate::checkRange(['level'], $this->param, ['1', '2', '3']);
        if (true !== $checkRange) {
            return $checkRange;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $ret = Dbi::getDbi()->addHospital($this->param['name'], $this->param['level'], $this->param['tel'], 
                $this->param['area'], $this->param['province'], $this->param['city'], $this->param['address']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
