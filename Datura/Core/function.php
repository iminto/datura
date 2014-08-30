<?php

/**
 * 改进的加载函数，实现只加载一次的功能
 * @staticvar array $_files
 * @param type $file
 */
function _requireOnce($file) {
    static $_files = array();
    if (!isset($_files[$file])) {
        if (is_file($file)) {
            require $file;
            $_files[$file] = true;
        } else {
            $_files[$file] = false;
        }
    }
}

/**
 * 任意位置引入类或普通PHP文件
 * 使用方法如下：import('Helper.Http') 引入Helper/Http.class.php
 * @param type $file
 * @param type $Base
 * @param type $ext
 */
function import($file, $Base = '', $ext = '.class.php') {
    $load = str_replace('.', DIRECTORY_SEPARATOR, $file);
    $load.=$ext;
    if (empty($Base)) {
        $Base = _DTR_PATH;
    } else {
        $Base = _ROOT . $Base . DIRECTORY_SEPARATOR;
    }
    $load = $Base . $load;
    _requireOnce($load);
}

/**
 * 从控制器和动作、参数构建URL，即反向路由
 * @param type $module
 * @param type $action
 * @param type $args
 * @return type
 */
function URL($module, $action, $args = array(), $group = '') {
    return DaturaRoute::url($module, $action, $args, $group);
}

/**
 * 数据过滤
 * @param type $str
 * @return type
 */
function safe_str($str) {
    if (!get_magic_quotes_gpc()) {
        if (is_array($str)) {
            foreach ($str as $key => $value) {
                $str[$key] = safe_str($value);
            }
        } else {
            $str = addslashes($str);
        }
    }
    return $str;
}

/**
 * 自定义的错误处理，生产环境建议在PHP.INI中处理，否则写日志可能对系统造成压力
 * @param type $errNo
 * @param type $errStr
 * @param type $errFile
 * @param type $errLine
 */
function baseErrorHandler($errNo, $errStr, $errFile, $errLine) {
    $err = '错误级别：' . $errNo . '|错误描述：' . $errStr;
    $err.='|错误所在文件：' . $errFile . '|错误所在行号：' . $errLine . "\r\n";
    file_put_contents(_ROOT . 'Data/errorLog.txt', $err, FILE_APPEND);
}

/**
 * 递归读一个目录下的文件
 * @staticvar array $dInfo
 * @param type $dirname
 * @return type
 */
function readFilerecursive($dirname) {
    static $dInfo = array();
    $dirname .= substr($dirname, -1) == "/" ? "" : "/";
    $dirInfo = glob($dirname . "*");
    foreach ($dirInfo as $info) {
        $dInfo[] = $info;
        if (is_dir($info)) {
            if (!is_readable($info)) {
                chmod($info, 0777);
            }
            readFilerecursive($info);
        }
    }
    return $dInfo;
}

/**
 * 递归读一个目录下的文件,带有后缀过滤
 * 使用方法：readFilerecursiveWithExtension("E:/data/php/datura/Class/",'.php');
 * @param type $path
 * @param type $extension
 * @return type
 */
function readFilerecursiveWithExtension($path, $extension) {
    //由于递归中有static变量，为了避免多次使用带来重复数据，先去重
    $fileArr = array_unique(readFilerecursive($path));
    $ret = array();
    foreach ($fileArr as $v) {
        if (!is_dir($v) && substr($v, 0 - strlen($extension)) == $extension) {
            $ret[] = $v;
        }
    }
    return $ret;
}

/**
 * 读取配置信息，使用最简单高效的方式
 * @param String $key
 */
function Config($key='') {
    $config =require _DTR_PATH . 'Config.php';
    if (empty($key)) {
        return $config;
    }
    $ret=NULL;
    if(stripos($key,'.')>0){
        $keys=  explode('.', $key);
        $cnt=count($keys);
        if($cnt==2){
            $ret=$config[$keys[0]][$keys[1]];
        }elseif($cnt==3){
           $ret=$config[$keys[0]][$keys[1]][$keys[2]]; 
        }else{
           $ret=$config[$keys[0]][$keys[1]][$keys[2]][$keys[3]];  
        }
    }else{
       $ret=$config[$key]; 
    }
    return $ret;
}

class Mcrypt {

    public static function encode($code) {
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(Config('key')), $code, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    }

    public static function decode($code) {
        return mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(Config('key')), base64_decode($code), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND));
    }

}

?>
