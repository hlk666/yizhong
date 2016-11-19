<?php
require_once PATH_ROOT . 'logic/BaseLogicSite.php';
require_once PATH_ROOT . 'lib/db/DbiCRM.php';

class AddSchedule extends BaseLogicSite
{
    protected function execute()
    {
        if (isset($_POST['submit'])) {
            if (false == HpValidate::checkRequired($_POST['hospital'])) {
                $this->display(false, '请选择医院(如果没有医院列表，请先创建医院)。');
                return;
            }
            if (false == HpValidate::checkRequired($_POST['stage'])) {
                $this->display(false, '请输入"开发阶段"。');
                return;
            }
            if (false == HpValidate::checkRequired($_POST['progress'])) {
                $this->display(false, '请输入"最新进展"。');
                return;
            }
            if (false == HpValidate::checkRequired($_POST['info'])) {
                $this->display(false, '请输入"相关意见或建议"。');
                return;
            }
            
            $ret = DbiCRM::getDbi()->addSchedule($_SESSION['user'], 
                    $_POST['hospital'], $_POST['stage'], $_POST['progress'], $_POST['info']);
            if (VALUE_DB_ERROR === $ret) {
                $this->display(true, '数据库错误，请重试或者联系管理员。');
                return;
            }
            $this->display(true);
        } else {
            $this->display(false);
        }
    }
    private function display($postFlag, $error = '', $paramModel = array())
    {
        $this->setTpl(__CLASS__);
        $model = ['subTitle' => '添加记录'];
        
        if (!$postFlag) {
            $user = DbiCRM::getDbi()->getUserInfoById($_SESSION['user']);
            if (VALUE_DB_ERROR === $user) {
                display_error_page('数据库错误。');
            }
            if (empty($user)) {
                display_error_page('数据一致性错误。');
            }
            
            if (AUTHORITY_ADMIN == $user['type']) {
                $hospital = DbiCRM::getDbi()->getHospitalList();
            } else {
                $hospital = DbiCRM::getDbi()->getHospitalList($_SESSION['user']);
            }
            if (VALUE_DB_ERROR === $user) {
                display_error_page('数据库错误。');
            }
            $model['hospital'] = $hospital;
            $model['postSuccess'] = false;
            $model['error'] = $error;
        } else {
            $model['postSuccess'] = true;
        }
        
        $this->setModel($model);
    }
}
