<?php
require_once PATH_ROOT . 'logic/BaseLogicSite.php';
require_once PATH_ROOT . 'lib/db/DbiCRM.php';

class Index extends BaseLogicSite
{
    protected function execute()
    {
        if (isset($_POST['login'])) {
            if (false == HpValidate::checkRequired($_POST['user'])) {
                $this->display('请输入用户名。');
                return;
            }
            if (false == HpValidate::checkRequired($_POST['password'])) {
                $this->display('请输入密码。');
                return;
            }
            
            $pwd = md5($_POST['password']);
            $userInfo = DbiCRM::getDbi()->getUserInfo($_POST['user']);
            if (VALUE_DB_ERROR === $userInfo) {
                $this->display(MESSAGE_DB_ERROR);
            }
            
            if (empty($userInfo)) {
                $this->display('用户名错误。');
                return;
            } elseif ($userInfo['password'] != $pwd) {
                $this->display('密码错误。');
                return;
            } else {
                $_SESSION['isLogin'] = true;
                $_SESSION['user'] = $userInfo['user_id'];
                if ($userInfo['type'] == AUTHORITY_ADMIN) {
                    header('location:user');
                } else {
                    header('location:schedule');
                }
            }
        } else {
            if (isset($_SESSION['login']) && true === $_SESSION['login']) {
                header('location:schedule');
                exit;
            }
            $this->display();
        }
    }
    private function display($error = '')
    {
        $this->setTpl(__CLASS__);
    
        $model = ['error' => $error, 'subTitle' => '登录页面'];
        $this->setModel($model);
    }
}
