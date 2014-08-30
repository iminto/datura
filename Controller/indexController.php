<?php

class indexController extends Controller {

    function indexAction() {
        $imgUrl = url('index', 'hi');
        $ret1 = $this->db()->update('article', array('title' => '哈哈欢迎你6', 'type' => 5), 'id=3');
        var_dump($ret1);
//        $ret2 = $this->db()->insert('article', array('title' =>" '哈哈欢迎你<>6'", 'type' => 5));
//        var_dump($ret2);
        $ret = $this->db()->getAll("SELECT * FROM `article` ");
        $this->db()->tt("UPDATE `article` set content='world' ");
        $oneRecord=$this->db()->getOne("SELECT * FROM `article` WHERE type>5");
        var_dump($oneRecord);
        $this->assign('hello', $imgUrl)->assign('site', 'Datual 框架演示');
        $this->assign('version', '0.02');
        $this->assign('article', $ret);
        $this->display();
    }

    function hiAction() {
        Image::buildImageVerify(4);
    }

    function _after_() {
        
    }

}

?>
