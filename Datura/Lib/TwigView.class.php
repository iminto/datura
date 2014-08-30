<?php
/**
 * 引入twig模板引擎
 */
require_once _ROOT . 'Lib/Twig/Autoloader.php';
Twig_Autoloader::register();
class TwigView {
    public $twig;
    private $value = array();
    public function __construct() {
        $loader = new Twig_Loader_Filesystem(_VIEW);
        $this->twig=new Twig_Environment($loader, array(
            'cache' => _TEMPLATE_CACHE,
            'debug'=>$GLOBALS['_config']['templateconf']['debug'],
        ));
    }
    
    /**
     * 模板变量赋值
     * @param type $key
     * @param type $value
     */
    public function assign($key, $value) {
        $this->value[$key] = $value;
    }
    
    /**
     * 展示模板
     * @param type $file
     */
    public function display($tpl){
        $output = $this->twig->render($tpl.$GLOBALS['_config']['templateconf']['suffix'],$this->value);
        echo $output;
    }
    
    
}

?>
