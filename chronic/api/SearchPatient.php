<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class SearchPatient extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        if (empty($this->param['name']) 
                && empty($this->param['identity_card'])
                && empty($this->param['birth_year'])
                && empty($this->param['sex'])
                && empty($this->param['tel'])
                && empty($this->param['address'])
                && empty($this->param['hospitalization'])) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $name = isset($this->param['name']) ? $this->param['name'] : null;
        $identityCard = isset($this->param['identity_card']) ? $this->param['identity_card'] : null;
        $birthYear = isset($this->param['birth_year']) ? $this->param['birth_year'] : null;
        $sex = isset($this->param['sex']) ? $this->param['sex'] : null;
        $tel = isset($this->param['tel']) ? $this->param['tel'] : null;
        $address = isset($this->param['address']) ? $this->param['address'] : null;
        $hospitalization = isset($this->param['hospitalization']) ? $this->param['hospitalization'] : null;
        
        $patient = Dbi::getDbi()->searchPatient($name, $identityCard, $birthYear, $sex, $tel, $address, $hospitalization);
        if (VALUE_DB_ERROR === $patient) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($patient)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        $this->retSuccess['patient'] = $patient;
        return $this->retSuccess;
    }
}
