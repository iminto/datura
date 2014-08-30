<?php

/**
 * 测试类
 */
class ftController extends Controller {

    public function indexAction() {
        $name = '杨指数';
        $this->assign('name', $name);
        $fruit = array('apple', 'pear', 'lemon');
        $this->assign('fruit', $fruit);
        var_dump('name1');
        $this->display();
    }

    public function helloAction() {
        echo 'ftController::helloAction', '<BR/>';
        $name = '杨指数';
        $this->assign('name', $name);
        var_dump($this->param);
        $this->display();
    }

    function _before_() {
    }

}

?>
