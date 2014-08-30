<?php

class Cache_Driver_Xcache extends Cache {

    //xcache在清空缓存时需要获取权限
    private $auth_user = '';
    private $auth_pwd = '';

    public function __construct() {
        if (!extension_loaded('xcache')) {
            throw new BaseException('The xcache extension must be loaded !', 1201);
        }
    }

    protected function delete_value($key) {
        return xcache_unset($key);
    }

    protected function get_value($key) {
        return unserialize(xcache_get($key));
    }

    protected function set_value($key, $value, $expires = 0) {
        return xcache_set($key, $value, $expires);
    }

    public function clear() {
        // xcache_clear_cache需要验证权限
        $tmp ['user'] = isset($_SERVER ['PHP_AUTH_USER']) ? null : $_SERVER ['PHP_AUTH_USER'];
        $tmp ['pwd'] = isset($_SERVER ['PHP_AUTH_PW']) ? null : $_SERVER ['PHP_AUTH_PW'];
        $_SERVER ['PHP_AUTH_USER'] = $this->auth_user;
        $_SERVER ['PHP_AUTH_PW'] = $this->auth_pwd;
        // 如果配置中xcache.var_count > 0 则不能用xcache_clear_cache(XC_TYPE_VAR, 0)的方式删除
        $max = xcache_count(XC_TYPE_VAR);
        for ($i = 0; $i < $max; $i++) {
            xcache_clear_cache(XC_TYPE_VAR, $i);
        }
        // 恢复之前的权限
        $_SERVER ['PHP_AUTH_USER'] = $tmp ['user'];
        $_SERVER ['PHP_AUTH_PW'] = $tmp ['pwd'];
        return true;
    }

    public function set_config($options = array()) {
        if (!is_array($options))
            return false;
        $this->auth_user = $options ['user'];
        $this->auth_pwd = $options ['pwd'];
    }

}

?>
