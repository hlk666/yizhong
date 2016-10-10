<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/tool/HpUpload.php';

class UploadImage extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $ret = isset($this->param['data']) ? HpValidate::checkRequired($this->param['data']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_UPLOAD_NO_DATA);
        }
        
        $ret = isset($this->param['name']) ? HpValidate::checkRequired($this->param['name']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'name');
        }
        
        $names = ['cbc', 'myocardial_markers', 'serum_electrolytes', 'echocardiography', 'ecg'];
        if (!in_array($this->param['name'], $names)) {
            return HpErrorMessage::getError(ERROR_UPLOAD_NAME);
        }
        
        $suffixs = ['jpg', 'png', 'gif', 'jpeg'];
        if (isset($this->param['suffix']) && !in_array($this->param['suffix'], $suffixs)) {
            return HpErrorMessage::getError(ERROR_UPLOAD_SUFFIX);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $suffix = isset($this->param['suffix']) ? $this->param['suffix'] : null;
        $imgUrl = HpUpload::uploadImage($this->param['data'], $this->param['name'], $suffix);
        if (false === $imgUrl) {
            return HpErrorMessage::getError(ERROR_UPLOAD_FAIL);
        }
        
        $this->retSuccess['url'] = $imgUrl;
        return $this->retSuccess;
    }
}
