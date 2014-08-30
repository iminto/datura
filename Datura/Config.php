<?php
// 系统基本配置 
return array(
    'mode' => 'debug', // 应用程序模式，默认为调试模式
    'Filter' => true, // 是否过滤 $_GET、$_POST、$_COOKIE、$_FILES
    'XSS' => true, // 是否开启 XSS防范
    'SessionStart' => true, // 是否开启 SESSION
    'DebugPhp' => false, // 是否开启PHP运行报错信息
    'DebugSql' => false, // 是否开启源码调试Sql语句
    'CharSet' => 'utf-8', // 设置网页编码
    'default_controller' => 'index', // 默认的控制器名称
    'default_action' => 'index', // 默认的动作名称
    'UrlControllerName' => 'c', // 自定义控制器名称 例如: index.php?c=index
    'UrlActionName' => 'a', // 自定义方法名称 例如: index.php?c=index&a=Index
    'UrlGroupName'=>'g',//自定义分组名
    'import_file' => array(), // 已经载入的文件
    'init_class' => array(),
    'timezone' => 'Asia/Chongqing',
//数据库配置
    'db' => array(// 数据库连接配置
        'driver' => 'mysqli', // 驱动类型
        'host' => 'localhost', // 数据库地址
        'port' => 3306, // 端口
        'login' => 'root', // 用户名
        'password' => '123', // 密码
        'database' => 'test', // 库名称
        'prefix' => '', // 表前缀
        'persistent' => FALSE, // 是否使用长链接
        'charset'=>'UTF8',
        'dummy'=>1 //傻瓜模式
    ),
    'smtp' => array(//SMTP配置
        'AuthUsername' => 'yanq',
        'Server' => 'smtp.163.com',
        'port' => 25,
        'AuthPassword' => '123456',
        'format'=>0,
        'debug'=>1
    ),
    'db_driver_path' => '', // 自定义数据库驱动文件地址
    'viewMode'=>'php',//取值有HTML/PHP两种，HTML模板还是PHP原生模板
    'style' => 'bluesky',//模板风格
    'templateconf'=>array(//模板配置
        'suffix' => '.html', //设置模板文件的后缀
        'templateDir' => '', //模板所在的文件夹
        'compiledir' => '__cache__', //设置编译后存放的目录
        'left'=>'<!--',//左定界符，不建议使用{}，会和页面中的JS函数冲突
        'right'=>'-->',//右定界符
        'php_turn' => true, //是否支持原生态PHP代码
        'debug' => FALSE
    ),
    'exceptionLevel'=>4000,//异常码警戒线，值越小说明异常越重要，小于此值的异常一旦发生，程序将立即终止
);