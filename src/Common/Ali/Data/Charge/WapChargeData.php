<?php
/**
 * @author: helei
 * @createTime: 2016-07-22 17:02
 * @description:
 */

namespace Payment\Common\Ali\Data\Charge;

use Payment\Common\PayException;
use Payment\Utils\ArrayUtil;

class WapChargeData extends ChargeBaseData
{
    protected function checkDataParam()
    {
        parent::checkDataParam(); // TODO: Change the autogenerated stub

        // 手机网站支付接口  需要检查show_url 参数，必须上传
        $showUrl = $this->show_url;
        $version = $this->version;
        if (empty($version) && empty($showUrl)) {
            throw new PayException('使用手机网站支付接口时，必须提供show_url参数');
        }
    }

    protected function alipay1_0Data($timeExpire = '')
    {
        $signData = [
            // 基本参数
            'service'   => 'alipay.wap.create.direct.pay.by.user',
            'partner'   => trim($this->partner),
            '_input_charset'   => trim($this->inputCharset),
            'sign_type'   => trim($this->signType),
            'notify_url'    => trim($this->notifyUrl),
            'return_url'    => trim($this->returnUrl),

            // 业务参数
            'out_trade_no'  => trim($this->order_no),
            'subject'   => trim($this->subject),
            'total_fee' => trim($this->amount),
            'seller_id' => trim($this->partner),
            'payment_type'  => 1,
            'show_url'  => trim($this->show_url),
            'body'  => trim($this->body),
            'goods_type'    => 1, //默认为实物类型
            //'app_pay'   => 'Y', // 是否使用支付宝客户端支付  如果为Y，需要处理alipays协议
        ];

        if (! empty($timeExpire)) {
            $signData['it_b_pay'] = '"' . trim($this->timeExpire) . 'm"';// 超时时间 统一使用分钟计算
        }

        return $signData;
    }

    protected function alipay2_0Data($timeExpire = '')
    {
        $signData = [
            // 公共参数
            'app_id'        => $this->appId,
            'method'        => $this->method,
            'format'        => $this->format,
            'return_url'    => $this->returnUrl,
            'charset'       => $this->inputCharset,
            'sign_type'     => $this->signType,
            'timestamp'     => $this->timestamp,
            'version'       => $this->version,
            'notify_url'    => $this->notifyUrl,

            // 业务参数  新版支付宝，将所有业务参数设置到改字段中了，  这样不错
            'biz_content'   => $this->getBizContent($timeExpire),
        ];

        return $signData;
    }

    /**
     * 业务请求参数的集合，最大长度不限，除公共参数外所有请求参数都必须放在这个参数中传递
     *
     * @param string $timeExpire 订单过期时间，  分钟
     *
     * @return string
     */
    private function getBizContent($timeExpire = '')
    {
        $content = [
            'body'          => strval($this->body),
            'subject'       => strval($this->subject),
            'out_trade_no'  => strval($this->order_no),
            'total_amount'  => strval($this->amount),

            // 销售产品码，商家和支付宝签约的产品码，为固定值QUICK_MSECURITY_PAY
            'product_code'  => 'QUICK_WAP_PAY',
            'goods_type'    => strval(1),
        ];

        if (! empty($timeExpire)) {
            $content['timeout_express'] = $this->timeExpire . 'm';// 超时时间 统一使用分钟计算
        }

        $partner = $this->partner;
        if (! empty($partner)) {
            $content['seller_id'] = strval($partner);
        }

        return json_encode($content, JSON_UNESCAPED_UNICODE);
    }
}
