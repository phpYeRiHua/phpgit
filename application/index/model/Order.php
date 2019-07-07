<?php
namespace app\index\model;
use think\Model;
class Order extends Model{
    // 下单的方法
    public function order($order){
        // 订单主表写入内容
        // 提取用户id
        $order['user_id']=session('user_info')['id'];
        // 计算订单号
        $order['order_sn']=date('YmdHis').rand(100000,999999);
        // 获取购物车数据
        $cart_list=model('Cart')->getList();
        // 计算总金额
        $order['total']=model('Cart')->getTotal($cart_list);
        $this->save($order);
        $order['id']=$this->getLastInsId();
        // 订单详情表写入内容
        $order_detail=[];
        foreach($cart_list as $key => $value){
            $order_detail=[
                'order_id'=>$order['id'],
                'goods_id'=>$value['goods_id'],
                'goods_count'=>$value['goods_count'],
                'goods_attr_ids'=>$value['goods_attr_ids']
            ];
        }
        // 批量写入数据
        db('order_detail')->insertAll($order_detail);
        // 清空购物车
        return $order;
    }
}