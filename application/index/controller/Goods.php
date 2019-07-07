<?php
namespace app\index\controller;
class Goods extends Common{
    public function detail(){
        $goods_model=model('Goods');
        $goods=$goods_model->getGoodsInfo(input('id'));
        // dump($goods);
        $this->assign('goods',$goods);
        return $this->fetch();
    }
}