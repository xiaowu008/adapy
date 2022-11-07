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
 * Time: 15:12
 */

namespace Sheng\Pay\tool;

/**
 * @name:请求
 * Class Request
 * @package Sheng\Pay\tool
 */
class Request
{
    public $postCharset = "utf-8";

    /**
     * @describe: curl 请求
     * @time:2022/11/4 15:14
     * @param $url
     * @param null $postFields
     * @param null $headers
     * @param bool $is_json
     * @return array
     */
    public static function curl_request($url , $postFields = null , $headers = null , $is_json = false): array
    {
        Config::writeLog("curl方法参数:" . json_encode(func_get_args() , JSON_UNESCAPED_UNICODE) , "INFO");
        $ch = curl_init();
        curl_setopt($ch , CURLOPT_URL , $url);
        curl_setopt($ch , CURLOPT_FAILONERROR , false);
        curl_setopt($ch , CURLOPT_RETURNTRANSFER , true);
        curl_setopt($ch , CURLOPT_HEADER , 0);
        curl_setopt($ch , CURLOPT_SSL_VERIFYPEER , false);
        if (is_array($postFields) && 0 < count($postFields)) {
            curl_setopt($ch , CURLOPT_POST , true);
            if ($is_json) {
                $json_data = json_encode($postFields);
                Config::writeLog("post-json请求参数:" . json_encode($postFields , JSON_UNESCAPED_UNICODE) , "INFO");
                $headers[] = "Content-Length:" . strlen($json_data);
                curl_setopt($ch , CURLOPT_POSTFIELDS , $json_data);
            } else {
                Config::writeLog("post-form请求参数:" . json_encode($postFields , JSON_UNESCAPED_UNICODE) , "INFO");
                curl_setopt($ch , CURLOPT_POSTFIELDS , $postFields);
            }
        }
        if (empty($headers)) {
            $headers = array('Content-type: application/x-www-form-urlencoded');
        }
        Config::writeLog("curl请求头:" . json_encode($headers , JSON_UNESCAPED_UNICODE) , "INFO");
        curl_setopt($ch , CURLOPT_HTTPHEADER , $headers);
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch , CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            AdaPay::writeLog(curl_error($ch) , "ERROR");
        }
        curl_close($ch);
        Config::writeLog("curl返回参数:" . $statusCode . json_encode($response , JSON_UNESCAPED_UNICODE) , "INFO");
        return array($statusCode , $response);

    }

    /**
     * @describe: 编码处理
     * @time:2022/11/4 15:13
     * @param $data
     * @param $targetCharset
     * @return string
     */
    function characet($data , $targetCharset): string
    {

        if (!empty($data)) {
            $fileType = $this->postCharset;
            if (strcasecmp($fileType , $targetCharset) != 0) {
                $data = mb_convert_encoding($data , $targetCharset , $fileType);
            }
        }
        return $data;
    }
}