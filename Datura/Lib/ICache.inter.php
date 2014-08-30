<?php
/**
 * 缓存处理器接口
 */
interface ICache {

    public function connect();

    public function get($name);

    public function set($name, $val, $expire = null);

    public function have($name);

    public function remove($name);

    public function clear();
}

?>
