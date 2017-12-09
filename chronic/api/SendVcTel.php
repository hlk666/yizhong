<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';
require_once PATH_ROOT . 'lib/util/HpVerificationCode.php';
require_once PATH_ROOT . 'lib/tool/HpShortMessageService.php';

class SendVcTel extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['tel'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $vc = HpVerificationCode::createFileNumericVC('Tel' . $this->param['tel']);
        if (empty($vc)) {
            return HpErrorMessage::getError(ERROR_OTHER);
        }
        
        $ret = HpShortMessageService::send($this->param['tel'], "您正在羿中医疗慢病系统注册，验证码是【 $vc 】。如果未进行该操作，请无视本消息。");
        if (false === $ret) {
            return HpErrorMessage::getError(ERROR_SHORT_MESSAGE);
        }
        
        return $this->retSuccess;
    }
}
