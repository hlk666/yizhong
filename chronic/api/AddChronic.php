<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddChronic extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['name', 'level', 'parent_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        if (Dbi::getDbi()->existedChronicByName($this->param['name'])) {
            return HpErrorMessage::getError(ERROR_DATA_EXISTED);
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
        
        $chronicId = Dbi::getDbi()->addChronic($this->param['name'], $this->param['level'], $this->param['parent_id']);
        if (VALUE_DB_ERROR === $chronicId) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        $this->retSuccess['chronic_id'] = $chronicId;
        return $this->retSuccess;
    }
}
