# Choijx IPayNow Sdk

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

## Requirement
- PHP 5.6 +

## Installation
```bash
composer require choijx/ipaynow-sdk:*
```

## Usage
```php
use Choijx\IPayNowSDK\IPayNow;
use Choijx\IPayNowSDK\Exceptions\Exceptions;
use Choijx\IPayNowSDK\Exceptions\InvalidSignException;

$config = [
    'appid' => '123456789012345',                      // 商户应用唯一标识
    'key' => 'abcdefghijklmnopqrstuvwxyz123456789012', // 密钥
    'notify_url' => 'http://cloudycity.me/notify.php', // 异步回调地址 (拉起支付时必选，只用于验证回调时可选)
    'return_url' => 'http://cloudycity.me/return.php', // 同步回调地址 (拉起支付时必选，只用于验证回调时可选)
];

$order = [
    'no' => '1234560', // 订单号
    'money' => 600,    // 订单金额(单位: 分)
    'attach' => '',    // 可选，商户保留域
    'detail' => '',    // 可选，订单详情
    'ip' => '0.0.0.0', // 可选，支付端IP
];

// 拉起支付
try {
    $url = IPayNow::wechat($config)->pre($order);
    $url = IPayNow::ali($config)->pre($order);
} catch (Exceptions $e) {
     //
}

// 校验回调
try {
    PayNow::wechat($config)->verify();
    PayNow::ali($config)->verify();
} catch (InvalidSignException $e) {
    //
}
```

## License

MIT

[ico-version]: https://img.shields.io/packagist/v/cloudycity/ipaynow-sdk.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/cloudycity/ipaynow-sdk/master.svg?style=flat-square
[ico-code-coverage]: https://img.shields.io/scrutinizer/coverage/g/cloudycity/ipaynow-sdk.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/352580171/shield?branch=master
[ico-code-quality]: https://img.shields.io/scrutinizer/g/cloudycity/ipaynow-sdk.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/cloudycity/ipaynow-sdk.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/cloudycity/ipaynow-sdk
[link-travis]: https://travis-ci.org/cloudycity/ipaynow-sdk
[link-code-coverage]: https://scrutinizer-ci.com/g/cloudycity/ipaynow-sdk/code-structure
[link-styleci]: https://styleci.io/repos/352580171
[link-code-quality]: https://scrutinizer-ci.com/g/cloudycity/ipaynow-sdk
[link-downloads]: https://packagist.org/cloudycity/ipaynow-sdk
[link-author]: https://github.com/cloudycity
