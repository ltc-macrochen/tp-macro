<?php
namespace common\components;

use yii\base\Exception;

/**
 * Http操作类
 */
class HttpBase {

    //调试模式
    public static $debug = false;

    /**
     * 发起一个HTTP/HTTPS的请求
     * @param $url string 接口的URL
     * @param array $params 接口参数   array('content'=>'test', 'format'=>'json');
     * @param string $method 请求类型    GET|POST
     * @param bool $multi 图片信息
     * @param array $extheaders 扩展的包头信息
     * @return mixed
     * @throws Exception
     */
    public static function request($url, $params = array(), $method = 'GET', $multi = false, $extheaders = array()) {
        if (!function_exists('curl_init')) {
            throw new Exception('Need to open the curl extension', 500);
        }

        $method = strtoupper($method);
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_USERAGENT, 'PHP-SDK OAuth2.0');
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ci, CURLOPT_TIMEOUT, 10);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ci, CURLOPT_HEADER, false);
        $headers = (array) $extheaders;
        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (is_string($params)) {
                    //post原始数据
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $params);
                }else {
                    //post表单
                    if ($multi) {   //表单中含有文件, 入参前请增加@
                        //如果$params是一个数组，Content-Type头将会被设置成multipart/form-data。
                        curl_setopt($ci, CURLOPT_POSTFIELDS, $params);
                    } else {        //表单中没有文件
                        //如果$params是一个URL-encoded字符串，Content-Type头将会被设置成application/x-www-form-urlencoded                  
                        curl_setopt($ci, CURLOPT_POSTFIELDS, http_build_query($params));
                    }
                }
                break;
            case 'DELETE':
            case 'GET':
                $method == 'DELETE' && curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($params)) {
                    $url = $url . (strpos($url, '?') ? '&' : '?')
                            . (is_array($params) ? http_build_query($params) : $params);
                }
                break;
        }
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($ci, CURLOPT_URL, $url);
        if ($headers) {
            curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ci);
        if($response) {
            curl_close($ci);
            return $response;            
        }else {
            $error = curl_errno($ci);
            curl_close($ci);
            throw new Exception("curl出错: $error", 500);            
        }
        
    }

    public static function redirect($url) {
        \Yii::$app->getResponse()->redirect($url);
        //header("Location:{$url}");
    }
    
    /**************************************************************************/
    /**************************  客户端请求参数相关操作  ************************/
    /**************************************************************************/
    
    public static function getGetParameter($name, $defaultVal = '') {
        return isset($_GET[$name]) ? $_GET[$name] : $defaultVal;
    }

    public static function getPostParameter($name, $defaultVal = '') {
        return isset($_POST[$name]) ? $_POST[$name] : $defaultVal;
    }

    public static function getClientIp() {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        }else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
            $ip = getenv("REMOTE_ADDR");
        }else if (isset($_SERVER ['REMOTE_ADDR']) && $_SERVER ['REMOTE_ADDR'] && strcasecmp($_SERVER ['REMOTE_ADDR'], "unknown")) {
            $ip = $_SERVER ['REMOTE_ADDR'];
        }else {
            $ip = "unknown";
        }
        return ($ip);
    }
    
    /**************************************************************************/
    /****************************  Cookie 相关操作  ****************************/
    /**************************************************************************/
    public static function getCookie($name) {
        return $_COOKIE[$name];
    }

    public static function setCookie($name, $value, $expire = 0, $path = null, $domain = null, $secure = false, $httponly = false) {
        return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }

    public static function delCookie($name) {
        return setcookie($name, '', time() - 3600);
    }
    
    /**************************************************************************/
    /***************************   Session 相关操作  ***************************/
    /**************************************************************************/
    
    /**
     * 开启一个session
     * 
     * 如果已经存在则打开，如果不存在则创建
     * @return boolean
     */
    static public function openSession() {
        session_start();
        
        //简单的session检查
        if ($_SESSION['state'] != true) {    //新session
            $_SESSION['state'] = true;
        
            $existed = false;
        }else {
            $existed = true;    //已经存在的session
        }
        
        //防止攻击的session检查
        if(!isset($_SESSION['user_agent'])){  //新session
            $_SESSION['user_agent'] = $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'];
            $existed = false;
        }elseif($_SESSION['user_agent'] != $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']) {  //session非法
            session_regenerate_id();
            $existed = false;
        }else {
            $existed = true;    //已经存在的session
        }
        
        return $existed;
    }
    
    /**
     * 关闭一个session 
     * 
    */
    static public function closeSession() {
        //清除cookie
        self::setCookie(session_name(),"",time()-3600);
        
        session_start();
        //释放session资源(销毁服务器/tmp目录下的session文件，下次访问时$_SESSION信息为空)
        session_destroy();
        //销毁session全局变量(后续操作时$_SESSION信息为空)
        $_SESSION= array();

        return;
    }
}
