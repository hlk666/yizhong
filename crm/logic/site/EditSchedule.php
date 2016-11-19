<?php
require_once PATH_ROOT . 'logic/BaseLogicSite.php';
require_once PATH_ROOT . 'lib/db/DbiCRM.php';

class EditSchedule extends BaseLogicSite
{
    private $_scheduleId;
    protected function execute()
    {
        if (isset($_POST['submit'])) {
            if (false == HpValidate::checkRequired($_POST['schedule_id'])) {
                $this->display(false, '修改的记录不存在。');
                return;
            }
            $this->_scheduleId = $_POST['schedule_id'];
            if (false == HpValidate::checkRequired($_POST['hospital'])) {
                $this->display(false, '请选择医院。');
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
            
            $ret = DbiCRM::getDbi()->editSchedule($this->_scheduleId, 
                    $_POST['hospital'], $_POST['stage'], $_POST['progress'], $_POST['info']);
            if (VALUE_DB_ERROR === $ret) {
                $this->display(true, '数据库错误，请重试或者联系管理员。');
                return;
            }
            $this->display(true);
        } else {
            if (!isset($_GET['id'])) {
                display_error_page('访问错误：没有记录ID。');
            }
            $this->_scheduleId = $_GET['id'];
            $this->display(false);
        }
    }
    private function display($postFlag, $error = '', $paramModel = array())
    {
        $this->setTpl(__CLASS__);
        $model = ['subTitle' => '修改记录'];
        
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
            
            $scheduleInfo = DbiCRM::getDbi()->getScheduleInfo($this->_scheduleId);
            if (VALUE_DB_ERROR === $scheduleInfo) {
                display_error_page('数据库错误。');
            }
            
            $model['schedule_id'] = $scheduleInfo['schedule_id'];
            $model['hospital_id'] = $scheduleInfo['hospital_id'];
            $model['stage'] = $scheduleInfo['stage'];
            $model['progress'] = $scheduleInfo['progress'];
            $model['info'] = $scheduleInfo['info'];
            
            $model['hospital'] = $hospital;
            $model['postSuccess'] = false;
            $model['error'] = $error;
        } else {
            $model['postSuccess'] = true;
        }
        $this->setModel($model);
    }
}
