<?php
/**
 * 自定义异常类，用以获取框架运期间的异常并记录下来
 */
class BaseException extends Exception {

    public function __toString() {
        
    }
    /**
     * 记录异常日志
     */
    protected function _Log() {
        $err = '异常信息：' . self::getMessage() . '|';
        $err.='异常码：' . self::getCode() . '|';
        $err.='异常发生时间：' . date('Y-m-d H:i:s') . '|';
        $err.='堆栈回溯:'.serialize(debug_backtrace()) . PHP_EOL;
        file_put_contents(_ROOT . 'Data/exceptionLog.txt', $err, FILE_APPEND);
    }
    /**
     * 打印简单的异常信息
     * @todo 需要格式化debug_backtrace()，展示更详细的的信息
     */
    public function errorMessage() {
        $errorMsg=self::getMessage();
        include(_DTR_PATH .'Lib/Exception.php');
        self::_Log();
        return 0;
        //如果错误级别较大，则终止程序运行
        if (self::getCode() < $GLOBALS['_config']['exceptionLevel'])
            exit;
    }

}

?>
