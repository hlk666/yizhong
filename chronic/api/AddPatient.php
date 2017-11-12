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
        
        $required = ['name', 'identity_card', 'birth_year', 'sex', 'tel', 'department_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['birth_year', 'department_id'], $this->param);
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
        $address = isset($this->param['address']) ? $this->param['address'] : '';
        $familyName = isset($this->param['family_name']) ? $this->param['family_name'] : '';
        $familyTel = isset($this->param['family_tel']) ? $this->param['family_tel'] : '';
        $identityCard = isset($this->param['identity_card']) ? $this->param['identity_card'] : '0';
        $ethnic = isset($this->param['ethnic']) ? $this->param['ethnic'] : '0';
        $nativePlace = isset($this->param['native_place']) ? $this->param['native_place'] : '0';
        $hospitalization = isset($this->param['hospitalization']) ? $this->param['hospitalization'] : '0';
        $height = isset($this->param['height']) ? $this->param['height'] : '';
        $weight = isset($this->param['weight']) ? $this->param['weight'] : '';
        $job = isset($this->param['job']) ? $this->param['job'] : '';
        $education = isset($this->param['education']) ? $this->param['education'] : '';
        $patientId = Dbi::getDbi()->addPatient($identityCard, $this->param['name'], $this->param['birth_year'], $this->param['sex'], 
                $this->param['tel'], $address, $ethnic, $nativePlace, $hospitalization, 
                $familyName, $familyTel, $this->param['department_id'], $height, $weight, $job, $education);
        if (VALUE_DB_ERROR === $patientId) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        $this->retSuccess['patient_id'] = $patientId;
        return $this->retSuccess;
    }
}
