<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 17:56
 * @description: 微信 app 支付接口
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Charge\Weixin;

use Payment\Common\Weixin\Data\Charge\AppChargeData;
use Payment\Common\Weixin\WxBaseStrategy;

class WxAppCharge extends WxBaseStrategy
{

    protected function getBuildDataClass()
    {
        return AppChargeData::class;
    }
}