<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class EditChronic extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['id', 'name', 'level', 'parent_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedChronic($this->param['id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'id.');
        }
        
        if (Dbi::getDbi()->existedChronic($this->param['parent_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_EXISTED);
        }
        
        $checkRange = HpValidate::checkRange(['level'], $this->param, ['1', '2']);
        if (true !== $checkRange) {
            return $checkRange;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $ret = Dbi::getDbi()->editChronic($this->param['id'], $this->param['name'], $this->param['level'], $this->param['parent_id']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
