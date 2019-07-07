<?php
namespace app\index\controller;

class Cart extends Common{
    public function addCart(){
        $goods_id=input('goods_id/d');
        $goods_count=input('goods_count/d');
        $goods_attr_ids=input('attr_id/a',[]);

        $goods_attr_ids=implode(',',$goods_attr_ids);
        model('Cart')->addCart($goods_id,$goods_count,$goods_attr_ids);
        $this->success('ok','index');
    }

    public function index(){
        $data=model('Cart')->getList();
        $this->assign('data',$data);
        $total=model('Cart')->getTotal($data);
        $this->assign('total',$total);
        return $this->fetch();
    }

    public function remove(){
        $goods_id=input('goods_id/d');
        $goods_attr_ids=input('goods_attr_ids','');
        model('Cart')->remove($goods_id,$goods_attr_ids);
        $this->success('ok','index');
    }

    public function changeNumber(){
        $goods_id=input('goods_id/d');
        $goods_count=input('goods_count/d');
        $goods_attr_ids=input('goods_attr_ids','');
        model('Cart')->changeNumber($goods_id,$goods_count,$goods_attr_ids);
        return json(['code'=>1]);
    }

}