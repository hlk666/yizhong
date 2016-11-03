<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class EditCase extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['case_id', 'name', 'sex', 'birth_year', 'tel', 'diagnosis', 'info', 
                        'img_cbc', 'img_myocardial_markers', 'img_serum_electrolytes', 
                        'img_echocardiography', 'img_ecg', 'img_holter'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['case_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if ($this->param['sex'] != '1' && $this->param['sex'] != '2') {
            return HpErrorMessage::getError(ERROR_PARAM_RANGE, 'sex.');
        }
        
        $ret = HpValidate::checkBirthYear($this->param['birth_year']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_RANGE, 'birth_year.');
        }
        
        $ret = HpValidate::checkPhoneNo($this->param['tel']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_PHONE);
        }
        
        $ret = Dbi::getDbi()->existedCase($this->param['case_id']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_NOT_EXIST_ID);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $ret = Dbi::getDbi()->editCase($this->param['case_id'], $this->param['name'], $this->param['sex'], 
                $this->param['birth_year'], $this->param['tel'], $this->param['diagnosis'], $this->param['info'], 
                $this->param['img_cbc'], $this->param['img_myocardial_markers'], $this->param['img_serum_electrolytes'], 
                $this->param['img_echocardiography'], $this->param['img_ecg'], $this->param['img_holter']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
