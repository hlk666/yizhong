<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/util/HpNoticeQuery.php';

class GetNotice extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $notice = new HpNoticeQuery('department', $this->param['id']);
        $messages = $notice->getAll();
        $this->retSuccess['notice'] = $messages;
        return $this->retSuccess;
    }
}
