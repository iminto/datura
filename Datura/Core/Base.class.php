<?php
/**
 * 常用工具类，用于数据过滤、获取配置等
 * 截止到0.02版本，此类目前暂未使用
 */
class Base{
    public static $INSTANCES=array();//实例化结果集

    public static function filter(&$array, $function) {
        if (!is_array($array))
            return $array = $function($array);
        foreach ($array as $key => $value)
            (is_array($value) && $array[$key] = Datura::filter($value, $function)) || $array[$key] = $function($value);
        return $array;
    }
    
    public static function config($key,$value=NULL){
        if(array_key_exists($key, $GLOBALS['_config'])){
            if(!empty($value)){
               $GLOBALS['_config'][$key]=$value;
               $c=var_export($GLOBALS['_config'],TRUE);
               $config.="<?php\r\nreturn ".$c.";\r\n";
               file_put_contents(_DTR_PATH.'Config.php', $config);
               return true;
            }else{
              return $GLOBALS['_config'][$key];  
            }
        }
    }
    
    public function instance($Class,$Args=null,$Method=null,$MethodArgs=null){
        $Identify=$Class.serialize($Args).$Method.serialize($MethodArgs);//标记
            if(!isset(self::$INSTANCES[$Identify])){
			if(class_exists($Class)){
				$Class=$Args===null?new $Class():new $Class($Args);
				if(!empty($Method) && method_exists($Class,$Method)){
					self::$INSTANCES[$Identify]=$MethodArgs===null?call_user_func(array(&$Class,$Method)):call_user_func_array(array(&$Class,$Method),array($MethodArgs));
				}else{
					self::$INSTANCES[$Identify]=$Class;
				}
			}else{
                            sprintf('类 %s 不存在',$Class);
			}
		}
		return self::$INSTANCES[$Identify];
    }
}
?>
