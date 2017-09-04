<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/tool/HpUpload.php';

class UploadFile extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['name', 'size'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $ret = isset($this->param['data']) ? HpValidate::checkRequired($this->param['data']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_UPLOAD_NO_DATA);
        }
        
        $checkRange = HpValidate::checkRange(['suffix'], $this->param, ['jpg', 'png', 'gif', 'jpeg', 'pdf']);
        if (true !== $checkRange) {
            return $checkRange;
        }
        
        $len = strlen($this->param['data']);
        if ($len != $this->param['size']) {
            return HpErrorMessage::getError(ERROR_PARAM_SIZE);
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
        
        $this->retSuccess['name'] = $this->param['name'];
        $this->retSuccess['url'] = $imgUrl;
        return $this->retSuccess;
    }
}
