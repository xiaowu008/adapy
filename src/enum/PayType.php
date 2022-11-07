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
 * Time: 10:38
 */

namespace Xiaowu008\Pay\enum;


class PayType
{
    const ALIPAY = 'alipay';
    const ALIPAY_QR = 'alipay_qr';
    const ALIPAY_WAP = 'alipay_wap';
    const ALIPAY_LITE = 'alipay_lite';
    const ALIPAY_PUB = 'alipay_pub';
    const ALIPAY_SCAN = 'alipay_scan';
    const WX_PUB = 'wx_pub';
    const WX_LITE = 'wx_lite';
    const WX_SCAN = 'wx_scan';
    const UNION = 'union';
    const UNION_QR = 'union_qr';
    const UNION_WAP = 'union_wap';
    const UNION_SCAN = 'union_scan';
    const UNION_ONLINE = 'union_online';
    const UNION_CHECKOUT = 'union_checkout';
    const FAST_PAY = 'fast_pay';
    const B2C = 'b2c';
    const B2B = 'b2b';

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data(): array
    {
        return [
            self::ALIPAY => [
                'name' => '支付宝 App 支付' ,
                'value' => self::ALIPAY ,
            ] ,
            self::ALIPAY_QR => [
                'name' => '支付宝正扫' ,
                'value' => self::ALIPAY_QR ,
            ] ,
            self::ALIPAY_WAP => [
                'name' => '支付宝 H5 支付' ,
                'value' => self::ALIPAY_WAP ,
            ] ,
            self::ALIPAY_LITE => [
                'name' => '支付宝小程序支付' ,
                'value' => self::ALIPAY_LITE ,
            ] ,
            self::ALIPAY_PUB => [
                'name' => '支付宝生活号支付' ,
                'value' => self::ALIPAY_PUB ,
            ] ,
            self::ALIPAY_SCAN => [
                'name' => '支付宝反扫' ,
                'value' => self::ALIPAY_SCAN ,
            ] ,
            self::WX_PUB => [
                'name' => '微信公众号支付' ,
                'value' => self::WX_PUB ,
            ] ,
            self::WX_LITE => [
                'name' => '微信小程序支付' ,
                'value' => self::WX_LITE ,
            ] ,
            self::WX_SCAN => [
                'name' => '微信反扫' ,
                'value' => self::WX_SCAN ,
            ] ,
            self::UNION => [
                'name' => '银联云闪付 App' ,
                'value' => self::UNION ,
            ] ,
            self::UNION_QR => [
                'name' => '银联云闪付正扫' ,
                'value' => self::UNION_QR ,
            ] ,
            self::UNION_WAP => [
                'name' => '银联云闪付 H5 支付' ,
                'value' => self::UNION_WAP ,
            ] ,
            self::UNION_SCAN => [
                'name' => '银联云闪付反扫' ,
                'value' => self::UNION_SCAN ,
            ] ,
            self::UNION_ONLINE => [
                'name' => '银联 H5 支付' ,
                'value' => self::UNION_ONLINE ,
            ] ,
            self::UNION_CHECKOUT => [
                'name' => '银联统一收银台支付' ,
                'value' => self::UNION_CHECKOUT ,
            ] ,
            self::FAST_PAY => [
                'name' => '快捷支付' ,
                'value' => self::FAST_PAY ,
            ] ,
            self::B2C => [
                'name' => '个人网银支付' ,
                'value' => self::B2C ,
            ] ,
            self::B2B => [
                'name' => '企业网银支付' ,
                'value' => self::B2B ,
            ] ,
        ];
    }

    /**
     * @describe: 获取所有支付方式
     * @time:2022/11/7 10:49
     * @return array
     */
    public static function get_val(): array
    {
        $data = [];
        $arr = self::data();
        foreach ($arr as $k => $v) {
            $data[] = $v[''];
        }
        return $data;
    }
}