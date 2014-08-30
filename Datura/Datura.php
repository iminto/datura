<?php
/**
 * Datura（中文名为“曼陀罗花”) PHP 框架 0.02
 */
header("Content-type:text/html;charset=utf-8");
define('_VERSION', '0.1'); // 当前框架版本
define('_CONTROLL', _ROOT . 'Controller' . DIRECTORY_SEPARATOR); // 用户控制器目录
define('_MODEL', _ROOT . 'Model' . DIRECTORY_SEPARATOR); // 用户模型目录
define('_CLASS', _ROOT . 'Class' . DIRECTORY_SEPARATOR); // 用户类目录
define('_VIEW', _ROOT . 'View' . DIRECTORY_SEPARATOR); // 用户视图模板目录
define('_CORE', _DTR_PATH . 'Core' . DIRECTORY_SEPARATOR);//框架核心目录
require _CORE . 'function.php';
$GLOBALS['_config'] = require _DTR_PATH . 'Config.php';//同时提供Config函数，方便再自己的控制器里获取配置。这里为了效率，使用全局变量
define('_TEMPLATE_CACHE',_ROOT . 'View' . DIRECTORY_SEPARATOR.$GLOBALS['_config']['templateconf']['compiledir']. DIRECTORY_SEPARATOR);
date_default_timezone_set($GLOBALS['_config']['timezone']);
require _CORE . 'Base.class.php';
require _CORE . 'DaturaRoute.php';
require _CORE . 'DaturaControll.php';
require _CORE . 'BaseException.php';
session_start();
// 根据配置文件进行一些全局变量的定义
if ('debug' == Config('mode')) {
    // 如果是调试模式，打开警告输出
    if (substr(PHP_VERSION, 0, 3) == "5.5") {
        error_reporting(E_ALL);
    } else {
        error_reporting(E_ALL| E_STRICT);
    }
} else {
    //生产模式关闭所有错误报告，将错误报告重定向到文件
    error_reporting(0);
    set_error_handler('baseErrorHandler');
    
}
class Loader{
    public static function loadClass(){
    }
    public static function loadLibClass($class){       
        import('Lib.driver.db.'.$class);
        import('Helper.'.$class);
    }
}
spl_autoload_register(array('Loader','loadLibClass'));
/**
 * 核心，负责路由分发与调度
 */
class Datura {
    protected $control;//控制器名
    protected $action;//模块名
    protected $param;//参数
    protected $group;//分组名
    public static $IMPORT=array();

    public function __construct() {
    }

    public function run() {
        try{
        $this->route();
        $this->runControll();
        }catch(BaseException $e){
            $e->errorMessage();
        }
        
    }

    public function route() {
        $router = new DaturaRoute;
        $router->init();
        $this->control=  strtolower($router->control);
        $this->action = strtolower($router->action);
        $this->group=  strtolower($router->group);
        $this->param = $router->params;
    }

    public function requireOnce($name) {
        if(!isset(self::$IMPORT[$name])){
            require $name;
            self::$IMPORT[$name]=$name;
        }         
    }
    
    public function runControll(){
        $controlName=$this->control.'Controller';
        $actionName= $this->action.'Action';
        //如果设置了分组，则去分组文件夹下寻找文件
        if(!empty($this->group)&&$this->group!='default'){
            $lfile=_CONTROLL.$this->group.DIRECTORY_SEPARATOR.$controlName.'.php';                  
        }else{
            $lfile=_CONTROLL.$controlName.'.php';
        }
        if(is_file($lfile)){
          $this->requireOnce($lfile);  
        }else{
           throw new BaseException('控制器文件不存在',1001); 
        }
        if(!class_exists($controlName,FALSE)){
            throw new BaseException(sprintf('控制器类 %s 不存在',  $controlName),1002);
        }
        $methods = get_class_methods($controlName);
        if(!in_array($actionName, $methods, TRUE)){
           throw new BaseException(sprintf('方法名 %s 不存在',  $actionName),1003);
        }
        $GLOBALS['ACTION'][]=$this->action;
        $handler= new $controlName();// 实例控制器
        $handler->param=$this->param;
        if(in_array('_before_', $methods)){
            call_user_func(array($handler,'_before_')) ;
        }
	$handler-> {$actionName}();
        if(in_array('_after_', $methods)){
            call_user_func(array($handler,'_after_')) ;
        }
    }

}
session_write_close();
