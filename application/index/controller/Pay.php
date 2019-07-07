<?php
namespace app\index\controller;

class Pay extends Common
{
    public function check()
    {
        // 检查用户登录
        $this->checkLogin();
        // 获取购物车数据列表
        $data = model('Cart')->getList();
        $this->assign('data', $data);
        // 计算总金额
        $total = model('Cart')->getTotal($data);
        $this->assign('total', $total);
        return $this->fetch();
    }

    public function pay()
    {
        // 检查用户登录
        $this->checkLogin();
        // 获取模型对象
        $model = model('Order');
        $res = $model->order(input());
        if ($res === false) {
            $this->error($model->getError());
        }
        $this->alipay($res);
    }


    public function alipay($order)
    {
        require_once '../extend/alipay/config.php';
        require_once '../extend/alipay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';
        require_once '../extend/alipay/pagepay/service/AlipayTradeService.php';
        $out_trade_no = $order['order_sn'];
        $subject = '商品支付';
        $total_amount = $order['total'];
        $body = 'desc';
        $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setOutTradeNo($out_trade_no);

        $aop = new \AlipayTradeService($config);

        /**
         * pagePay 电脑网站支付请求
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @param $return_url 同步跳转地址，公网可以访问
         * @param $notify_url 异步通知地址，公网可以访问
         * @return $response 支付宝返回的信息
         */
        $response = $aop->pagePay($payRequestBuilder, $config['return_url'], $config['notify_url']);

        //输出表单
        var_dump($response);
    }

    public function returnurl()
    {
        require_once("../extend/alipay/config.php");
        require_once '../extend/alipay/pagepay/service/AlipayTradeService.php';


        $arr = $_GET;
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($arr);
        if (!$result) {
            return '验证失败';
        }
        //商户订单号
        $out_trade_no = htmlspecialchars($_GET['out_trade_no']);

        //支付宝交易号
        $trade_no = htmlspecialchars($_GET['trade_no']);
        $order_info = db('order')->where('order_sn', $out_trade_no)->find();
        if (!$order_info) {
            return 'fail';
        }
        if ($order_info['status'] == 1) {
            return '已支付';
        }
        db('order')->where('order_sn', $out_trade_no)->setField('status', 1);
    }
}
