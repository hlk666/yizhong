<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/util/HpNoticeQuery.php';

class DeleteNotice extends BaseApi
{
    private $dataList = array();
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['id', 'notice_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $notice = new HpNoticeQuery('department', $this->param['id']);
        $notice->delete($this->param['notice_id']);
        return $this->retSuccess;
    }
}
