<?php
/**
 * 文件缓存类
 */
class Cache_Driver_File extends Cache {
    private $expire=0;
    const DATA='filecache';
    
    public function __construct() {
    }
    
    protected function delete_value($key) {
        $filename=$this->path($key);
        if(is_file($filename)){
            unlink($filename);
        }
        return true;
    }

    protected function get_value($key) {
       $filename=$this->path($key);
       if(time()- filemtime($filename)>$this->expire&&$this->expire>0){
           //缓存过期，删除
           $this->delete_value($key);
           return NULL;
       }else{
           $content=  file_get_contents($filename);
           return unserialize($content);
       }
        
    }

    protected function set_value($key, $value, $expires = 0) {
        $filename=$this->path($key);
        touch($filename);
        file_put_contents($filename, $value);
        if(func_num_args()>2&&  func_get_arg(2)>0){
            $this->expire=$expires;
        }
        return true;
        
    }

    public function clear() {
        $dir=_ROOT.'Data'.DIRECTORY_SEPARATOR.self::DATA;
        deleteDirrecursive($dir);
    }
    
    public function path($key){
        $path=_ROOT.'Data'.DIRECTORY_SEPARATOR.self::DATA;
        if(!is_dir($path)) mkdir ($path);
        $path.=DIRECTORY_SEPARATOR.$key.'.'.md5(Config('key'));
        return $path;
    }
    
    public function set_config($options = array()) {
        if (! is_array ( $options )) return false;
    }
}
?>
