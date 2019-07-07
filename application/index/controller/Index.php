<?php
namespace app\index\controller;


class Index extends Common
{
    public function index()
    {
        $this->assign('is_show',1);
        // 获取热卖商品
        $goods_model=model('Goods');
        $goods['hot']=$goods_model->getRecGoods('is_hot');
        $goods['rec']=$goods_model->getRecGoods('is_rec');
        $goods['new']=$goods_model->getRecGoods('is_new');
        $this->assign('goods',$goods);
        return $this->fetch();
    }
}
