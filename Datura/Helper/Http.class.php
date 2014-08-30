<?php

/**
 * HTTP工具类，处理一些数据提交等常用任务,代码待完善和补充
 */
class Http {

    /**
     * 采集远程文件
     * @access public
     * @param string $remote 远程文件名
     * @param string $local 本地保存文件名
     * @return mixed
     */
    static public function curlDownload($remote, $local) {
        $cp = curl_init($remote);
        $fp = fopen($local, "w");
        curl_setopt($cp, CURLOPT_FILE, $fp);
        curl_setopt($cp, CURLOPT_HEADER, 0);
        curl_exec($cp);
        curl_close($cp);
        fclose($fp);
    }

    /**
     * POST方式提交数据，简单易用，但效率不高。
     * @param type $url
     * @param type $data
     * @param type $cookie
     * @param type $refer
     * @param type $ua
     * @param type $timeout
     * @return type
     */
    public function httpPost($url, $data, $cookie = '', $refer = '', $ua = '', $timeout = 10) {
        $data = http_build_query($data);
        $parse = parse_url($url);
        $host = $parse['host'];
        $header = "Content-type: application/x-www-form-urlencoded\r\n";
        $header.="Host: {$host}\r\n";
        $header.="Content-Length: " . strlen($data) . "\r\n";
        if ($cookie != '') {
            $header.="Cookie: {$cookie}" . "\r\n";
        }
        if ($ua != '') {
            $header.= "User-Agent: {$ua}\r\n";
        } else {
            $header.= "User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.64 Safari/537.31\r\n";
        }
        if ($refer != '') {
            $header.="Referer: {$refer}\r\n";
        }
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => $header,
                'content' => $data,
                'timeout' => $timeout)
        );
        $context = stream_context_create($opts);
        $html = file_get_contents($url, false, $context);
        return $html;
    }

    public static function sendHttpStatus($code) {
        static $_status = array(
    // Informational 1xx
    100 => 'Continue',
    101 => 'Switching Protocols',
    // Success 2xx
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    // Redirection 3xx
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found', // 1.1
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    // 306 is deprecated but reserved
    307 => 'Temporary Redirect',
    // Client Error 4xx
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Long',
    415 => 'Unsupported Media Type',
    416 => 'Requested Range Not Satisfiable',
    417 => 'Expectation Failed',
    // Server Error 5xx
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Timeout',
    505 => 'HTTP Version Not Supported',
    509 => 'Bandwidth Limit Exceeded'
        );
        if (isset($_status[$code])) {
            header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
        }
    }
    
    /**
     * 发送文件到浏览器以便下载
     * @param type $filename
     */
    public static function down($filename) {
        if (!$filename)
            return;
        $filename = basename($filename);
        header("Content-type: application/octet-stream");
        $ua = $_SERVER["HTTP_USER_AGENT"];
        //处理中文文件名
        $encode_filename = rawurlencod($filename);
        if (preg_match("/MSIE/", $ua)) {
            header('Content-Disposition: attachment; filename="' . $encode_filename . '"');
        } else if (preg_match("/Firefox/", $ua)) {
            header("Content-Disposition: attachment; filename*=\"utf8''" . $filename . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        }
        header("Content-Length: " . filesize($filename)); //告诉浏览器文件大小，下载才会有进度条
        //如果服务器支持xsendfile，则使用xsendfile头加速下载
        $sapi = strtolower(php_sapi_name());
        if (stripos($sapi, 'apache')) {
            $x_sendfile_supported = in_array('mod_xsendfile', apache_get_modules());
            if (!headers_sent() && $x_sendfile_supported) {
                header("X-Sendfile: {$filename}");
            } else {
                readfile($filename);
            }
        } elseif (stripos($sapi, 'cgi')) {
            //nginx 默认就支持xsend file
            header('X-Accel-Redirect: ' . $filename);
        } else {
            readfile($filename);
        }
    }
    
    /**
     * Forces the user's browser not to cache the results of the current request.
     *
     * @return void
     * @access protected
     * @link http://book.cakephp.org/view/431/disableCache
     */
    public static function disableBrowserCache()
    {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

    /**
     * Etag
     *
     * Set or check etag
     * @param string $etag
     * @param boolean $notModifiedExit
     */
    public static function etag($etag, $notModifiedExit = true)
    {
        if ($notModifiedExit && isset($_SERVER['HTTP_IF_NONE_MATCH']) && $etag == $_SERVER['HTTP_IF_NONE_MATCH']) {
            self::statusCode('304');
            exit();
        }
        header("Etag: $etag");
    }

    /**
     * Last modified
     * @param int $modifiedTime
     * @param boolean $notModifiedExit
     */
    public static function lastModified($modifiedTime, $notModifiedExit = true)
    {
        $modifiedTime = date('D, d M Y H:i:s \G\M\T', $modifiedTime);
        if ($notModifiedExit && isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $modifiedTime == $_SERVER['HTTP_IF_MODIFIED_SINCE']) {
            self::statusCode('304');
            exit();
        }
        header("Last-Modified: $modifiedTime");
    }

    /**
     * Expires
     *
     * @param int $seconds
     */
    public static function expires($seconds = 1800)
    {
        $time = date('D, d M Y H:i:s', time() + $seconds) . ' GMT';
        header("Expires: $time");
    }

}

?>
