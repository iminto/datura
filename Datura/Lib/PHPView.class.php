<?php

/*
 * PHP原生视图，对HTML模板引擎的补充，可满足特殊需求
 */

class PHPView {

    private static $init;
    //赋值变量
    private $value = array();

    public static function init() {
        if (empty(self::$init)) {
            self::$init = new self;
        }
        return self::$init;
    }

    /*
     * 变量赋值
     */

    public function assign($key, $value) {
        $this->value[$key] = $value;
    }

    /*
     * 视图显示
     */
    public function display($view) {
        extract($this->value);
        include _VIEW.$view. '.php';
    }

}

?>
