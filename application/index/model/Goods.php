<?php
namespace app\index\model;
use think\Model;

class Goods extends Model{
    public function getRecGoods($field){
        return $this->where($field,1)->limit(5)->select();
    }

    public function getGoodsInfo($goods_id){
        $query=db('goods');
        $goods=$query->find($goods_id);
        // halt($goods_id);
        $goods['imgs']=db('goods_img')->where('goods_id',$goods_id)->select();
        $attr=db('goods_attr')->alias('a')->field('a.*,b.attr_name,b.attr_type')->where('a.goods_id',$goods_id)->join('shop_attribute b','a.attr_id=b.id','left')->select();
        foreach($attr as $key=>$value){
            if($value['attr_type']==1){
                $goods['onlyone'][]=$value;
            }else{
                $goods['single'][$value['attr_id']][]=$value;
            }
        }
        return $goods;
    }   
}