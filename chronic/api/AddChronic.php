<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddCase extends BaseApi
{
    private $chronic = array();
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['name'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        if (Dbi::getDbi()->existedChronicByName($this->param['name'])) {
            return HpErrorMessage::getError(ERROR_DATA_EXISTED);
        }
        
        return true;
    }
    
    protected function execute()
    {
        
        $chronicId = Dbi::getDbi()->addChronic($this->param['name']);
        if (VALUE_DB_ERROR === $chronicId) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        $this->retSuccess['chronic_id'] = $chronicId;
        return $this->retSuccess;
    }
}
