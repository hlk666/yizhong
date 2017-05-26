<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class SearchDepartment extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        if (empty($this->param['name']) 
                && empty($this->param['level'])
                && empty($this->param['tel'])) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $name = isset($this->param['name']) ? $this->param['name'] : null;
        $tel = isset($this->param['tel']) ? $this->param['tel'] : null;
        
        $department = Dbi::getDbi()->searchDepartment($name, $tel);
        if (VALUE_DB_ERROR === $department) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($department)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        $this->retSuccess['department'] = $department;
        return $this->retSuccess;
    }
}
