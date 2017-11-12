<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class CheckPatient extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        if (isset($this->param['identity_card']) 
                && true === Dbi::getDbi()->existedPatientIdentityCard($this->param['identity_card'])) {
            return HpErrorMessage::getError(ERROR_DATA_EXISTED, 'identity_card.');
        }
        
        if (isset($this->param['tel'])
                && true === Dbi::getDbi()->existedPatientTel($this->param['tel'])) {
            return HpErrorMessage::getError(ERROR_DATA_EXISTED, 'tel.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        return $this->retSuccess;
    }
}
