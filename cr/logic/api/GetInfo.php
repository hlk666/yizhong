<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetInfo extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $checkRequired = HpValidate::checkRequiredParam(['table', 'field', 'value'], $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $info = Dbi::getDbi()->getInfo($this->param['table'], $this->param['field'], $this->param['value']);
        if (VALUE_DB_ERROR === $info) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($info)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        
        return array_merge($this->retSuccess, $info);
    }
}
