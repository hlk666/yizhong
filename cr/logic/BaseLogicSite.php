<?php
require PATH_ROOT . 'vendor/smarty-3.1.29/libs/Smarty.class.php';

class BaseLogicSite
{
    private $_smarty = null;
    private $_tpl;
    protected $model = array();
    
    public function __construct()
    {
        $this->_smarty = new Smarty();
        $this->_smarty->template_dir = PATH_ROOT . 'template';
        $this->_smarty->compile_dir = PATH_ROOT . 'runtime';
        $this->_smarty->left_delimiter = '{';
        $this->_smarty->right_delimiter = '}';
    }
    
    protected function validate()
    {
        //for override
    }
    protected function setTpl($tpl)
    {
        $this->_tpl = 'site' . DIRECTORY_SEPARATOR . $tpl . '.tpl';
    }
    
    /**
     * if override by child class, must call parent::setModel() first.
     */
    protected function setModel(array $model = array())
    {
        $this->model['domain'] = URL_ROOT;
        $this->model['header'] = 'header.tpl';
        $this->model['footer'] = 'footer.tpl';
        $this->model['title'] = '默认标题';
        $this->model['keywords'] = '默认关键字';
        $this->model['description'] = '默认描述';
        
        if (isset($_SESSION['isLogin']) && true === $_SESSION['isLogin']) {
            $this->model['isLogin'] = true;
        } else {
            $this->model['isLogin'] = false;
        }
        
        foreach ($model as $key => $value) {
            $this->model[$key] = $value;
        }
    }
    
    public function run()
    {
        //session_start();
        
        $this->validate();
        $this->execute();
        
        if (empty($this->model)) {
            $this->setModel();
        }
        
        if (!file_exists(PATH_ROOT . 'template' . DIRECTORY_SEPARATOR . $this->_tpl)) {
            HpLogger::writeCommonLog('tpl file not exist : ' . $this->_tpl);
            $this->_tpl = 'default.tpl';
        }
        
        $this->_smarty->assign($this->model);
        $this->_smarty->display($this->_tpl);
    }
}
