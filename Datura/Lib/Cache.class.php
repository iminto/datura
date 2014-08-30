<?php

/**
 * 缓存抽象类
 * waitfox@qq.com
 */
abstract class Cache {
    private $expire = 0; //缓存过期时间
    public $cache;

    public static function get_instance($name = 'file') {
        static $instances = array();
        if (!isset($instances [$name]) || !is_object($instances [$name])) {
            $options = config('cache');
            $class = 'Cache_Driver_' . ucfirst($name);
            $instances [$name] = new $class($options [$name]);
            $instances [$name]->set_config($options [$name]);
        }
        return $instances[$name];
    }

    /**
     * 执行设置操作
     *
     * @param string $key 缓存数据的唯一key
     * @param string $value 缓存数据值，该值是一个含有有效数据的序列化的字符串
     * @param int $expires 缓存数据保存的有效时间，单位为秒，默认时间为0即永不过期
     * @return boolean
     * @throws leaps_exception 缓存失败的时候抛出异常
     */
    protected abstract function set_value($key, $value, $expires = 0);

    /**
     * 执行获取操作
     *
     * @param string $key 缓存数据的唯一key
     * @return string 缓存的数据
     * @throws leaps_exception 缓存数据获取失败抛出异常
     */
    protected abstract function get_value($key);

    /**
     * 需要实现的删除操作
     *
     * @param string $key 需要删除的缓存数据的key
     * @return boolean
     */
    protected abstract function delete_value($key);

    /**
     * 清楚缓存，过期及所有缓存
     *
     * @return boolean
     */
    public abstract function clear();

    /**
     * 设置缓存
     * 如果key不存在，添加缓存；否则，将会替换已有key的缓存。
     *
     * @param string $key 保存缓存数据的键。
     * @param string $value 保存缓存数据。
     * @param int $expires 缓存数据的过期时间,0表示永不过期
     * @return boolean
     * @throws leaps_exception 缓存失败时抛出异常
     */
    public function set($key, $value, $expires = 0) {
        $value = serialize($value);
        return $this->set_value($key, $value, $expires);
    }

    /**
     * 根据缓存key获取指定缓存
     *
     * @param string $key 获取缓存数据的标识
     * @param string $key 获取缓存数据的应用
     * @return mixed 返回被缓存的数据
     * @throws cache_exception 获取失败时抛出异常
     */
    public function get($key) {
        return $this->get_value($key);
    }

    /**
     * 删除缓存数据
     *
     * @param string $key 获取缓存数据的标识
     * @param string $key 获取缓存数据的应用
     * @return boolean
     * @throws cache_exception 删除失败时抛出异常
     */
    public function delete($key) {
        return $this->delete_value($key);
    }

    /**
     * 设置缓存过期时间
     * 单位为秒,默认为0永不过期
     * @param int $expire 缓存过期时间,单位为秒,默认为0永不过期
     */
    public function set_expire($expire) {
        $this->expire = intval($expire);
    }

    /**
     * 返回过期时间设置
     * 单位为秒，默认值为0永不过期
     * @return int $expire 缓存过期时间，默认为0永不过期，单位为秒
     */
    public function get_expire() {
        return $this->expire;
    }

}