<?php
require_once PATH_ROOT . 'logic/BaseLogicSite.php';
require_once PATH_ROOT . 'logic/Back.php';
require_once PATH_ROOT . 'lib/db/DbiCRM.php';

class DeleteSchedule extends BaseLogicSite
{
    protected function execute()
    {
        $back = new Back();
        if (!isset($_GET['id']) || false == DbiCRM::getDbi()->existedSchedule($_GET['id'])) {
            $back->display('该条数据不存在或者已经被删除，请确认ID。');
            return;
        }
        $ret = DbiCRM::getDbi()->deleteSchedule($_GET['id']);
        if (VALUE_DB_ERROR === $ret) {
            $back->display('数据库错误，请重试或者联系管理员。');
            return;
        }
        $this->setTpl(__CLASS__);
        $model = ['subTitle' => '修改记录'];
        $this->setModel($model);
    }
}
