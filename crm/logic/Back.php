<?php
require_once PATH_ROOT . 'vendor/smarty-3.1.29/libs/Smarty.class.php';

class Back
{
    private $_smarty = null;
    private $_model = array();
    
    public function __construct()
    {
        $this->_smarty = new Smarty();
        $this->_smarty->template_dir = PATH_ROOT . 'template';
        $this->_smarty->compile_dir = PATH_ROOT . 'runtime';
        $this->_smarty->left_delimiter = '{';
        $this->_smarty->right_delimiter = '}';
    }
    
    public function display($message)
    {
        $this->_model['domain'] = URL_ROOT;
        $this->_model['header'] = 'header.tpl';
        $this->_model['footer'] = 'footer.tpl';
        $this->_model['title'] = '返回 ';
        $this->_model['keywords'] = '默认关键字';
        $this->_model['description'] = '默认描述';
        if (isset($_SESSION['isLogin']) && $_SESSION['isLogin']) {
            $this->_model['isLogin'] = true;
        } else {
            $this->_model['isLogin'] = false;
        }
        $this->_model['message'] = $message;
        $this->_model['subTitle'] = '返回';
        
        $this->_smarty->assign($this->_model);
        $this->_smarty->display('back.tpl');
        exit;
    }
}