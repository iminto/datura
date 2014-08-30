<?php

class Cache_Driver_Memcache extends Cache {

    protected $memcache = null;
    protected $compress = 0; //是否对缓存压缩处理

    public function __construct() {
        
    }

    public function getMemcache() {
        return $this->memcache;
    }

    /**
     * 检查mem是否连接成功
     * @return	bool	连接成功返回true,否则返回false
     */
    public function mem_connect_error() {
        $stats = $this->memcache->getStats();
        if (empty($stats)) {
            return false;
        } else {
            return true;
        }
    }

    protected function delete_value($key) {
        return $this->memcache->delete($key);
    }

    protected function get_value($key) {
        return $this->compress ? $this->memcache->get($key, true) : $this->memcache->get($key);
    }

    protected function set_value($key, $value, $expires = 0) {
        return $this->compress ? $this->memcache->set($key, $value, MEMCACHE_COMPRESSED, (int) $expires) : $this->memcache->set($key, $value, (int) $expires);
    }

    protected function add_value($key, $value, $expires = 0) {
        return $this->compress ? $this->memcache->add($key, $value, MEMCACHE_COMPRESSED, (int) $expires) : $this->memcache->add($key, $value, (int) $expires);
    }

    public function clear() {
        return $this->memcache->flush();
    }

    public function stat() {
        return $this->memcache->getExtendedStats();
    }

    public function close() {
        return $this->memcache->close();
    }

    public function set_config($options = array()) {
        if (!is_array($options))
            return false;
        $mc = new Memcache();
        if (is_array($options)) {
            foreach ($options as $server) {
                call_user_func_array(array($mc, 'addServer'), $server);
            }
            //如果只有一个memcache服务器
        } else {
            call_user_func_array(array($mc, 'addServer'), $options);
        }
        $this->memcache = $mc;
    }

}

?>
