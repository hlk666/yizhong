<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddPatient extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['name', 'birth_year', 'sex', 'tel', 'address', 'family_name', 'family_tel', 'department_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['identity_card', 'birth_year', 'department_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        $checkRange = HpValidate::checkRange(['sex'], $this->param, ['男', '女']);
        if (true !== $checkRange) {
            return $checkRange;
        }
        
        if (false === Dbi::getDbi()->existedDepartment($this->param['department_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'department_id.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $identityCard = isset($this->param['identity_card']) ? $this->param['identity_card'] : '0';
        $ethnic = isset($this->param['ethnic']) ? $this->param['ethnic'] : '0';
        $nativePlace = isset($this->param['native_place']) ? $this->param['native_place'] : '0';
        $hospitalization = isset($this->param['hospitalization']) ? $this->param['hospitalization'] : '0';
        $patientId = Dbi::getDbi()->addPatient($identityCard, $this->param['name'], $this->param['birth_year'], $this->param['sex'], 
                $this->param['tel'], $this->param['address'], $ethnic, $nativePlace, $hospitalization, 
                $this->param['family_name'], $this->param['family_tel'], $this->param['department_id']);
        if (VALUE_DB_ERROR === $patientId) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        $this->retSuccess['patient_id'] = $patientId;
        return $this->retSuccess;
    }
}
