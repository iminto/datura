<?php

/**
 * 框架核心之一-路由器
 */
class DaturaRoute {

    public $group;
    public $control;
    public $action;
    public $params;

    public function __construct() {
        
    }

    public function init() {
        $route = $this->getRequest();
        $this->group = $route['group'];
        $this->control = $route['controll'];
        $this->action = $route['action'];
        !empty($route['param']) && $this->params = $route['param'];
    }

    /**
     * 在这里可以对不同样式的URL进行分门别类的处理，目前只实现了对Path Uri方式和传统URL的解析
     * @return Array
     */
    public function getRequest() {
        $filter_param = array('<', '>', '"', "'", '%3C', '%3E', '%22', '%27', '%3c', '%3e');
        $uri = str_replace($filter_param, '', $_SERVER['REQUEST_URI']);
        $path = parse_url($uri);
        if (strpos($path['path'], 'index.php') == 0) {
            $urlR0 = $path['path']; //处理urlrewrite后的URL，此时不带index.php
        } else {
            $urlR0 = substr($path['path'], strlen('index.php') + 1);
        }
        $urlR = ltrim($urlR0, '/'); // 移除左边的/,得到最需要的URL
        if ($urlR == '') {
            $route = $this->parseTradition();
            return $route;
        }
        $reqArr = explode('/', $urlR);
        //现在需要去除空白，比如index.php/g//b，多个斜杠产生的空数组
        foreach ($reqArr as $key => $value) {
            if (empty($value)) {
                unset($reqArr[$key]);
            }
        }
        $cnt = count($reqArr);
        if (empty($reqArr) || empty($reqArr[0])) {
            $cnt = 0;
        }
        switch ($cnt) {
            case 0:
                $route['controll'] = $GLOBALS['_config']['default_controller'];
                $route['action'] = $GLOBALS['_config']['default_action'];
                $route['group'] = 'default';
                break;
            case 1:
                if (stripos($reqArr[0], ':')) {
                    $gc = explode(':', $reqArr[0]);
                    $route['group'] = $gc[0];
                    $route['controll'] = $gc[1];
                    $route['action'] = $GLOBALS['_config']['default_action'];
                } else {
                    $route['group'] = 'default';
                    $route['controll'] = $reqArr[0];
                    $route['action'] = $GLOBALS['_config']['default_action'];
                }
                break;
            default:
                if (stripos($reqArr[0], ':')) {
                    $gc = explode(':', $reqArr[0]);
                    $route['group'] = $gc[0];
                    $route['controll'] = $gc[1];
                    $route['action'] = $reqArr[1];
                } else {
                    $route['group'] = 'default';
                    $route['controll'] = $reqArr[0];
                    $route['action'] = $reqArr[1];
                }
                for ($i = 2; $i < $cnt; $i++) {
                    $route['param'][$reqArr[$i]] = isset($reqArr[++$i]) ? $reqArr[$i] : '';
                }
                break;
        }
        //需要处理query字符串了
        if (!empty($path['query'])) {
            parse_str($path['query'], $routeQ);
            if (empty($route['param'])) {
                $route['param'] = array();
            }
            $route['param']+=$routeQ;
        }
        //注意，需要初始化param数组      
        return $route;
    }

    /**
     * @todo 解析传统的GET方式
     * @param type $uri
     * @return type
     */
    public function parseTradition() {
        $route = array();
        $_GET = safe_str($_GET);
        $_REQUEST = safe_str($_REQUEST);
        if (!isset($_GET[$GLOBALS['_config']['UrlGroupName']])) {
            $_GET[$GLOBALS['_config']['UrlGroupName']] = '';
        }
        if (!isset($_GET[$GLOBALS['_config']['UrlControllerName']])) {
            $_GET[$GLOBALS['_config']['UrlControllerName']] = '';
        }
        if (!isset($_GET[$GLOBALS['_config']['UrlActionName']])) {
            $_GET[$GLOBALS['_config']['UrlActionName']] = '';
        }
        $route['group'] = $_GET[$GLOBALS['_config']['UrlGroupName']];
        $route['controll'] = $_GET[$GLOBALS['_config']['UrlControllerName']];
        $route['action'] = $_GET[$GLOBALS['_config']['UrlActionName']];
        unset($_GET[$GLOBALS['_config']['UrlGroupName']]);
        unset($_GET[$GLOBALS['_config']['UrlControllerName']]);
        unset($_GET[$GLOBALS['_config']['UrlActionName']]);
        $route['param'] = $_GET; //如果要获得最原始的数据，可以使用$_REQUEST
        if ($route['group'] == NULL) {
            $route['group'] = 'default';
        }
        if ($route['controll'] == NULL) {
            $route['controll'] = $GLOBALS['_config']['default_controller'];
        }
        if ($route['action'] == NULL) {
            $route['action'] = $GLOBALS['_config']['default_action'];
        }
        return $route;
    }

    /**
     * 反向路由
     * @param type $module
     * @param type $action
     * @param type $args
     */
    public static function url($module, $action, $args = array(), $group = '') {
        $protocol=  isset($_SERVER['HTTPS'])&&!empty($_SERVER['HTTPS'])?'https':'http';
        if (empty($group)) {
            $args['module'] = $module;
            $args['action'] = $action;
            $url = $protocol.'://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '/' . implode('/', $args);
        } else {
            $args['action'] = $action;
            $url = $protocol.'://' .  $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '/' . $group . ':' . $module . '/' . implode('/', $args);
        }
        return $url;
    }

    public static function redirect($url) {
        if (is_string($url)) {
            if (!headers_sent()) {
                header("Location:" . $url);
                exit();
            } else {
                $redirect = '<meta http-equiv="Refresh" contect="0;url=' . $url . '">';
                exit($redirect);
            }
        } else {
            
        }
    }

}
