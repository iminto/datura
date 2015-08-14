<?php
/**
 * usage
$data = array(
    'id'     => 8,
    'sex'    => 'F',
    'tags'   => array('foo' => 3, 'bar' => 7),
    'age'    => 8,
    'email'  => 'foo@bar.com',
    'date'   => '2012-12-10',
    'body'   => 'foobarbarfoo',
);

$rules = array(
    'id'     => array('required' => true, 'type' => 'int'),
    'sex'    => array('in' => array('F', 'M')),
    'tags'   => array('required' => true, 'each' => array('type' => 'int')),
    'age'    => array('type' => 'int', 'range' => array(38, 130), 'msg' => 'age must be 18~130'),
    'email'  => array('type' => 'email'),
    'date'   => array('type' => 'date'),
    'body'   => array('required' => true, 'range' => array(1, 500))
);

var_dump(Validate::check($data, $rules));
**/

class Validate
{
    /**
     * Validate Errors
     *
     * @var array
     */
    public $errors = array();

    /**
     * Check if is not empty
     *
     * @param string $str
     * @return boolean
     */
    public static function notEmpty($str, $trim = true)
    {
        if (is_array($str)) {
            return 0 < count($str);
        }

        return strlen($trim ? trim($str) : $str) ? true : false;
    }

    /**
     * Match regex
     *
     * @param string $value
     * @param string $regex
     * @return boolean
     */
    public static function match($value, $regex)
    {
        return preg_match($regex, $value) ? true : false;
    }

    /**
     * Max
     *
     * @param mixed $value numbernic|string
     * @param number $max
     * @return boolean
     */
    public static function max($value, $max)
    {
        is_string($value) && $value = strlen($value);
        return $value <= $max;
    }

    /**
     * Min
     *
     * @param mixed $value numbernic|string
     * @param number $min
     * @return boolean
     */
    public static function min($value, $min)
    {
        is_string($value) && $value = strlen($value);
        return $value >= $min;
    }

    /**
     * Range
     *
     * @param mixed $value numbernic|string
     * @param array $max
     * @return boolean
     */
    public static function range($value, $range)
    {
        is_string($value) && $value = strlen($value);
        return (($value >= $range[0]) && ($value <= $range[1]));
    }

    /**
     * Check if in array
     *
     * @param mixed $value
     * @param array $list
     * @return boolean
     */
    public static function in($value, $list)
    {
        return in_array($value, $list);
    }

    /**
     * Check if is email
     *
     * @param string $email
     * @return boolean
     */
    public static function email($email)
    {
        return preg_match('/^[a-z0-9_\-]+(\.[_a-z0-9\-]+)*@([_a-z0-9\-]+\.)+([a-z]{2,5})$/', $email) ? true : false;
    }

    /**
     * Check if is url
     *
     * @param string $url
     * @return boolean
     */
    public static function url($url)
    {
        return 0 < preg_match('/^(?:http(?:s)?:\/\/(?:[\w-]+\.)+[\w-]+(?:\:\d+)*+(?:\/[\w- .\/?%&=]*)?)$/', $url);
    }

    /**
     * Check if is ip
     *
     * @param string $ip
     * @return boolean
     */
    public static function ip($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP) == true;
    }

    /**
     * Check if is date
     *
     * @param string $date
     * @return boolean
     */
    public static function date($date)
    {
        return preg_match('/^\d{4}[\/-]\d{1,2}[\/-]\d{1,2}$/', $date) ? true : false;
    }

    /**
     * Check if is datetime
     *
     * @param string $datetime
     * @return boolean
     */
    public static function datetime($datetime, $format = 'Y-m-d H:i:s')
    {
        return ($time = strtotime($datetime)) && ($datetime == date($format, $time));
    }

    /**
     * Check if is numbers
     *
     * @param mixed $value
     * @return boolean
     */
    public static function number($value)
    {
        return is_numeric($value);
    }

    /**
     * Check if is int
     *
     * @param mixed $value
     * @return boolean
     */
    public static function int($value)
    {
        return is_int($value);
    }

    /**
     * Check if is digit
     *
     * @param mixed $value
     * @return boolean
     */
    public static function digit($value)
    {
        return is_int($value) || ctype_digit($value);
    }

    /**
     * Check if is string
     *
     * @param mixed $value
     * @return boolean
     */
    public static function string($value)
    {
        return is_string($value);
    }
    
    public static function mobile($number) {
        return 0 < preg_match('#^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^17[0-9]\d{8}$|^18[0-9]\d{8}$#', $number);
    }
    
    public static function chinese($string) {
        return 0 < preg_match('/^[\x{FF00}-\x{FFFF}\x{4e00}-\x{9fa5}\x{300a}\x{300b}\x{3010}\x{3011}\x{3001}\x{3002}\x{3003}\x{2018}\x{2019}\x{201c}\x{201d}]+$/u', $string);
    }

    /**
     * Check
     *
     * $rules = array(
     *     'required' => true if required , false for not
     *     'type'     => var type, should be in ('email', 'url', 'ip', 'date', 'number', 'int', 'string')
     *     'regex'    => regex code to match
     *     'func'     => validate function, use the var as arg
     *     'max'      => max number or max length
     *     'min'      => min number or min length
     *     'range'    => range number or range length
     *     'msg'      => error message,can be as an array
     * )
     *
     * @param array $data
     * @param array $rules
     * @param boolean $ignorNotExists
     * @return boolean
     */
    public function check($data, $rules, $ignorNotExists = false)
    {
        foreach ($rules as $key => $rule) {
            $rule += array('required' => false, 'msg' => 'Unvalidated');

            // deal with not existed
            if (!isset($data[$key])) {
                if ($rule['required'] && !$ignorNotExists) {
                    $this->errors[$key] = $rule['msg'];
                }
                continue;
            }

            if (!self::_check($data[$key], $rule)) {
                $this->errors[$key] = $rule['msg'];
                continue;
            }

            if (isset($rule['rules'])) {
                $tmp = $this->check($data[$key], $rule['rules'], $ignorNotExists);
                if (0 !== $tmp['code']) {
                    $this->errors[$key] = $tmp['msg'];
                }
            }
        }

        return $this->errors ? false : true;
    }

    /**
     * Check value
     *
     * @param mixed $value
     * @param array $rule
     * @return mixed string as error, true for OK
     */
    protected static function _check($data, $rule)
    {
        $flag = true;
        foreach ($rule as $key => $val) {
            switch ($key) {
            	case 'required':
            		if ($val) $flag = self::notEmpty($data);
            		break;

                case 'func':
                    $flag = call_user_func($val, $data);
                    break;

                case 'regex':
                    $flag = self::match($data, $val);
                    break;

                case 'type':
                    $flag = self::$val($data);
                    break;

                case 'max':
                case 'min':
                case 'max':
                case 'range':
                    $flag = self::$key($data, $val);
                    break;

                case 'each':
                    $val += array('required' => false);
                    foreach ($data as $item) {
                        if (!$flag = self::_check($item, $val)) break;
                    }
                    break;
            	default:
            		break;
            }
            if (!$flag) {
                return false;
            }
        }

        return true;
    }
}
