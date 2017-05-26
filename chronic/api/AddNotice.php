<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/util/HpNoticeQuery.php';

class AddNotice extends BaseApi
{
    private $dataList = array();
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['type', 'id', 'data'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $this->dataList = $this->getStructalDataWithHeader($this->param['data']);
        if (empty($this->dataList)) {
            return HpErrorMessage::getError(ERROR_PARAM_FORMAT, 'plan_text.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $notice = new HpNoticeQuery($this->param['type'], $this->param['id']);
        $messages = $notice->set($this->dataList);
        return $this->retSuccess;
    }
}
