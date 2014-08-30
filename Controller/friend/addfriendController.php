<?php

/*
 * @Author: Waitfox@qq.com
 * @Date: 2013-7-24 22:27:33
 * @Desc: addFriendController
 */

class addfriendController extends Controller{
    public function indexAction(){
        echo 'I am in addFriendController.IndexAction','<BR />';
        $name='柳岩';
        $this->assign('name', $name);
        $this->display();
    }
}
