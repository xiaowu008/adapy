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
 * Time: 16:38
 */

namespace Xiaowu008\Pay\tool;


/**
 * @name:工具
 * Class AdaTools
 * @package Sheng\Pay\tool
 */
class AdaTools
{
    public static $rsaPrivateKeyFilePath = "";
    public static $rsaPublicKeyFilePath = "";
    public static $rsaPrivateKey = "";
    public static $rsaPublicKey = "";

    /**
     * @describe: 验证
     * @time:2022/11/4 16:43
     * @param $url
     * @param $params
     * @return string
     */
    public static function generateSignature($url , $params)
    {
        if (is_array($params)) {
            $Parameters = array();
            foreach ($params as $k => $v) {
                $Parameters[$k] = $v;
            }
            $data = $url . json_encode($Parameters);
        } else {
            $data = $url . $params;
        }
        return self::SHA1withRSA($data);
    }

    /**
     * @describe: SHA验证
     * @time:2022/11/4 16:42
     * @param $data
     * @return string
     */
    public static function SHA1withRSA($data): string
    {
        if (self::checkEmpty(self::$rsaPrivateKeyFilePath)) {
            $priKey = self::$rsaPrivateKey;
            $key = "-----BEGIN PRIVATE KEY-----\n" . wordwrap($priKey , 64 , "\n" , true) . "\n-----END PRIVATE KEY-----";
        } else {
            $priKey = file_get_contents(self::$rsaPrivateKeyFilePath);
            $key = openssl_get_privatekey($priKey);
        }
        try {
            openssl_sign($data , $signature , $key);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return base64_encode($signature);
    }

    /**
     * @describe: 证书验证
     * @time:2022/11/4 16:42
     * @param $signature
     * @param $data
     * @return bool
     */
    public static function verifySign($signature , $data): bool
    {
        if (self::checkEmpty(self::$rsaPublicKeyFilePath)) {
            $pubKey = self::$rsaPublicKey;
            $key = "-----BEGIN public static KEY-----\n" . wordwrap($pubKey , 64 , "\n" , true) . "\n-----END public static KEY-----";
        } else {
            $pubKey = file_get_contents(self::$rsaPublicKeyFilePath);
            $key = openssl_get_publickey($pubKey);
        }
        if (openssl_verify($data , base64_decode($signature) , $key)) {
            return true;
        }
        return false;
    }

    /**
     * @describe: 判断是否为空
     * @time:2022/11/4 16:41
     * @param $value
     * @return bool
     */
    public static function checkEmpty($value): bool
    {
        return !isset($value) || $value === null || trim($value) === "";
    }

    public static function get_array_value($data , $key)
    {
        if (isset($data[$key])) {
            return $data[$key];
        }
        return "";
    }

    public static function createLinkstring($params)
    {
        $arg = "";

        foreach ($params as $key => $val) {
            if ($val) {
                $arg .= $key . "=" . $val . "&";
            }
        }
        $arg = substr($arg , 0 , -1);
        return $arg;
    }
}