<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddCase extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $ret = isset($this->param['hospital_id']) ? HpValidate::checkRequired($this->param['hospital_id']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'hospital_id.');
        }
        
        if (false === is_numeric($this->param['hospital_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'hospital_id.');
        }
        
        $ret = isset($this->param['name']) ? HpValidate::checkRequired($this->param['name']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'name');
        }
        
        $ret = isset($this->param['sex']) ? HpValidate::checkRequired($this->param['sex']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'sex');
        }
        
        $ret = isset($this->param['birth_year']) ? HpValidate::checkRequired($this->param['birth_year']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'birth_year');
        }
        
        $ret = isset($this->param['tel']) ? HpValidate::checkRequired($this->param['tel']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'tel');
        }
        
        $ret = isset($this->param['diagnosis']) ? HpValidate::checkRequired($this->param['diagnosis']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'diagnosis');
        }
        
        $ret = isset($this->param['info']) ? HpValidate::checkRequired($this->param['info']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'info');
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
        
        return true;
    }
    
    protected function execute()
    {
        $imgCBC = isset($this->param['img_cbc']) ? $this->param['img_cbc'] : '';
        $imgMyocardialMarkers = isset($this->param['img_myocardial_markers']) ? $this->param['img_myocardial_markers'] : '';
        $imgSerumElectrolytes = isset($this->param['img_serum_electrolytes']) ? $this->param['img_serum_electrolytes'] : '';
        $imgEchocardiography = isset($this->param['img_echocardiography']) ? $this->param['img_echocardiography'] : '';
        $imgEcg = isset($this->param['img_ecg']) ? $this->param['img_ecg'] : '';
        $imgHolter = isset($this->param['img_holter']) ? $this->param['img_holter'] : '';
        
        $ret = Dbi::getDbi()->addCase($this->param['name'], $this->param['sex'], $this->param['birth_year'], $this->param['tel'], 
                $this->param['diagnosis'], $this->param['info'], $imgCBC, $imgMyocardialMarkers, $imgSerumElectrolytes, 
                $imgEchocardiography, $imgEcg, $imgHolter);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
