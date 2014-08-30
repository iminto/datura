<?php
/**
 * 控制器基类，是本框架核心之一，用于调度数据库和分发试图
 */
class Controller {

    private $db;
    private $view;
    private $control;
    private $action;
    
    /**
     * 构造函数，可根据参数来采用不同的模板引擎
     */
    public function __construct() {
        $viewMode=$GLOBALS['_config']['viewMode'];
        if('HTML'==$viewMode){
        import('Core.View');       
        $this->view=new View($GLOBALS['_config']['templateconf']);  
        }else{
         import('Lib.PHPView');       
        $this->view=new PHPView();
        }
        
    }
    /**
     * 重定向
     * @param type $url
     */
    public function _redirect($url){
        DaturaRoute::redirect($url);
    }
    /**
     * 赋值给模板
     * @param type $key
     * @param type $val
     * @return type
     */
    protected function assign($key,$val){        
        $this->view->assign($key, $val);
        return $this->view;
    }
    /**
     * 分派给模板处理
     * @param string $file
     */
    public  function display($file=""){
        if(func_num_args()==0){
        $this->control=get_called_class();
        $controlPath=substr($this->control,0,stripos($this->control,'Controller'));
        $this->action=$GLOBALS['ACTION'][0];
        $file=$GLOBALS['_config']['style']. DIRECTORY_SEPARATOR.$controlPath. DIRECTORY_SEPARATOR.$this->action;
        }
        $this->view->display($file);
    }
    /**
     * 调度DB
     * @param type $conf
     * @return type
     */
    public function db($conf=array()){
        if($conf==NULL){
            $conf=$GLOBALS['_config']['db'];
        }
        $this->db=DbMysqli::getInstance($conf);
        return $this->db;        
    }
    
    /**
     * 判断是否是通过Ajax发送来的请求，阻止一些潜在恶意操作
     * 根据HTTP协议的特点，此函数无法彻底判断是否是真实的Ajax请求。
     * @return boolean
     */
    public static function isAjax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            if ('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']))
                return true;
        }
        if (!empty($_REQUEST['ajax'])) return true;
        return false;
    }


}

?>
