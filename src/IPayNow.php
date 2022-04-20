<?php

namespace Choijx\NowIpaySdk;

use Choijx\NowIpaySdk\Exceptions\BusinessException;
use Choijx\NowIpaySdk\Exceptions\GatewayException;
use Choijx\NowIpaySdk\Exceptions\InvalidConfigException;
use Choijx\NowIpaySdk\Exceptions\InvalidSignException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class IPayNow
{
    const TRADE_URL = 'https://pay.ipaynow.cn';
    const TRADE_URL_TEST = 'https://tls-pay.ipaynow.cn';
    const TRADE_TIME_OUT = '3600';
    const TRADE_FUNCODE = 'WP001';
    const QUERY_FUNCODE = 'MQ002';
    const NOTIFY_FUNCODE = 'N001';
    const FRONT_NOTIFY_FUNCODE = 'N002';
    const TRADE_TYPE = '01';
    const TRADE_CURRENCY_TYPE = '156';
    const TRADE_CHARSET = 'UTF-8';
    // const TRADE_DEVICE_TYPE_H5 = '0601';
    // const TRADE_DEVICE_TYPE_CODE = '08';
    const TRADE_SIGN_TYPE = 'MD5';
    const TRADE_QSTRING_EQUAL = '=';
    const TRADE_QSTRING_SPLIT = '&';
    const TRADE_FUNCODE_KEY = 'funcode';
    const TRADE_SIGNATURE_KEY = 'mhtSignature';
    // const TRADE_OUTPUT_TYPE = '2';
    const SIGNATURE_KEY = 'signature';
    const VERSION = '1.0.0';
    const ALI_PAY_CHANNEL = 12;  // 用户所选渠道类型 支付宝：12
    const WECHAT_PAY_CHANNEL = 13; // 用户所选渠道类型 微信：13

    /**
     * 应用ID.
     *
     * @var string
     */
    protected $appid;

    /**
     * 应用Key.
     *
     * @var string
     */
    protected $key;

    /**
     * 支付渠道.
     *
     * @var string
     */
    protected $channel;

    /**
     * 异步回调地址
     *
     * @var string
     */
    protected $notifyUrl;

    /**
     * 同步回调地址
     *
     * @var string
     */
    protected $returnUrl;

    /**
     * 设备类型 08扫码支付 0601（手机网页H5）
     *
     * @var string
     */
    protected $deviceType;

    /**
     * 输出格式
     *
     * @var string
     */
    protected $outputType;

    /**
     * NowSdk constructor.
     *
     * @param $appid
     * @param $key
     * @param $channel
     * @param $notifyUrl
     * @param $returnUrl
     * @param $deviceType
     * @param $outputType
     */
    protected function __construct($appid, $key, $channel, $notifyUrl, $returnUrl, $deviceType, $outputType)
    {
        $this->appid = $appid;
        $this->key = $key;
        $this->channel = $channel;
        $this->notifyUrl = $notifyUrl;
        $this->returnUrl = $returnUrl;
        $this->deviceType = $deviceType;
        $this->outputType = $outputType;
    }

    /**
     * 获取现在支付微信SDK.
     *
     * @param $config
     *
     * @throws InvalidConfigException
     *
     * @return IPayNow
     */
    public static function wechat($config)
    {
        try {
            return new static(
                $config['appid'],
                $config['key'],
                static::WECHAT_PAY_CHANNEL,
                isset($config['notify_url']) ? $config['notify_url'] : '',
                isset($config['return_url']) ? $config['return_url'] : '',
                isset($config['deviceType']) ? $config['deviceType'] : '0601',
                isset($config['outputType']) ? $config['outputType'] : '2'
            );
        } catch (\Exception $e) {
            throw new InvalidConfigException($e->getMessage());
        }
    }

    /**
     * 获取现在支付支付宝SDK.
     *
     * @param $config
     *
     * @throws InvalidConfigException
     *
     * @return IPayNow
     */
    public static function ali($config)
    {
        try {
            return new static(
                $config['appid'],
                $config['key'],
                static::ALI_PAY_CHANNEL,
                isset($config['notify_url']) ? $config['notify_url'] : '',
                isset($config['return_url']) ? $config['return_url'] : '',
                isset($config['deviceType']) ? $config['deviceType'] : '0601',
                isset($config['outputType']) ? $config['outputType'] : '2'
            );
        } catch (\Exception $e) {
            throw new InvalidConfigException($e->getMessage());
        }
    }

    /**
     * 预下单.
     *
     * @see https://mch.ipaynow.cn/h5Pay
     *
     * @param array $order
     *
     * @throws \Choijx\NowIpaySdk\Exceptions\GatewayException
     *
     * @return string
     */
    public function pre(array $order)
    {
        $params = [
            'appId'             => $this->appid,
            'deviceType'        => $this->deviceType, // 设备类型
            'frontNotifyUrl'    => $this->returnUrl, // 前端通知URL
            'funcode'           => static::TRADE_FUNCODE, // 功能码
            'mhtCharset'        => static::TRADE_CHARSET, // 商户字符编码
            'mhtCurrencyType'   => static::TRADE_CURRENCY_TYPE, // 商户订单币种类型
            'mhtOrderAmt'       => (int) bcmul($order['money'], 100, 0), // 商户订单交易金额
            'mhtOrderDetail'    => isset($order['detail']) ? $order['detail'] : '', // 商户订单详情
            'mhtOrderName'      => (string) $order['money'], // 商户商品名称
            'mhtOrderNo'        => $order['no'], // 商户订单号
            'mhtOrderStartTime' => date('YmdHis'), // 商户订单开始时间
            'mhtOrderTimeOut'   => static::TRADE_TIME_OUT, // 商户订单超时时间
            'mhtOrderType'      => static::TRADE_TYPE, // 商户交易类型
            'mhtReserved'       => isset($order['attach']) ? $order['attach'] : '', // 商户保留域
            'mhtSignType'       => static::TRADE_SIGN_TYPE, // 商户签名方法
            'notifyUrl'         => $this->notifyUrl, // 商户后台通知URL
            'outputType'        => $this->outputType, // 输出格式
            'payChannelType'    => $this->channel, // 用户所选渠道类型: 12-支付宝 13-微信
            'version'           => static::VERSION,
            'consumerCreateIp'  => isset($order['ip']) ? $order['ip'] : '', // 消费者下单ip: 微信时必填
        ];

        $reqString = $this->getRequestString($params);

        try {
            $res = (new Client())->post(static::TRADE_URL, [
                'body' => $reqString,
            ]);
        } catch (GuzzleException $e) {
            throw new GatewayException($e->getMessage());
        }

        $resContent = $res->getBody()->getContents();
        $resArray = queryStringToArray($resContent, false);
        $resCode = isset($resArray['responseCode']) ? $resArray['responseCode'] : '';
        $resMsg = isset($resArray['responseMsg']) ? $resArray['responseMsg'] : '';

        if ($resCode !== 'A001') {
            throw new BusinessException(urldecode($resMsg));
        }
        $url = isset($resArray['tn']) ? $resArray['tn'] : '';

        return urldecode($url);
    }

    /**
     * 验证回调通知.
     *
     * @param $params
     *
     * @throws InvalidSignException
     */
    public function verify($params = null)
    {
        if (!$params) {
            $inputString = file_get_contents('php://input');
            $params = queryStringToArray($inputString);
        }

        if (!isset($params['signature'])) {
            throw new InvalidSignException('缺少签名');
        }

        if ($params['signature'] != $this->getSignature($params)) {
            throw new InvalidSignException('签名错误');
        }
    }

    /**
     * 获取下单请求的字符串.
     *
     * @param array $params
     *
     * @return string
     */
    protected function getRequestString(array $params)
    {
        $params = $this->filterParams($params);
        $signature = $this->getSignature($params);
        $reqString = '';
        foreach ($params as $k => $v) {
            if ($v != '') {
                $reqString .= $k.static::TRADE_QSTRING_EQUAL.urlencode($v).static::TRADE_QSTRING_SPLIT;
            }
        }
        $reqString .= static::TRADE_SIGNATURE_KEY.static::TRADE_QSTRING_EQUAL.$signature;

        return $reqString;
    }

    /**
     * 计算签名.
     *
     * @param array $params 参数
     *
     * @return string
     */
    protected function getSignature(array $params)
    {
        $signature = '';
        ksort($params);
        foreach ($params as $key => $value) {
            if ($value == '' || $key == 'signature') {
                continue;
            }
            $signature .= "{$key}={$value}&";
        }
        $signature = md5($signature.md5($this->key));

        return $signature;
    }

    /**
     * 过滤参数.
     *
     * @param array $params
     *
     * @return array
     */
    protected function filterParams(array $params)
    {
        $result = [];
        $funcode = $params[static::TRADE_FUNCODE_KEY];
        foreach ($params as $key => $value) {
            if (($funcode == static::TRADE_FUNCODE)
                && !($key == static::TRADE_SIGNATURE_KEY || $key == static::SIGNATURE_KEY)) {
                $result[$key] = $value;
                continue;
            }
            if (($funcode == static::NOTIFY_FUNCODE || $funcode == static::FRONT_NOTIFY_FUNCODE)
                && !($key == static::SIGNATURE_KEY)) {
                $result[$key] = $value;
                continue;
            }
            if (($funcode == static::QUERY_FUNCODE) &&
                !($key == static::TRADE_SIGNATURE_KEY || $key == static::SIGNATURE_KEY)) {
                $result[$key] = $value;
                continue;
            }
        }

        return $result;
    }
}
