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
 * Date: 2022/11/07
 * Time: 9:40
 */

namespace Xiaowu008\Pay;

/**
 * @name:支付
 * Class Pay
 * @package Sheng\Pay
 */
class Pay extends Adapay
{
    /**
     * @describe: 支付宝app支付
     * @time:2022/11/7 9:48
     * @param $order_no 订单号
     * @param $money 订单金额
     * @param $title 标题
     * @param $time_expire 过期时间
     * @param string $body 商品描述信息，微信小程序和微信公众号该字段最大长度42个字符
     * @param array $special 自定义参数
     * @param array $extend
     * @return array|bool
     * {
        pay_info 支付信息，用于唤起支付宝
        sub_open_id  买家的支付宝用户 id
        couponInfos 优惠券信息，使用 JSON格式
        cashPayAmt 现金支付金额
        discountAmt  优惠金额
     * }
     */
    public static function alipay_app($order_no , $money , $title , $time_expire , $body = '' ,$special=[], $extend = [])
    {
        return self::payment($order_no , 'alipay' , $money , $title , $time_expire , $body ?: $title ,$special,  $extend);
    }

    /**
     * @describe: 支付宝正扫
     * @time:2022/11/7 9:48
     * @param $order_no 订单号
     * @param $money 订单金额
     * @param $title 标题
     * @param $time_expire 过期时间
     * @param string $body 商品描述信息，微信小程序和微信公众号该字段最大长度42个字符
     * @param array $special 自定义参数
     * @param array $extend
     * @return array|bool
     * {
    qrcode_url 二维码连接
    sub_open_id 买家的支付宝用户 id
    couponInfos 优惠券信息，使用 JSON格式
    cashPayAmt 现金支付金额
    discountAmt  优惠金额
     * }
     */
    public static function alipay_qr($order_no , $money , $title , $time_expire , $body = '' ,$special=[], $extend = [])
    {
        return self::payment($order_no , 'alipay_qr' , $money , $title , $time_expire , $body ?: $title ,$special,  $extend);
    }

    /**
     * @describe: 支付宝正扫
     * @time:2022/11/7 9:48
     * @param $order_no 订单号
     * @param $money 订单金额
     * @param $title 标题
     * @param $time_expire 过期时间
     * @param string $body 商品描述信息，微信小程序和微信公众号该字段最大长度42个字符
     * @param array $special 自定义参数
     * @param array $extend
     * @return array|bool
     * {
    pay_info 支付信息，用于唤起支付宝
    sub_open_id 买家的支付宝用户 id
    couponInfos 优惠券信息，使用 JSON格式
    cashPayAmt 现金支付金额
    discountAmt  优惠金额
     * }
     */
    public static function alipay_wap($order_no , $money , $title , $time_expire , $body = '' ,$special=[], $extend = [])
    {
        return self::payment($order_no , 'alipay_wap' , $money , $title , $time_expire , $body ?: $title ,$special,  $extend);
    }

    /**
     * @describe: 支付宝小程序支付
     * @time:2022/11/7 9:48
     * @param $order_no 订单号
     * @param $money 订单金额
     * @param $title 标题
     * @param $time_expire 过期时间
     * @param string $body 商品描述信息，微信小程序和微信公众号该字段最大长度42个字符
     * @param array $special 自定义参数
     * @param array $extend
     * @return array|bool
     * {
    pay_info 支付信息，用于唤起支付宝
    sub_open_id 买家的支付宝用户 id
    couponInfos 优惠券信息，使用 JSON格式
    cashPayAmt 现金支付金额
    discountAmt  优惠金额
     * }
     */
    public static function alipay_lite($order_no , $money , $title , $time_expire , $body = '' ,$special=[], $extend = [])
    {
        return self::payment($order_no , 'alipay_lite' , $money , $title , $time_expire , $body ?: $title ,$special,  $extend);
    }

}