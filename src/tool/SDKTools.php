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
 * Time: 15:09
 */

namespace Xiaowu008\Pay\tool;


class SDKTools extends Config
{
    //创建静态私有的变量保存该类对象
    static private $instance;

    public function __construct()
    {
        parent::__construct();
    }

    private function __clone() { }

    public static function getInstance()
    {
        //判断$instance是否是Singleton的对象，不是则创建
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function post($params = array() , $endpoint)
    {
        $request_params = $this->do_empty_data($params);
        $req_url = $this->gateWayUrl . $endpoint;
        $header = $this->get_request_header($req_url , $request_params , self::$header);
        return $this->ada_request->curl_request($req_url , $request_params , $header , $is_json = true);
    }

    public function get($params = array() , $endpoint)
    {
        ksort($params);
        $request_params = $this->do_empty_data($params);
        $req_url = $this->gateWayUrl . $endpoint;
        $header = $this->get_request_header($req_url , http_build_query($request_params) , self::$headerText);
        return $this->ada_request->curl_request($req_url . "?" . http_build_query($request_params) , "" , $header , false);
    }

    /**
     * @describe: 获取ip
     * @time:2022/11/4 16:27
     * @return array|false|mixed|string
     */
    public static function get_client_ip()
    {
        if (!isset($_SERVER)) {
            return getenv('SERVER_ADDR');
        }

        if($_SERVER['SERVER_ADDR']) {
            return $_SERVER['SERVER_ADDR'];
        }

        return $_SERVER['LOCAL_ADDR'];
    }

    public function isError()
    {
        return $this->isError();
    }
}