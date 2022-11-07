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
 * Time: 10:28
 */

namespace Sheng\Pay;


use Sheng\Pay\enum\PayType;

class CheckParam
{
    private static $error;

    /**
     * @describe: 验证支付参数
     * @time:2022/11/7 11:20
     * @param $order_no
     * @param $pay_channel
     * @param $pay_amt
     * @param $goods_title
     * @param $extend
     * @return bool
     */
    public static function check($order_no , $pay_channel , $pay_amt , $goods_title , $extend)
    {
        if (empty($order_no)) {
            self::$error = '订单号不为空';
            return false;
        }
        if (empty($pay_channel)) {
            self::$error = '支付渠道不能为空';
            return false;
        }
        if (empty($pay_amt)) {
            self::$error = '支付金额不能为空';
            return false;
        }
        if (empty($goods_title)) {
            self::$error = '商品标题不能为空';
            return false;
        }
        if (mb_strlen($order_no , 'utf-8') > 64) {
            self::$error = '订单号长度超出限制';
            return false;
        }
        $pay_type = PayType::get_val();
        if (!in_array($pay_channel , $pay_type , true)) {
            self::$error = '支付方式不正确';
            return false;
        }
        if ((PayType::ALIPAY_LITE || PayType::ALIPAY_PUB) && empty($extend['buyer_id'])) {
            self::$error = '买家的支付宝用户 id 不能为空';
            return false;
        }

        if ((PayType::ALIPAY_SCAN || PayType::WX_SCAN || PayType::UNION_SCAN) && empty($extend['auth_code'])) {
            self::$error = '扫码设备读出的条形码或者二维码信息不能为空';
            return false;
        }
        if ((PayType::WX_PUB || PayType::WX_LITE) && empty($extend['open_id'])) {
            self::$error = '微信用户关注商家公众号的 openid 不能为空';
            return false;
        }

        if (PayType::UNION_WAP && empty($extend['user_identity_id'])) {
            self::$error = '云闪付用户唯一标识 不能为空';
            return false;
        }
        if (PayType::UNION_WAP && empty($extend['client_ip'])) {
            self::$error = '发起支付的设备ip地址 不能为空';
            return false;
        }

        if (PayType::UNION_ONLINE && empty($extend['callback_url'])) {
            self::$error = '商户前端页面地址 不能为空';
            return false;
        }
        if (PayType::UNION_ONLINE && empty($extend['client_ip'])) {
            self::$error = '发起支付的用户端ip 不能为空';
            return false;
        }
        if (PayType::FAST_PAY && empty($extend['token_no'])) {
            self::$error = 'Adapay生成的银行卡唯一标识 不能为空';
            return false;
        }
        if ((PayType::B2C || PayType::B2B) && empty($extend['acct_issr_id'])) {
            self::$error = '个人网银支持的银行bank_code 不能为空';
            return false;
        }
        if ((PayType::B2C || PayType::B2B) && empty($extend['card_type'])) {
            self::$error = '银行卡类型 不能为空';
            return false;
        }
        if ((PayType::B2C || PayType::B2B) && empty($extend['client_ip'])) {
            self::$error = '发起支付的用户端ip 不能为空';
            return false;
        }
        if ((PayType::B2C || PayType::B2B) && empty($extend['callback_url'])) {
            self::$error = '商户前端页面地址 不能为空';
            return false;
        }
        return true;
    }

    public static function getError()
    {
        return self::$error;
    }
}


