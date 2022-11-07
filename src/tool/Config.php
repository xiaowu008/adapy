<?php
/**
 *  *  佛曰:
 *          写字楼里写字间，写字间里程序员；
 *          程序人员写程序，又拿程序换酒钱。
 *          酒醒只在网上坐，酒醉还来网下眠；
 *          酒醉酒醒日复日，网上网下年复年。
 *          但愿老死电脑间，不愿鞠躬老板前；
 *          奔驰宝马贵者趣，公交自行程序员。
 *          别人笑我忒疯癫，我笑自己命太贱；
 *          不见满街漂亮妹，哪个归得程序员？
 * QQ: 1477862679@qq.com
 * Created by eeae.vip
 * Author: 小五
 * Date: 2022/11/04
 * Time: 15:00
 */

namespace Xiaowu008\Pay\tool;


class Config
{
    public static $api_key = "";
    public static $rsaPrivateKeyFilePath = "";
    public static $rsaPrivateKey = "";
    # 不允许修改
    public static $rsaPublicKey = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCwN6xgd6Ad8v2hIIsQVnbt8a3JituR8o4Tc3B5WlcFR55bz4OMqrG/356Ur3cPbc2Fe8ArNd/0gZbC9q56Eb16JTkVNA/fye4SXznWxdyBPR7+guuJZHc/VW2fKH2lfZ2P3Tt0QkKZZoawYOGSMdIvO+WqK44updyax0ikK6JlNQIDAQAB";
    public static $header = array('Content-Type:application/json');
    public static $headerText = array('Content-Type:text/html');
    public static $headerEmpty = array('Content-Type:multipart/form-data');
    public static $gateWayUrl = "";
    public static $gateWayType = "api";
    public static $mqttAddress = "post-cn-0pp18zowf0m.mqtt.aliyuncs.com:1883";
    public static $mqttInstanceId = "post-cn-0pp18zowf0m";
    public static $mqttGroupId = "GID_CRHS_ASYN";
    public static $mqttAccessKey = "LTAIOP5RkeiuXieW";

    public static $isDebug;
    public static $logDir = "";
    public $postCharset = "utf-8";
    public $signType = "RSA2";
    public $ada_request = "";
    public static $ada_tools = "";
    public $statusCode = 200;
    public static $result = array();
    public static $error = '';
    public static $app_id = '';
    public static $gate_wap_url = 'https://%s.adapay.tech';

    public function __construct($config)
    {
        self::init($config);
        $this->ada_request = new Request();
        self::$ada_tools = new AdaTools();
        self::getGateWayUrl(self::$gateWayType);
        $this->_init_params();
    }

    public function set_config()
    {
        $data = [
            'api_key_live' => '' ,
            'api_key_test' => '' ,
            'rsa_public_key' => '' ,
            'rsa_private_key' => '' ,

            'prod_mode' => 'live' ,
            'sdk_version' => 'v1.4.4' ,
            'debug' => true ,
            'debug_dir' => dirname(__FILE__) . "/log/prod" ,
            'env' => 'prod' ,
        ];
    }


    /**
     * @describe: 初始化数据
     * @time:2022/11/7 11:41
     * @param $config_info
     */
    public static function init($config_info): void
    {
        if (empty($config_info)) {
            try {
                throw new \Exception('缺少SDK配置信息');
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }

        $sdk_version = $config_info['sdk_version'] ?? "v1.0.0";
        self::$header['sdk_version'] = $sdk_version;
        self::$headerText['sdk_version'] = $sdk_version;
        self::$headerEmpty['sdk_version'] = $sdk_version;
        self::$isDebug = $config_info['debug'] ?? false;
        self::$logDir = $config_info['debug_dir'] ?? dirname(__FILE__) . "/log";
        $project_env = $config_info['env'] ?: "prod";
        self::init_mqtt($project_env);

        $prod_mode = $config_info['prod_mode'] ?: "live";
        if ($prod_mode == 'live') {
            self::$api_key = $config_info['api_key_live'] ?? '';
        }
        if ($prod_mode == 'test') {
            self::$api_key = $config_info['api_key_test'] ?? '';
        }

        if (isset($config_info['rsa_public_key']) && $config_info['rsa_public_key']) {
            self::$rsaPublicKey = $config_info['rsa_public_key'];
        }

        if (isset($config_info['rsa_private_key']) && $config_info['rsa_private_key']) {
            self::$rsaPrivateKey = $config_info['rsa_private_key'];
        }

        self::$app_id = $config_info['app_id'];

    }

    /**
     * @describe: 替换地址
     * @time:2022/11/7 11:46
     * @param $type
     */
    public static function getGateWayUrl($type): void
    {
        self::$gateWayUrl = self::$gate_wap_url ? sprintf(self::$gate_wap_url , $type) : "https://api.adapay.tech";
    }

    public static function setAppId($app_id)
    {
        self::$app_id = $app_id;
    }

    public static function setApiKey($api_key)
    {
        self::$api_key = $api_key;
    }

    public static function setRsaPublicKey($pub_key)
    {
        self::$rsaPublicKey = $pub_key;
    }

    /**
     * @describe: _init_params
     * @time:2022/11/6 14:59
     */
    protected function _init_params(): void
    {
        AdaTools::$rsaPrivateKey = self::$rsaPrivateKey;
        AdaTools::$rsaPublicKey = self::$rsaPublicKey;
    }

    /**
     * @describe: 请求
     * @time:2022/11/6 14:59
     * @param $req_url
     * @param $post_data
     * @param array $header
     * @return array
     */
    protected static function get_request_header($req_url , $post_data , $header = array()): array
    {
        $header[] = 'Authorization:' . self::$api_key;
        $header[] = 'Signature:' . AdaTools::generateSignature($req_url , $post_data);
        return $header;
    }

    protected function handleResult()
    {
        $json_result_data = json_decode(self::$result[1] , true);
        if (isset($json_result_data['data'])) {
            return json_decode($json_result_data['data'] , true);
        }
        return [];
    }


    protected static function do_empty_data($req_params): array
    {
        $req_params = array_filter($req_params , static function ($v) {
            return !empty($v) || $v == '0';
        });
        return $req_params;
    }

    /**
     * @describe: 移除数据
     * @time:2022/11/6 13:55
     * @param $arr
     * @param $key
     * @return mixed
     */
    public static function array_remove($arr , $key)
    {
        if (!array_key_exists($key , $arr)) {
            return $arr;
        }

        $keys = array_keys($arr);
        $index = array_search($key , $keys , true);

        if ($index !== FALSE) {
            array_splice($arr , $index , 1);
        }

        return $arr;
    }

    /**
     * @describe: 请求参数
     * @time:2022/11/6 13:56
     * @param $request_params
     * @return array|mixed
     */
    public static function format_request_params($request_params)
    {
        $request_params = self::array_remove($request_params , "adapay_func_code");
        $request_params = self::array_remove($request_params , "adapay_api_version");
        $request_params = self::do_empty_data($request_params);
        return $request_params;
    }

    public static function packageRequestUrl($requestParams = array())
    {
        $adapayFuncCode = $requestParams["adapay_func_code"];
        if (empty($adapayFuncCode)) {
            try {
                throw new \Exception('adapay_func_code不能为空');
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }

        $adapayApiVersion = $requestParams['adapay_api_version'] ?? 'v1';

        self::getGateWayUrl(self::$gateWayType);
        return self::$gateWayUrl . "/" . $adapayApiVersion . "/" . str_replace("." , "/" , $adapayFuncCode);
    }


    /**
     * @describe: 写入日志
     * @time:2022/11/4 15:02
     * @param $message
     * @param string $level
     */
    public static function writeLog($message , $level = "INFO"): void
    {
        if (self::$isDebug) {
            if (!is_dir(self::$logDir)) {
                if (!mkdir($concurrentDirectory = self::$logDir , 0777 , true) && !is_dir($concurrentDirectory)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created' , $concurrentDirectory));
                }
            }

            $log_file = self::$logDir . "/adapay_" . date("Ymd") . ".log";
            $server_addr = "127.0.0.1";
            if (isset($_SERVER["REMOTE_ADDR"])) {
                $server_addr = $_SERVER["REMOTE_ADDR"];
            }
            $message_format = "[" . $level . "] [" . gmdate("Y-m-d\TH:i:s\Z") . "] " . $server_addr . " " . $message . "\n";
            $fp = fopen($log_file , "a+");
            fwrite($fp , $message_format);
            fclose($fp);
        }
    }

    public static function init_mqtt($project_env): void
    {
        if (isset($project_env) && $project_env == "test") {
            self::$mqttAddress = "post-cn-459180sgc02.mqtt.aliyuncs.com:1883";
            self::$mqttGroupId = "GID_CRHS_ASYN";
            self::$mqttInstanceId = "post-cn-459180sgc02";
            self::$mqttAccessKey = "LTAILQZEm73RcxhY";
        }
    }


    /**
     * @describe: 数据处理
     * @time:2022/11/6 14:50
     * @param $req_url
     * @param $request_params
     * @param string $method
     * @param string $is_json
     * @return bool|mixed
     */
    public static function handle($req_url , $request_params , $method = 'get' , $is_json = '')
    {
        $request_params = self::do_empty_data($request_params);
        $header = self::get_request_header($req_url , $request_params , self::$header);
        if ($method == 'get') {
            $result = Request::curl_request($req_url . "?" . http_build_query($request_params) , '' , $header , $is_json ?? false);
        } else {
            $result = Request::curl_request($req_url , $request_params , $header , $is_json ?? true);
        }
        return self::handleResultData($result);
    }

    /**
     * @describe: 返回数据处理
     * @time:2022/11/6 14:47
     * @param array|object|string $result
     * @return bool|mixed
     */
    public static function handleResultData($result)
    {
        $code = $result[0] ?? '';
        $resp_str = $result[1] ?? '';
        $resp_arr = json_decode($resp_str , true);
        $resp_data = $resp_arr['data'] ?? '';
        $resp_sign = $resp_arr['signature'] ?? '';
        $resp_data_decode = json_decode($resp_data , true);
        if ($resp_sign && $code != 401) {
            if (AdaTools::verifySign($resp_sign , $resp_data)) {
                if ($code != 200) {
                    self::$error = $resp_data_decode;
                    return false;
                }
                return $resp_data_decode;
            }
            self::$error = '接口结果返回签名验证失败';
            return false;
        }
        return $resp_arr;
    }

    /**
     * @describe: 获取错误
     * @time:2022/11/6 14:43
     * @return string
     */
    public static function getError(): string
    {
        return self::$error;
    }

    public function isError()
    {
        if (empty(self::$result)) {
            return true;
        }
        $this->statusCode = self::$result[0];
        $resp_str = self::$result[1];
        $resp_arr = json_decode($resp_str , true);
        $resp_data = $resp_arr['data'] ?? '';
        $resp_sign = $resp_arr['signature'] ?? '';
        $resp_data_decode = json_decode($resp_data , true);
        if ($resp_sign && $this->statusCode != 401) {
            if ($this->ada_tools->verifySign($resp_sign , $resp_data)) {
                if ($this->statusCode != 200) {
                    self::$result = $resp_data_decode;
                    return true;
                } else {
                    self::$result = $resp_data_decode;
                    return false;
                }
            } else {
                self::$result = [
                    'failure_code' => 'resp_sign_verify_failed' ,
                    'failure_msg' => '接口结果返回签名验证失败' ,
                    'status' => 'failed'
                ];
                return true;
            }
        } else {
            self::$result = $resp_arr;
            return true;
        }
    }

    /**
     * @describe: 判断是否错误
     * @time:2022/11/4 16:47
     * @return bool
     */
    public static function isErrorMsg()
    {
        if (empty(self::$result)) {
            return true;
        }
        $code = self::$result[0];
        $resp_str = self::$result[1];
        $resp_arr = json_decode($resp_str , true);
        $resp_data = $resp_arr['data'] ?? '';
        $resp_sign = $resp_arr['signature'] ?? '';
        $resp_data_decode = json_decode($resp_data , true);
        if ($resp_sign && $code != 401) {
            if (AdaTools::verifySign($resp_sign , $resp_data)) {
                if ($code != 200) {
                    self::$error = $resp_data_decode;
                    return false;
                }
                self::$result = $resp_data_decode;
                return false;
            }
            self::$error = '接口结果返回签名验证失败';
            return false;
        }
        self::$result = $resp_arr;
        return true;
    }
}