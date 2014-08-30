<?php

/**
 * Description of mail
 * @author Administrator
 */
class EmailController extends Controller {

    public function indexAction() {
        import('Helper.Mail');
        $mail = new Mail($GLOBALS['_config']['smtp']);
        $mail->setFrom('lengfeng1601@163.com')->setTo('waitfox@qq.com')
                ->setSubject('测试邮件，来自冷风')
                ->setContent('哈哈哈哈<strong>粗体啊</strong>')
                ->IsHtml(TRUE)
                ->sendMail();
        echo '邮件发送完毕，请查收';
    }

}

?>
