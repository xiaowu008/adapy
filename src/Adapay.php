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
 * Time: 16:07
 */

namespace Sheng\Pay;


use Sheng\Pay\tool\AdaTools;
use Sheng\Pay\tool\Config;
use Sheng\Pay\tool\Request;

/**
 * @name:汇支付
 * Class Adapay
 * @package Sheng\Pay
 */
class Adapay extends Config
{
    static private $instance;
    public static $split = '_';
    /**
     * @notes:支付对象
     * @var string
     */
    public static $payments_url = '/v1/payments';
    /**
     * @notes:个人用户
     * @var string
     */
    public static $member_url = '/v1/members';
    /**
     * @notes:企业用户
     * @var string
     */
    public static $corp_member_url = '/v1/corp_members';
    /**
     * @notes:结算账户
     * @var string
     */
    public static $settle_account_url = "/v1/settle_accounts";
    /**
     * @notes:钱包收银台
     * @var string
     */
    public static $money_url = "/v1/cashs";
    /**
     * @notes:取现查询
     * @var string
     */
    public static $money_account_url = "/v1/account";
    /**
     * @notes:收银台对象
     * @var string
     */
    public static $check_money_url = "/v1/checkout";
    /**
     * @notes:钱包收银台
     * @var string
     */
    public static $wallet_login_url = "/v1/walletLogin";
    /**
     * @notes:快捷支付
     * @var string
     */
    public static $fast_pay_url = "/v1/fast_pay";
    /**
     * @notes:卡
     * @var string
     */
    public static $fast_card_url = "/v1/fast_card";
    /**
     * @notes:解冻账号
     * @var string
     */
    public static $unfreeze_url = "/v1/settle_accounts/unfreeze";
    /**
     * @notes:冻结账号
     * @var string
     */
    public static $freeze_url = "/v1/settle_accounts/freeze";
    /**
     * @notes:转账交易
     * @var string
     */
    public static $commissions_url = "/v1/settle_accounts/commissions";
    /**
     * @notes:转账交易
     * @var string
     */
    public static $transfer_url = "/v1/settle_accounts/commissions";
    /**
     * @notes:支付撤销对象
     * @var string
     */
    public static $reverse_url = "/v1/payments/reverse";


    //*************聚合支付

    /**
     * @describe: 创建支付信息
     * @time:2022/11/7 10:17
     * @param string $order_no String(64)  请求订单号，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
     * @param string $pay_channel String(20)  支付渠道，参见 支付渠道
     * @param string $pay_amt String(14)  交易金额，必须大于0，保留两位小数点，如0.10、100.05等
     * @param string $goods_title String(64) 商品标题
     * @param string $goods_desc String(127)  商品描述信息，微信小程序和微信公众号该字段最大长度42个字符
     * @param array $special 自定义参数，会拼接在订单中回调时自动返回 一维数组
     * @param array $extend
     * {
     * pay_mode  String(20) 支付模式，delay- 延时分账模式（值为 delay 时，div_members 字段必须为空）；值为空时并且div_mermbers不为空时，表示实时分账；值为空时并且div_mermbers也为空时，表示不分账；
     * goods_desc String(127)  商品描述信息，微信小程序和微信公众号该字段最大长度42个字符
     * currency  String(3)  3位 ISO 货币代码，小写字母，默认为人民币：cny，详见 货币代码
     * div_members String  分账对象信息列表，最多仅支持7个分账方，json 数组形式，详见 分账对象信息列表
     * description String(128) 订单附加说明
     * time_expire String(20) 订单失效时间，输入格式：yyyyMMddHHmmss，最长失效时间为微信、支付宝：反扫类：3分钟；非反扫类：2小时；云闪付：1天，值为空时默认最长时效时间
     * device_info Map 前端设备信息，详见 设备信息
     * expend Map 支付渠道额外参数，JSON格式，条件可输入，详见 支付渠道 expend参数
     * notify_url  String(250) 异步通知地址，url为http/https路径，服务器POST回调，URL 上请勿附带参数
     * fee_mode String(1)  手续费收取模式：O-商户手续费账户扣取手续费，I-交易金额中扣取手续费；值为空时，默认值为I；若为O时，分账对象列表中不支持传入手续费承担方
     * }
     * @return bool|mixed
     */
    public static function create_pay(string $order_no , string $pay_channel , string $pay_amt = '0.01' , string $goods_title = '' , string $goods_desc = '' , $special = [] , $extend = [])
    {
        if ($special) {
            $str = implode(self::$split , $special);
            $order_out_no = $order_no . self::$split . $str;
        } else {
            $order_out_no = $order_no;
        }

        $device_info = ['device_ip' => SDKTools::get_client_ip()];
        if ($extend && !empty($extend['device_info'])) {
            $device_info = array_merge([
                'device_ip' => SDKTools::get_client_ip()
            ] , $extend['device_info']);
        }
        if (!CheckParam::check($order_no , $pay_channel , $pay_amt , $goods_title , $extend)) {
            self::$error = CheckParam::getError();
            return false;
        }

        $params = array_merge([
            'app_id' => self::$app_id ,
            'order_no' => $order_out_no ,
            'pay_channel' => $pay_channel ,
            'pay_amt' => $pay_amt ,
            'goods_title' => $goods_title ,
            'goods_desc' => $goods_desc ?? $goods_title ,
            'device_info' => $device_info ,
            'currency' => 'cny' ,
            'sign_type' => 'RSA2' ,
        ] , $extend);
        $req_url = self::$gateWayUrl . self::$payments_url;
        return self::handle($req_url , $params , 'post');
    }

    /**
     * @describe: 返回处理
     * @time:2022/11/7 11:16
     * @param $data 获取的参数
     * @return array|bool order_no-订单号 special-自定义参数
     */
    public static function notifyCallback($data)
    {
        if (empty($data)) {
            self::$error = '请求参数错误';
            return false;
        }
        if (empty($data['sign'])) {
            self::$error = 'sign 不能为空';
            return false;
        }
        $is_check = AdaTools::verifySign($data['data'] , $data['sign']);
        if ($is_check) {
            $order_data = json_decode($data['data'] , true);
            if ($data['type'] === 'payment.succeeded' && $order_data['status'] === 'succeeded') {
                $order_out_no = $order_data['order_no'];
                $result = explode(self::$split , $order_out_no);
                $order_no = $result[0];
                unset($result[0]);
                return [
                    'order_no' => $order_no ,
                    'special' => $result
                ];
            }
            self::$error = '查询支付未成功';
            return false;
        }
        self::$error = '签名验证失败';
        return false;
    }

    /**
     * @describe: 创建支付对象
     * @time:2022/11/4 16:16
     * @param string $order_no
     * @param string $pay_channel
     * @param string $pay_amt 交易金额，必须大于0，保留两位小数点，如0.10、100.05等
     * @param string $goods_title 商品标题
     * @param string $time_expire 订单失效时间，输入格式：yyyyMMddHHmmss，最长失效时间为微信、支付宝：反扫类：3分钟；非反扫类：2小时；云闪付：1天，值为空时默认最长时效时间
     * @param string $goods_desc 商品描述信息，微信小程序和微信公众号该字段最大长度42个字符
     * @param array $special 自定义参数，会拼接在订单中回调时自动返回
     * @param array $extend
     * {
     * description String(128) $description 订单附加说明
     * pay_mode String(20) 支付模式，delay- 延时分账模式（值为 delay 时，div_members 字段必须为空）；值为空时并且div_mermbers不为空时，表示实时分账；值为空时并且div_mermbers也为空时，表示不分账；
     * currency String(3) 3位 ISO 货币代码，小写字母，默认为人民币：cny，详见 货币代码
     * div_members  String 分账对象信息列表，最多仅支持7个分账方，json 数组形式，详见 分账对象信息列表
     * description String(128) 订单附加说明
     * time_expire  String(20) 订单失效时间，输入格式：yyyyMMddHHmmss，最长失效时间为微信、支付宝：反扫类：3分钟；非反扫类：2小时；云闪付：1天，值为空时默认最长时效时间
     * expend Map 支付渠道额外参数，JSON格式，条件可输入，详见 支付渠道 expend参数
     * notify_url  String(250) 异步通知地址，url为http/https路径，服务器POST回调，URL 上请勿附带参数
     * fee_mode  String(1) 手续费收取模式：O-商户手续费账户扣取手续费，I-交易金额中扣取手续费；值为空时，默认值为I；若为O时，分账对象列表中不支持传入手续费承担方
     * }
     * @return array|bool
     */
    public static function payment($order_no = '' , $pay_channel = '' , $pay_amt = '0.01' , $goods_title = '' , $time_expire = '' , $goods_desc = '' , $special = [] , $extend = [])
    {
        if ($special) {
            $str = implode(self::$split , $special);
            $order_out_no = $order_no . self::$split . $str;
        } else {
            $order_out_no = $order_no;
        }
        $params = array_merge([
            'app_id' => self::$app_id ,
            'order_no' => $order_out_no ,
            'pay_channel' => $pay_channel ,
            'pay_amt' => $pay_amt ,
            'time_expire' => $time_expire ,
            'goods_title' => $goods_title ,
            'goods_desc' => $goods_desc ?? $goods_title ,
            'device_info' => [
                'device_ip' => SDKTools::get_client_ip()
            ] ,
            'currency' => 'cny' ,
            'sign_type' => 'RSA2' ,
        ] , $extend);
        $req_url = self::$gateWayUrl . self::$payments_url;
        return self::handle($req_url , $params , 'post');
    }

    /**
     * @describe: 查询支付对象列表
     * @time:2022/11/6 16:21
     * @param array $extend
     * {
     * order_no String(64) 创建支付对象时上送的请求订单号，只能为英文、数字或者下划线的一种或多种组合
     * payment_id String(64) Adapay 生成的支付对象 id
     * page_index int 当前页码，取值范围 1~300000，默认值为 1
     * page_size int 页面容量，取值范围 1~20，默认值为 10
     * created_gte String 查询大于等于创建时间（13位时间戳）
     * created_lte String  查询小于等于创建时间（13位时间戳）；若不为空时，created_gte 字段值不能为空且小于created_lte 时间
     * }
     * @return array|bool
     */
    public static function queryOrderList($extend = [])
    {
        $params = [];
        !empty($extend['order_no']) && $params['order_no'] = $extend['order_no'];
        !empty($extend['payment_id']) && $params['payment_id'] = $extend['payment_id'];

        if (!empty($extend['created_gte']) && !empty($extend['created_lte'])) {
            $params['created_gte'] = $extend['created_gte'];
            $params['created_lte'] = $extend['created_lte'];
        }
        $params = array_merge([
            'app_id' => self::$app_id ,
            'page_index' => $extend['page_index'] ?? 1 ,
            'page_size' => $extend['page_size'] ?? 10
        ] , $params);

        ksort($params);
        $req_url = self::$gateWayUrl . self::$payments_url . "/list";
        return self::handle($req_url , $params , 'get');
    }

    /**
     * @describe: 查询支付对象
     * @time:2022/11/6 16:24
     * @param string $payment_id String 由 Adapay 生成的支付对象 id
     * @return bool|mixed
     */
    public static function queryOrderDetail($payment_id)
    {
        $params = ['payment_id' => $payment_id];
        ksort($params);
        $req_url = self::$gateWayUrl . self::$payments_url . "/" . $payment_id;
        return self::handle($req_url , $params , 'get');
    }

    /**
     * @describe: 关闭支付对象
     * @time:2022/11/4 18:15
     * @param string $payment_id 由 Adapay 生成的支付对象 id
     * @param array $extend
     * {
     * reason  String(255)  关单描述
     * expend  String(255)  扩展域
     * notify_url  String(250)   异步通知地址，url为http/https路径，服务器POST回调，URL 上请勿附带参数
     * }
     * @return array|bool
     */
    public static function orderClose($payment_id , $extend = [])
    {
        $params = array_merge([
            'payment_id' => $payment_id
        ] , $extend);
        $req_url = self::$gateWayUrl . self::$payments_url . "/" . $payment_id . "/close";
        return self::handle($req_url , $params , 'post');
    }

    //=============退款对象

    /**
     * @describe: 创建退款对象
     * @time:2022/11/4 18:22
     * @param string $id 当支付确认成功后进行退款，请传入支付确认对象的id；当实时分账成功后进行退款，请传入支付对象的id。
     * @param string $refund_order_no 请求订单号，只能为英文、数字或者下划线的一种或多种组合
     * @param string $refund_amt 退款金额，若退款金额小于原交易金额，则认为是部分退款，必须大于0，保留两位小数点，如0.10、100.05等
     * @param array $extend
     * {
     * reason String(512)  退款描述
     * expend  String(512) 扩展域
     * device_info  String(1024) 前端设备信息，详见 设备信息
     * div_members String(1024) 分账对象信息列表，json 形式，详见 分账对象信息列表 ；若原交易对象未分账，则创建退款对象时，该字段不传；若原交易对象分账，则退款分账对象必须在原交易参与的分账方范围内，分账对象列表内的总金额必须等于退款金额，每个分账对象的退分账金额必须满足：退分账金额+已退分账金额 <= 原交易分账对象的分账金额。
     * notify_url String(250) 异步通知地址，url为http/https路径，服务器POST回调，URL 上请勿附带参数
     * fail_fast String(1)  快速失败标识：N 或者为空时-退款失败及时返回错误，仅支持实时分账场景；为Y时，退款失败会走 Adapay 人工审核流程，核对后再次出款或回账，最晚 T+3 返回终态结果
     * }
     * @return array|bool
     */
    public static function createRefundOrder($id , $refund_order_no , $refund_amt , $extend = [])
    {
        $params = array_merge([
            'id' => $id ,
            'refund_order_no' => $refund_order_no ,
            'refund_amt' => $refund_amt
        ] , $extend);
        $req_url = self::$gateWayUrl . self::$payments_url . "/" . $id . "/refunds";
        return self::handle($req_url , $params , 'post');
    }

    /**
     * @describe: 查询退款对象
     * @time:2022/11/4 18:25
     * @param string $payment_id Adapay生成的支付对象id，三者必传其一
     * @param string $refund_order_no 退款订单号，三者必传其一
     * @param string refund_id Adapay生成的退款对象id，三者必传其一
     * @return array|bool
     */
    public static function queryRefundOrder($refund_order_no = '' , $payment_id = '' , $refund_id = '')
    {
        $request_params = [];
        if ($refund_order_no) {
            $request_params['refund_order_no'] = $refund_order_no;
        }
        if ($payment_id) {
            $request_params['payment_id'] = $payment_id;
        }
        if ($refund_id) {
            $request_params['refund_id'] = $refund_id;
        }
        if (count($request_params) != 0) {
            self::$error = '请求参数错误';
            return false;
        }
        $req_url = self::$gateWayUrl . self::$payments_url . "/refunds";
        return self::handle($req_url , $request_params , 'get');
    }



    //=============支付确认对象

    /**
     * @describe: 创建支付确认对象
     * @time:2022/11/6 14:23
     * @param $payment_id String(64)  Adapay生成的支付对象id
     * @param $order_no  String(64) 请求订单号，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
     * @param $confirm_amt  String(14)  确认金额，必须大于0，保留两位小数点，如0.10、100.05等。必须小于等于原支付金额-已确认金额-已撤销金额。
     * @param array $extend
     * {
     * description  String(128)  附加说明
     * div_members  List 分账对象信息列表，一次请求最多仅支持7个分账方。json对象 形式，详见 https://docs.adapay.tech/api/appendix.html#divmembers
     * fee_mode String(1) 手续费收取模式：O-商户手续费账户扣取手续费，I-交易金额中扣取手续费；值为空时，默认值为I；若为O时，分账对象列表中不支持传入手续费承担方
     * }
     * @return array|bool
     */
    public function createPaymentConfirm($payment_id , $order_no , $confirm_amt , $extend = [])
    {
        $params = array_merge([
            'order_no' => $order_no ,
            'payment_id' => $payment_id ,
            'confirm_amt' => $confirm_amt
        ] , $extend);
        $req_url = self::$gateWayUrl . self::$payments_url . "/confirm";
        return self::handle($req_url , $params , 'post');
    }

    /**
     * @describe: 查询支付确认对象
     * @time:2022/11/6 14:26
     * @param $payment_confirm_id String(64)  Adapay生成的支付确认对象id
     * @return array|bool
     */
    public function queryPaymentConfirm($payment_confirm_id)
    {
        $params = [
            'payment_confirm_id' => $payment_confirm_id
        ];
        $req_url = self::$gateWayUrl . self::$payments_url . "/confirm/" . $payment_confirm_id;
        return self::handle($req_url , $params , 'get');
    }

    /**
     * @describe: 查询支付确认对象列表
     * @time:2022/11/6 16:29
     * @param array $extend
     * {
     * order_no String(64) 创建支付对象时上送的请求订单号，只能为英文、数字或者下划线的一种或多种组合
     * payment_id String(64) Adapay 生成的支付对象 id
     * page_index int 当前页码，取值范围 1~300000，默认值为 1
     * page_size int 页面容量，取值范围 1~20，默认值为 10
     * created_gte String 查询大于等于创建时间（13位时间戳）
     * created_lte String  查询小于等于创建时间（13位时间戳）；若不为空时，created_gte 字段值不能为空且小于created_lte 时间
     * }
     * @return bool|mixed
     */
    public function queryPaymentConfirmList($extend = [])
    {
        $params = [];
        !empty($extend['order_no']) && $params['order_no'] = $extend['order_no'];
        !empty($extend['payment_id']) && $params['payment_id'] = $extend['payment_id'];

        if (!empty($extend['created_gte']) && !empty($extend['created_lte'])) {
            $params['created_gte'] = $extend['created_gte'];
            $params['created_lte'] = $extend['created_lte'];
        }
        $params = array_merge([
            'app_id' => self::$app_id ,
            'page_index' => $extend['page_index'] ?? 1 ,
            'page_size' => $extend['page_size'] ?? 10
        ] , $params);
        ksort($params);
        $req_url = self::$gateWayUrl . self::$payments_url . "/confirm/list";
        return self::handle($req_url , $params , 'get');
    }



    //=============支付撤销对象

    /**
     * @describe: 创建支付撤销对象
     * @time:2022/11/6 14:31
     * @param $payment_id   String(64)  Adapay生成的支付对象id
     * @param $order_no String(64)  请求订单号，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
     * @param array $extend
     * {
     * notify_url String(250) 异步通知地址，url为http/https路径，服务器POST回调，URL 上请勿附带参数。
     * reverse_amt String(14) 撤销金额，必须大于0，保留两位小数点，如0.10、100.05等。撤销金额必须小于等于支付金额 - 已确认金额 - 已撤销（撤销成功+撤销中）金额。
     * reason  String(512)  撤销描述
     * expand  String(512)  扩展域
     * device_info String(1024)  设备静态信息，详见 https://docs.adapay.tech/api/appendix.html#deviceinfo
     * }
     * @return array|bool
     */
    public function createPaymentReverse($payment_id , $order_no , $extend = [])
    {
        $params = array_merge([
            'order_no' => $order_no ,
            'payment_id' => $payment_id ,
            'app_id' => self::$app_id
        ] , $extend);
        $req_url = self::$gateWayUrl . self::$reverse_url;
        return self::handle($req_url , $params , 'post');
    }

    /**
     * @describe: 查询支付撤销对象
     * @time:2022/11/6 14:35
     * @param $reverse_id String(64) Adapay生成的支付撤销对象id
     * @return array|bool
     */
    public function queryPaymentReverse($reverse_id)
    {
        $params['reverse_id'] = $reverse_id;
        ksort($params);
        $req_url = self::$gateWayUrl . self::$reverse_url . "/" . $reverse_id;
        return self::handle($req_url , $params , 'get');
    }

    /**
     * @describe: 查询支付撤销对象列表
     * @time:2022/11/6 16:32
     * @param array $extend
     * {
     * payment_id String(64) Adapay 生成的支付对象 id
     * page_index int 当前页码，取值范围 1~300000，默认值为 1
     * page_size int 页面容量，取值范围 1~20，默认值为 10
     * created_gte String 查询大于等于创建时间（13位时间戳）
     * created_lte String  查询小于等于创建时间（13位时间戳）；若不为空时，created_gte 字段值不能为空且小于created_lte 时间
     * }
     * @return array|bool
     */
    public function queryPaymentReverseList($extend = [])
    {
        $params = [];
        !empty($extend['payment_id']) && $params['payment_id'] = $extend['payment_id'];

        if (!empty($extend['created_gte']) && !empty($extend['created_lte'])) {
            $params['created_gte'] = $extend['created_gte'];
            $params['created_lte'] = $extend['created_lte'];
        }
        $params = array_merge([
            'app_id' => self::$app_id ,
            'page_index' => $extend['page_index'] ?? 1 ,
            'page_size' => $extend['page_size'] ?? 10
        ] , $params);

        ksort($params);
        $req_url = self::$gateWayUrl . self::$reverse_url . "/list";
        return self::handle($req_url , $params , 'get');
    }



    //=============个人用户

    /**
     * @describe: 创建用户对象
     * @time:2022/11/4 18:36
     * @param string $member_id String(64) 商户下的用户id，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
     * @param array $extend
     * {
     * location  String(128) 用户地址
     * email  String(64) 用户邮箱
     * gender String(16)  MALE：男，FEMALE：女，为空时表示未填写
     * nickname String(16) 用户昵称
     * tel_no  String(11) 用户手机号，使用 收银台对象 功能必填 若创建用户对象用于分账功能，则手机号字段一定不要上送
     * user_name String(64) 用户姓名，使用 收银台对象 功能必填 若创建用户对象用于分账功能，则用户姓名字段一定不要上送
     * cert_type String(2)  证件类型，仅支持：00-身份证，使用 收银台对象 功能必填 若创建用户对象用于分账功能，则证件类型字段一定不要上送
     * cert_id   String(64)  证件号，使用 收银台对象 功能必填 若创建用户对象用于分账功能，则证件号字段一定不要上送
     * }
     * @return array|bool
     */
    public static function createUser($member_id , $extend = [])
    {
        $params = array_merge([
            'app_id' => self::$app_id ,
            'member_id' => $member_id
        ] , $extend);
        $req_url = self::$gateWayUrl . self::$member_url;
        return self::handle($req_url , $params , 'post');
    }

    /**
     * @describe: 查询用户对象
     * @time:2022/11/4 18:41
     * @param string $member_id 商户下的用户id，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
     * @return array|bool
     */
    public static function queryUser(string $member_id)
    {
        $params = array_merge([
            'app_id' => self::$app_id ,
            'member_id' => $member_id
        ]);
        ksort($params);
        $req_url = self::$gateWayUrl . self::$member_url . "/" . $member_id;
        return self::handle($req_url , $params , 'get');
    }

    /**
     * @describe: 更新用户对象
     * @time:2022/11/4 18:36
     * @param string $member_id 商户下的用户id，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
     * @param array $extend
     * {
     * location  String(128) 用户地址
     * email  String(64) 用户邮箱
     * gender String(16)  MALE：男，FEMALE：女，为空时表示未填写
     * nickname String(16) 用户昵称
     * tel_no  String(11) 用户手机号，使用 收银台对象 功能必填 若创建用户对象用于分账功能，则手机号字段一定不要上送
     * disabled   String(1)  是否禁用该用户，Y：是，N：否
     * }
     * @return array|bool
     */
    public static function updateUser(string $member_id , $extend = [])
    {
        $params = array_merge([
            'app_id' => self::$app_id ,
            'member_id' => $member_id
        ] , $extend);
        $req_url = self::$gateWayUrl . self::$member_url . '/update';
        return self::handle($req_url , $params , 'post');
    }

    /**
     * @describe: 查询用户对象列表
     * @time:2022/11/4 18:03
     * @param int $page_index 当前页码，取值范围 1~300000，默认值为 1
     * @param int $page_size 页面容量，取值范围 1~20，默认值为 10
     * @param string $created_gte 查询大于等于创建时间（13位时间戳）
     * @param string $created_lte 查询小于等于创建时间（13位时间戳）；若不为空时，created_gte 字段值不能为空且小于created_lte 时间
     * @return array|bool
     */
    public static function queryUserList($page_index = 1 , $page_size = 10 , $created_gte = '' , $created_lte = '')
    {
        $params = [];
        if (!empty($created_gte) && !empty($created_lte)) {
            $params['created_gte'] = $created_gte;
            $params['created_lte'] = $created_lte;
        }
        $params = array_merge([
            'app_id' => self::$app_id ,
            'page_index' => $page_index ,
            'page_size' => $page_size
        ] , $params);
        $req_url = self::$gateWayUrl . self::$member_url . "/list";
        return self::handle($req_url , $params , 'get');
    }




    //=============企业用户

    /**
     * @describe: 创建企业用户对象
     * @time:2022/11/4 19:04
     * @param array $params
     * {
     * app_id  String(64)  控制台 主页面应用的app_id
     * order_no  String(64)  请求订单号，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
     * member_id  String(64)  商户下的用户id，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
     * name String(50) 企业名称
     * prov_code String(4)  省份编码 （省市编码）
     * area_code  String(4) 地区编码 （省市编码）
     * social_credit_code String(18) 统一社会信用码
     * social_credit_code_expires  String(8)  统一社会信用证有效期
     * business_scope   String(200)  经营范围
     * legal_person String(20) 法人姓名
     * legal_cert_id  String(20) 法人身份证号码
     * legal_cert_id_expires  String(8)  法人身份证有效期
     * legal_mp  String(11) 法人手机号
     * address   String(256)  企业地址
     * attach_file  File 上传附件，传入的中文文件名称为 UTF-8 字符集 URLEncode 编码后的字符串。内容须包含三证合一证件照、法人身份证正面照、法人身份证反面照、开户银行许可证照。 压缩 zip包后上传，最大限制为 9 M。
     * }
     * @param array $extend
     * {
     * zip_code String(6) 邮编
     * telphone   String(30)  企业电话
     * email  String(40)  企业邮箱
     * bank_code  String(8) 银行代码，如果需要自动开结算账户，本字段必填（详见附录 银行代码）
     * bank_acct_type String(1) 银行账户类型：1-对公；2-对私，如果需要自动开结算账户，本字段必填
     * card_no  String(40) 银行卡号，如果需要自动开结算账户，本字段必填
     * card_name   String(64) 银行卡对应的户名，如果需要自动开结算账户，本字段必填；若银行账户类型是对公，必须与企业名称一致
     * notify_url  String(250)   异步通知地址，url为http/https路径，服务器POST回调，URL 上请勿附带参数
     * }
     * @return array|bool
     */
    public static function createCorpMember($params = array() , $extend = [])
    {
        $request_params = array_merge($params , $extend);
        $req_url = self::$gateWayUrl . self::$corp_member_url;
        ksort($request_params);
        $request_params = self::do_empty_data($request_params);
        $sign_request_params = $request_params;
        unset($sign_request_params['attach_file']);
        ksort($sign_request_params);
        $sign_str = AdaTools::createLinkstring($sign_request_params);

        $header = self::get_request_header($req_url , $sign_str , self::$headerEmpty);
        $result = Request::curl_request($req_url , $request_params , $header);
        return self::handleResultData($result);
    }

    /**
     * @describe: 更新企业用户对象
     * @time:2022/11/4 19:08
     * @param $order_no String(64) 请求订单号，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
     * @param $member_id String(64) 商户下已创建成功的企业用户id
     * @param array $extend
     * {
     * name  String(50)  企业名称
     * prov_code String(4) 省份编码 （省市编码）
     * area_code String(4) 地区编码 （省市编码）
     * social_credit_code_expires String(8)  统一社会信用证有效期
     * business_scope   String(200)  经营范围
     * legal_person String(20)  法人姓名
     * legal_cert_id String(20)  法人身份证号码
     * legal_cert_id_expires  String(8) 法人身份证有效期
     * legal_mp  String(11)  法人手机号
     * address   String(256) 企业地址
     * zip_code  String(6) 邮编
     * telphone  String(30) 企业电话
     * email String(40) 企业邮箱
     * attach_file    File  上传附件，传入的中文文件名称为 UTF-8 字符集 URLEncode 编码后的字符串。内容须包含三证合一证件照、法人身份证正面照、法人身份证反面照、开户银行许可证照。 压缩 zip包后上传，最大限制为 9 M。
     * notify_url   String(250)  异步通知地址，url为http/https路径，服务器POST回调，URL 上请勿附带参数
     * }
     * @return array|bool
     */
    public static function updateCorpMember($order_no , $member_id , $extend = [])
    {
        $request_params = array_merge([
            'app_id' => self::$app_id ,
            'order_no' => $order_no ,
            'member_id' => $member_id
        ] , $extend);

        $request_params = self::do_empty_data($request_params);
        $req_url = self::$gateWayUrl . self::$corp_member_url . "/update";
        ksort($request_params);
        $sign_request_params = $request_params;
        unset($sign_request_params['attach_file']);
        ksort($sign_request_params);
        $sign_str = AdaTools::createLinkstring($sign_request_params);

        $header = self::get_request_header($req_url , $sign_str , self::$headerEmpty);
        $result = Request::curl_request($req_url , $request_params , $header);
        return self::handleResultData($result);
    }

    /**
     * @describe: 查询企业用户对象
     * @time:2022/11/4 19:12
     * @param string $member_id String(64) 商户下的用户id，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
     * @return array|bool
     */
    public static function queryCorpMember($member_id)
    {
        $params = [
            'app_id' => self::$app_id ,
            'member_id' => $member_id
        ];
        ksort($params);
        $req_url = self::$gateWayUrl . self::$corp_member_url . DIRECTORY_SEPARATOR . $member_id;
        return self::handle($req_url , $params , 'get');
    }


    //=============结算账户

    /**
     * @describe: 查询账户余额
     * @time:2022/11/6 16:45
     * @param $cust_id String(16) Adapay商户号
     * @return array|bool
     */
    public function balance($cust_id)
    {
        ksort($params);
        $req_url = self::$gateWayUrl . self::$settle_account_url . "/balance";
        return self::handle($req_url , $params , 'get');
    }

    /**
     * @describe: 创建结算账户对象
     * @time:2022/11/4 19:22
     * @param $member_id String(64) 商户下的用户id，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
     * @param $channel string 目前仅支持：bank_account（银行卡）
     * @param $account_info Object 结算账户信息，参见
     * @return array|bool
     */
    public function createSettle($member_id , $channel , $account_info)
    {
        $params = array_merge([
            'app_id' => self::$app_id ,
            'member_id' => $member_id ,
            'channel' => $channel ,
            'account_info' => $account_info
        ]);
        $req_url = self::$gateWayUrl . self::$settle_account_url;
        return self::handle($req_url , $params , 'post');
    }

    /**
     * @describe: 查询结算账户对象
     * @time:2022/11/4 19:32
     * @param $member_id String(64)  商户下的用户id，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
     * @param $begin_date String(8)  结算起始日期，格式为 yyyyMMdd
     * @param $end_date String(8)   结算结束日期，格式为 yyyyMMdd，日期间隔必须小于等于31天
     * @param string $settle_account_id String   由Adapay生成的结算账户对象id，若查询商户本身时，可以为空
     * @return array|bool
     */
    public function querySettle($member_id , $begin_date , $end_date , $settle_account_id = '')
    {
        $params = array_merge([
            'app_id' => self::$app_id ,
            'member_id' => $member_id ,
            'begin_date' => $begin_date ,
            'end_date' => $end_date ,
            'settle_account_id' => $settle_account_id ,
        ]);
        ksort($request_params);
        $req_url = self::$gateWayUrl . self::$settle_account_url . "/" . $settle_account_id;
        return self::handle($req_url , $params , 'get');
    }

    /**
     * @describe: 删除结算账户对象
     * @time:2022/11/4 19:26
     * @param $member_id String(64)  商户下的用户id，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
     * @param $settle_account_id String(64) 由 Adapay 生成的结算账户对象 id
     * @return array|bool
     */
    public function deleteSettle($member_id , $settle_account_id)
    {
        $params = array_merge([
            'app_id' => self::$app_id ,
            'member_id' => $member_id ,
            'settle_account_id' => $settle_account_id ,
        ]);
        $req_url = self::$gateWayUrl . self::$settle_account_url . "/delete";
        return self::handle($req_url , $params , 'post');
    }

    /**
     * @describe: 查询结算账户对象
     * @time:2022/11/4 19:26
     * @param $member_id String(64)  商户下的用户id，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
     * @param $settle_account_id String(64) 由 Adapay 生成的结算账户对象 id
     * @return array|bool
     */
    public function detailSettle($member_id , $settle_account_id)
    {
        $params = array_merge([
            'app_id' => self::$app_id ,
            'member_id' => $member_id ,
            'settle_account_id' => $settle_account_id ,
        ]);
        ksort($request_params);
        $req_url = self::$gateWayUrl . self::$settle_account_url . "/settle_details";
        return self::handle($req_url , $params , 'get');
    }

    /**
     * @describe: 修改结算配置
     * @time:2022/11/4 19:28
     * @param $member_id String(64)  商户下的用户id，若为商户本身时，传入0
     * @param array $extend
     * {
     * settle_account_id  String(64)  Adapay系统返回的结算账户id，若为商户本身时，不传该值
     * min_amt  String(16) 结算起始金额 ( 0.00格式，整数部分最长13位，小数部分最长2位) min_amt， remained_amt，channel_remark至少有一个不为空
     * remained_amt  String(16)  结算留存金额 ( 0.00格式，整数部分最长13位，小数部分最长2位)
     * channel_remark  String(200)  结算信息摘要，银行出款时摘要信息
     * }
     * @return array|bool
     */
    public function updateSettle($member_id , $extend = [])
    {
        $params = array_merge([
            'app_id' => self::$app_id ,
            'member_id' => $member_id
        ] , $extend);
        $req_url = self::$gateWayUrl . self::$settle_account_url . "/modify";
        return self::handle($req_url , $params , 'post');
    }




    //=============钱包收银台

    /**
     * @describe: 创建取现对象
     * @time:2022/11/4 19:42
     * @param $payment_id String(64) Adapay生成的支付对象id
     * @param $order_no String(64)  请求订单号，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
     * @param $confirm_amt String(14)  确认金额，必须大于0，保留两位小数点，如0.10、100.05等。必须小于等于原支付金额-已确认金额-已撤销金额。
     * @param array $extend
     * {
     * description String(128)  附加说明
     * div_members  List  分账对象信息列表，一次请求最多仅支持7个分账方。json对象 形式，详见 分账对象信息列表
     * fee_mode  String(1)  手续费收取模式：O-商户手续费账户扣取手续费，I-交易金额中扣取手续费；值为空时，默认值为I；若为O时，分账对象列表中不支持传入手续费承担方
     * }
     * @return array|bool
     */
    public function createSubmit($payment_id , $order_no , $confirm_amt , $extend = [])
    {
        $params = array_merge([
            'payment_id' => $payment_id ,
            'order_no' => $order_no ,
            'confirm_amt' => $confirm_amt
        ] , $extend);
        $req_url = self::$gateWayUrl . self::$money_url;
        return self::handle($req_url , $params , 'post');
    }

    /**
     * @describe: 查询取现对象
     * @time:2022/11/6 16:50
     * @param $order_no
     * @return bool|mixed
     */
    public function querySubmit($order_no)
    {
        $params = ['order_no' => $order_no];
        ksort($request_params);
        $req_url = self::$gateWayUrl . self::$money_url . "/stat";
        return self::handle($req_url , $params , 'post');
    }

    /**
     * @describe: 创建钱包支付对象
     * @time:2022/11/6 10:45
     * @param $order_no String(64)  请求订单号，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
     * @param $cash_type  String(2)  取现类型：T1-T+1取现；D1-D+1取现；D0-即时取现；DM-可用额度取现（已入账金额如未取现，则转为可用额度，取现后实时到账）。
     * @param $cash_amt String(16) 取现金额，必须大于0，人民币为元，保留两位小数点，如0.10、100.05等
     * @param $member_id String(64)  用户对象的member_id，若是商户本身取现时，请传入0
     * @param array $extend
     * {
     * notify_url  String(250)  异步通知地址，url为http/https 路径，服务器 POST 回调，请不要地址上带有参数。
     * remark  String(200) 备注
     * fee_mode  手续费收取模式：O-商户手续费账户扣取手续费，I-交易金额中扣取手续费；值为空时，默认值为I；
     * }
     * @return array|bool
     */
    public function createAccount($order_no , $cash_type , $cash_amt , $member_id , $extend = [])
    {
        $params = array_merge([
            'app_id' => self::$app_id ,
            'order_no' => $order_no ,
            'cash_type' => $cash_type ,
            'cash_amt' => $cash_amt ,
            'member_id' => $member_id
        ] , $extend);
        $req_url = self::$gateWayUrl . self::$money_account_url . '/payment';
        return self::handle($req_url , $params , 'post');
    }



    //=============收银台对象

    /**
     * @describe: 创建收银台对象
     * @time:2022/11/6 10:53
     * @param $order_no String(64)  请求订单号，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
     * @param $pay_amt String(14) 交易金额，必须大于0，保留两位小数点，如0.10、100.05等
     * @param $goods_title String(64)  商品标题
     * @param string $goods_desc String(127)   商品描述信息
     * @param array $extend
     * {
     * member_id String(64)  商户下的用户id。支付渠道为 fast_pay 时，必传。只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
     * pay_channel  String(20)  支付渠道: fast_pay-快捷支付，online_pay-网银支付；为空时，默认为fast_pay。
     * pay_mode  String(20) 支付模式，delay- 延时分账模式；值为空时，表示实时分账；值为 delay 时，div_members 字段必须为空
     * div_members List 分账对象信息列表，一次请求最多仅支持7个分账方。json对象 形式，详见 分账对象信息列表
     * currency  String(3)  3位 ISO 货币代码，小写字母，默认为人民币：cny，详见 货币代码
     * time_expire  String(20)  订单失效时间，输入格式：yyyyMMddHHmmss，默认失效时间2小时；最短1分钟，最长不超过1天
     * description String(128) 订单附加说明
     * notify_url  String(250)  异步通知地址，url为http/https路径，服务器POST回调，URL 上请勿附带参数
     * callback_url String(250)  商户前端页面地址，支付成功或失败时，会向该地址跳转
     * fee_mode  String(1) 手续费收取模式：O-商户手续费账户扣取手续费，I-交易金额中扣取手续费；值为空时，默认值为I；若为O时，分账对象列表中不支持传入手续费承担方
     * }
     * @return array|bool
     */
    public function createCheckout($order_no , $pay_amt , $goods_title , $goods_desc = '' , $extend = [])
    {
        $params = array_merge([
            'app_id' => self::$app_id ,
            'order_no' => $order_no ,
            'pay_amt' => $pay_amt ,
            'goods_title' => $goods_title ,
            'goods_desc' => $goods_desc ?: $goods_title
        ] , $extend);
        $req_url = self::$gateWayUrl . self::$check_money_url;
        return self::handle($req_url , $params , 'post');
    }

    /**
     * @describe: 查询收银台对象列表
     * @time:2022/11/6 10:58
     * @param int $page_index int   当前页码，取值范围 1~300000，默认值为 1
     * @param int $page_size int  页面容量，取值范围 1~20，默认值为 10
     * @param string $created_gte String  查询大于等于创建时间（13位时间戳）
     * @param string $created_lte String 查询小于等于创建时间（13位时间戳）；若不为空时，created_gte 字段值不能为空且小于created_lte 时间
     * @param string $order_no String(64)  创建收银台对象时上送的请求订单号，只能为英文、数字或者下划线的一种或多种组合
     * @param $member_id String(64)  创建收银台对象时上送的商户下的用户id，只能为英文、数字或者下划线的一种或多种组合
     * @return array|bool
     */
    public function queryCheckoutList($page_index = 1 , $page_size = 10 , $created_gte = '' , $created_lte = '' , $order_no = '' , $member_id = '')
    {
        $params = [];
        !empty($order_no) && $params['order_no'] = $order_no;
        !empty($member_id) && $params['member_id'] = $member_id;

        if (!empty($created_gte) && !empty($created_lte)) {
            $params['created_gte'] = $created_gte;
            $params['created_lte'] = $created_lte;
        }
        $params = array_merge([
            'app_id' => self::$app_id ,
            'page_index' => $page_index ,
            'page_size' => $page_size
        ] , $params);

        ksort($params);
        $req_url = self::$gateWayUrl . self::$check_money_url . "/list";
        return self::handle($req_url , $params , 'post');
    }




    //=============钱包收银台

    /**
     * @describe: 钱包登录
     * @time:2022/11/6 16:53
     * @param $member_id String(64)  商户用户对象 id，只能为英文、数字或者下划线的一种或多种组合，若查询商户本身时，传入值0
     * @param $ip String(64)  请填写真实的用户客户端IP
     * @param array $extend
     * @return array|bool
     */
    public function login($member_id , $ip , $extend = [])
    {
        $params = array_merge([
            'app_id' => self::$app_id ,
            'member_id' => $member_id ,
            'ip' => $ip ,
        ] , $extend);
        $req_url = self::$gateWayUrl . self::$wallet_login_url;
        return self::handle($req_url , $params , 'post');
    }



    //=============转账交易

    /**
     * @describe: 创建转账交易
     * @time:2022/11/6 14:07
     * @param array $params
     * @return array|bool
     */
    public function createSettleAccountCommissions($params = array())
    {
        $request_params = self::do_empty_data($params);
        $req_url = self::$gateWayUrl . self::$commissions_url;
        $header = self::get_request_header($req_url , $request_params , self::$header);
        self::$result = Request::curl_request($req_url , $request_params , $header , $is_json = true);
        if (self::isErrorMsg()) {
            self::writeLog(self::$result);
            return false;
        }
        return self::$result;
    }

    /**
     * @describe: 查询转账交易
     * @time:2022/11/6 14:07
     * @param array $params
     * @return array|bool
     */
    public function querySettleAccountCommissions($params = array())
    {
        ksort($params);
        $request_params = self::do_empty_data($params);
        $req_url = self::$gateWayUrl . self::$commissions_url . "/list";
        $header = self::get_request_header($req_url , http_build_query($request_params) , self::$headerText);
        self::$result = Request::curl_request($req_url . "?" . http_build_query($request_params) , "" , $header , false);
        if (self::isErrorMsg()) {
            self::writeLog(self::$result);
            return false;
        }
        return self::$result;
    }

    /**
     * @describe: 创建转账交易
     * @time:2022/11/6 14:10
     * @param array $params
     * @return array|bool
     */
    public function createSettleAccountTransfer($params = array())
    {
        $request_params = self::do_empty_data($params);
        $req_url = self::$gateWayUrl . self::$transfer_url;
        $header = self::get_request_header($req_url , $request_params , self::$header);
        self::$result = Request::curl_request($req_url , $request_params , $header , $is_json = true);
        if (self::isErrorMsg()) {
            self::writeLog(self::$result);
            return false;
        }
        return self::$result;
    }

    /**
     * @describe: 查询转账交易
     * @time:2022/11/6 14:10
     * @param array $params
     * @return array|bool
     */
    public function querySettleAccountTransfer($params = array())
    {
        ksort($params);
        $request_params = self::do_empty_data($params);
        $req_url = self::$gateWayUrl . self::$transfer_url . "/list";
        $header = self::get_request_header($req_url , http_build_query($request_params) , self::$headerText);
        self::$result = Request::curl_request($req_url . "?" . http_build_query($request_params) , "" , $header , false);
        if (self::isErrorMsg()) {
            self::writeLog(self::$result);
            return false;
        }
        return self::$result;
    }


    //=============快捷支付

    public function payConfirmFast($params = array())
    {
        $req_url = self::$gateWayUrl . self::$fast_pay_url . "/confirm";
        return self::handle($req_url , $params , 'post');
    }

    public function paySmsCode($params = array())
    {
        $req_url = self::$gateWayUrl . self::$fast_pay_url . "/sms_code";
        return self::handle($req_url , $params , 'post');
    }

    /**
     * @describe: 创建快捷绑卡申请
     * @time:2022/11/6 11:11
     * @param $app_id String(64) 控制台 主页面应用的app_id
     * @param $member_id String(64)  用户的member_id，只能为英文、数字或者下划线的一种或多种组合
     * @param $card_id String(32)   银行卡卡号，只能是数字，长度范围为8~32个字符
     * @param $tel_no String(11) 银行卡对应的预留手机号
     * @param array $extend {
     * vip_code  String(3) 信用卡验证码，若银行卡为信用卡时必填，银行卡背面签名条末三位
     * expiration  String(4)  信用卡有效期，若银行卡为信用卡时必填
     * }
     * @return array|bool
     */
    public function CreateFastCard($member_id , $card_id , $tel_no , $extend = [])
    {
        $params = array_merge([
            'app_id' => self::$app_id ,
            'member_id' => $member_id ,
            'card_id' => $card_id ,
            'tel_no' => $tel_no
        ] , $extend);
        $req_url = self::$gateWayUrl . self::$fast_card_url . "/confirm";
        return self::handle($req_url , $params , 'post');
    }

    /**
     * @describe: 一键绑卡对象
     * @time:2022/11/6 11:22
     * @param $order_no String(64) 请求订单号，只能为英文、数字或者下划线的一种或多种组合
     * @param $member_id String(64)  用户的member_id，只能为英文、数字或者下划线的一种或多种组合
     * @param $tel_no String(11) 银行卡对应的预留手机号
     * @param $dc_type String(1) 银行卡的借贷类型；C:信用卡，D:借记卡
     * @param $bank_code String(8) 银行code， 暂支持部分银行，详见 一键绑卡支持的银行代码
     * @param $verify_front_url String(100)  前台通知地址（url为http/https路径），绑卡成功，可跳转至指定页面，服务器POST回调，URL 上请勿附带参数
     * @param $notify_url String(250)  异步通知地址，url为http/https路径，服务器POST回调，URL 上请勿附带参数
     * @return array|bool
     */
    public function cardBind($order_no , $member_id , $tel_no , $dc_type , $bank_code , $verify_front_url , $notify_url)
    {
        $params = [
            'app_id' => self::$app_id ,
            'order_no' => $order_no ,
            'member_id' => $member_id ,
            'tel_no' => $tel_no ,
            'dc_type' => $dc_type ,
            'bank_code' => $bank_code ,
            'verify_front_url' => $verify_front_url ,
            'notify_url' => $notify_url ,
            'adapay_func_code' => 'fast_card.autoBind'
        ];
        $req_url = self::$gateWayUrl . self::$fast_card_url . '/apply';
        return self::handle($req_url , $params , 'post');
    }

    /**
     * @describe: 创建快捷绑卡确认
     * @time:2022/11/6 11:16
     * @param $apply_id String(64)  Adapay生成的快捷绑卡申请id
     * @param $sms_code String(6)  短信验证码
     * @param $notify_url String(250) 异步通知地址，url为http/https路径，服务器POST回调，URL 上请勿附带参数
     * @return array|bool
     */
    public function cardBindConfirm($apply_id , $sms_code , $notify_url)
    {
        $params = [
            'apply_id' => $apply_id ,
            'sms_code' => $sms_code ,
            'notify_url' => $notify_url
        ];
        $req_url = self::$gateWayUrl . self::$fast_card_url . "/confirm";
        return self::handle($req_url , $params , 'post');
    }

    /**
     * @describe: 查询快捷卡对象列表
     * @time:2022/11/6 11:18
     * @param $member_id member_id  String(64) 用户的member_id，只能为英文、数字或者下划线的一种或多种组合
     * @param array $extend
     * {
     * token_no  String(32)   Adapay生成的快捷卡唯一标识
     * }
     * @return array|bool
     */
    public function queryCardList($member_id , $extend = [])
    {
        $params = array_merge([
            'app_id' => self::$app_id ,
            'member_id' => $member_id ,
        ] , $extend);
        ksort($params);
        $req_url = self::$gateWayUrl . self::$fast_card_url . "/list";
        return self::handle($req_url , $params , 'get');
    }



    //=============冻结账号

    /**
     * @describe: 创建冻结支付对象
     * @time:2022/11/6 13:42
     * @param $order_no String(64) 请求订单号，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
     * @param $trans_amt String(16) 冻结金额, 必须大于0，保留两位小数点，如0.10、100.05等
     * @param $member_id String(64) 冻结用户的member_id，若为商户本身时，请传入0
     * @return array|bool
     */
    public function createFreezeAccount($order_no , $trans_amt , $member_id)
    {
        $params = [
            'app_id' => self::$app_id ,
            'order_no' => $order_no ,
            'trans_amt' => $trans_amt ,
            'member_id' => $member_id
        ];
        $req_url = self::$gateWayUrl . self::$freeze_url;
        return self::handle($req_url , $params , 'post');
    }

    /**
     * @describe: 查询支付冻结对象
     * @time:2022/11/4 18:03
     * @param int $page_index 当前页码，取值范围 1~300000，默认值为 1
     * @param int $page_size 页面容量，取值范围 1~20，默认值为 10
     * @param string $created_gte 查询大于等于创建时间（13位时间戳）
     * @param string $created_lte 查询小于等于创建时间（13位时间戳）；若不为空时，created_gte 字段值不能为空且小于created_lte 时间
     * @param string $payment_id Adapay 生成的支付对象 id
     * @param string $status String(16) 查询交易状态：succeeded-成功，failed-失败，pending-处理中
     * @param string $order_no 创建支付对象时上送的请求订单号，只能为英文、数字或者下划线的一种或多种组合
     * @return array|bool
     */
    public function queryFreezeAccount($page_index = 1 , $page_size = 20 , $created_gte = '' , $created_lte = '' , $payment_id = '' , $status = 'pending' , $order_no = '')
    {
        $params = [];
        !empty($order_no) && $params['order_no'] = $order_no;
        !empty($payment_id) && $params['payment_id'] = $payment_id;

        if (!empty($created_gte) && !empty($created_lte)) {
            $params['created_gte'] = $created_gte;
            $params['created_lte'] = $created_lte;
        }
        $params = array_merge([
            'app_id' => self::$app_id ,
            'page_index' => $page_index ,
            'status' => $status ,
            'page_size' => $page_size
        ] , $params);
        ksort($params);
        $request_params = self::do_empty_data($params);
        $req_url = self::$gateWayUrl . self::$freeze_url . "/list";
        return self::handle($req_url , $params , 'get');
    }



    //=============解冻账号

    /**
     * @describe: 创建账户解冻对象
     * @time:2022/11/6 13:42
     * @param $order_no String(64) 请求订单号，只能为英文、数字或者下划线的一种或多种组合，保证在app_id下唯一
     * @param $account_freeze_id  String(16)  Adapay生成的账户冻结对象id
     * @return array|bool
     */
    public function createUnFreezeAccount($order_no , $account_freeze_id)
    {
        $params = [
            'app_id' => self::$app_id ,
            'order_no' => $order_no ,
            'account_freeze_id' => $account_freeze_id ,
        ];
        $req_url = self::$gateWayUrl . self::$unfreeze_url;
        return self::handle($req_url , $params , 'post');
    }

    /**
     * @describe: 查询账户解冻对象
     * @time:2022/11/4 18:03
     * @param int $page_index 当前页码，取值范围 1~300000，默认值为 1
     * @param int $page_size 页面容量，取值范围 1~20，默认值为 10
     * @param string $created_gte 查询大于等于创建时间（13位时间戳）
     * @param string $created_lte 查询小于等于创建时间（13位时间戳）；若不为空时，created_gte 字段值不能为空且小于created_lte 时间
     * @param string $payment_id Adapay 生成的支付对象 id
     * @param string $status String(16) 查询交易状态：succeeded-成功，failed-失败，pending-处理中
     * @param string $order_no 创建支付对象时上送的请求订单号，只能为英文、数字或者下划线的一种或多种组合
     * @return array|bool
     */
    public function queryUnFreezeAccount($page_index = 1 , $page_size = 20 , $created_gte = '' , $created_lte = '' , $payment_id = '' , $status = 'pending' , $order_no = '')
    {
        $params = [];
        !empty($order_no) && $params['order_no'] = $order_no;
        !empty($payment_id) && $params['payment_id'] = $payment_id;

        if (!empty($created_gte) && !empty($created_lte)) {
            $params['created_gte'] = $created_gte;
            $params['created_lte'] = $created_lte;
        }
        $params = array_merge([
            'app_id' => self::$app_id ,
            'page_index' => $page_index ,
            'status' => $status ,
            'page_size' => $page_size
        ] , $params);
        ksort($params);
        $req_url = self::$gateWayUrl . self::$unfreeze_url . "/list";
        return self::handle($req_url , $params , 'get');
    }



    //=============多商户模式

    /**
     * 通用请求接口 - POST - 多商户模式
     * @param array $params 请求参数
     * @param string $merchantKey 如果传了则为多商户，否则为单商户
     */
    public static function requestAdapay($params = array() , $merchantKey = "")
    {
        if (!empty($merchantKey)) {
            self::$rsaPrivateKey = $merchantKey;
            AdaTools::$rsaPrivateKey = $merchantKey;
        }

        $request_params = $params;
        $req_url = self::packageRequestUrl($request_params);
        $request_params = self::format_request_params($request_params);

        $header = self::get_request_header($req_url , $request_params , self::$header);
        $result = Request::curl_request($req_url , $request_params , $header , $is_json = true);
        return self::handleResultData($result);
    }

    /**
     * @describe: 通用请求接口 - POST - 多商户模式
     * @time:2022/11/6 14:01
     * @param array $params
     * @param string $merchantKey
     * @return array|bool
     */
    public function requestAdapayUits($params = array() , $merchantKey = "")
    {
        self::$gateWayType = "page";

        if (!empty($merchantKey)) {
            self::$rsaPrivateKey = $merchantKey;
            AdaTools::$rsaPrivateKey = $merchantKey;
        }

        $request_params = $params;
        $req_url = self::packageRequestUrl($request_params);
        $request_params = self::format_request_params($request_params);

        echo $req_url;

        $header = self::get_request_header($req_url , $request_params , self::$header);
        $result = Request::curl_request($req_url , $request_params , $header , $is_json = true);
        return self::handleResultData($result);
    }

    /**
     * @describe: 通用查询接口 - GET
     * @time:2022/11/6 14:01
     * @param array $params
     * @param string $merchantKey 传了则为多商户模式
     * @return array|bool
     */
    public static function queryAdapay($params = array() , $merchantKey = "")
    {
        if (!empty($merchantKey)) {
            self::$rsaPrivateKey = $merchantKey;
            AdaTools::$rsaPrivateKey = $merchantKey;
        }

        ksort($params);
        $request_params = $params;
        $req_url = self::packageRequestUrl($request_params);
        $request_params = self::format_request_params($request_params);
        $header = self::get_request_header($req_url , http_build_query($request_params) , self::$headerText);
        $result = Request::curl_request($req_url . "?" . http_build_query($request_params) , "" , $header , false);
        return self::handleResultData($result);
    }

    /**
     * @describe: queryAdapayUits
     * @time:2022/11/6 14:01
     * @param array $params
     * @param string $merchantKey
     * @return array|bool
     */
    public static function queryAdapayUits($params = array() , $merchantKey = "")
    {
        self::$gateWayType = "page";

        if (!empty($merchantKey)) {
            self::$rsaPrivateKey = $merchantKey;
            AdaTools::$rsaPrivateKey = $merchantKey;
        }
        ksort($params);
        $request_params = $params;
        $req_url = self::packageRequestUrl($request_params);
        $request_params = self::format_request_params($request_params);

        $header = self::get_request_header($req_url , http_build_query($request_params) , self::$headerText);
        $result = Request::curl_request($req_url . "?" . http_build_query($request_params) , "" , $header , false);
        return self::handleResultData($result);
    }


}