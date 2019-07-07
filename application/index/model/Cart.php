<?php
namespace app\index\model;
use think\Model;
class Cart extends Model{
    public function addCart($goods_id,$goods_count,$goods_attr_ids){
        // 判断用户是否登录
        $user_info=session('user_info');
        if($user_info){
            // 如果登录 操作数据库 先查询是否存在相同的商品 累加数量否则写入
            $map=[
                'goods_id'=>$goods_id,
                'user_id'=>$user_info['id'],
                'goods_attr_ids'=>$goods_attr_ids
            ];
            if($this->where($map)->find()){
                // setInc设置指定字段的值增加
                $this->where($map)->setInc('goods_count',$goods_count);
            }else{
                // 写入数据
                $map['goods_count']=$goods_count;
                $this->save($map);
            }
        }else{
            // 未登录 操作cookie
            $cart_list=cookie('cart_list')?cookie('cart_list'):[];
            // 组装下标名称
            $key=$goods_id.'-'.$goods_attr_ids;
            // 判断是否存在
            if(array_key_exists($key,$cart_list)){
                $cart_list[$key]+=$goods_count;
            }else{
                $cart_list[$key]=$goods_count;
            }
            // 将数据保存到cookie中
            cookie('cart_list',$cart_list,3600*24*6);
        }
    }

    public function getList(){
        $user_info=session('user_info');
        if($user_info){
            $cart_list=db('cart')->where('user_id',$user_info['id'])->select();

        }else{
            $cart=cookie('cart_list')?cookie('cart_list'):[];
            $cart_list=[];
            foreach($cart as $key => $value){
                $temp=explode('-',$key);
                $cart_list[]=[
                    'goods_id'=>$temp[0],
                    'goods_count'=>$value,
                    'goods_attr_ids'=>$temp[1]
                ];
            }
            
        }
        foreach($cart_list as $key => $value){
            // 获取商品基本信息
            $cart_list[$key]['goods']=db('goods')->where('id',$value['goods_id'])->find();
            // 获取商品属性信息
            $sql="SELECT a.attr_value,b.attr_name FROM shop_goods_attr a LEFT JOIN shop_attribute b on a.attr_id=b.id WHERE a.id IN({$value['goods_attr_ids']})";
            $cart_list[$key]['attrs']=db('goods_attr')->query($sql);
        }
        return $cart_list;
    }

    public function getTotal($data){
        $total=0;
        foreach($data as $key => $value){
            $total+=$value['goods_count']*$value['goods']['shop_price'];
        }
        return $total;
    }

    public function remove($goods_id,$goods_attr_ids){
        $user_info=session('user_info');
        if($user_info){
            $map=[
                'goods_id'=>$goods_id,
                'goods_attr_ids'=>$goods_attr_ids,
                'user_id'=>$user_info['id']
            ];
            $this->where($map)->delete();
        }else{
            $cart_list=cookie('cart_list')?cookie('cart_list'):[];
            $key=$goods_id.'-'.$goods_attr_ids;
            unset($cart_list[$key]);
            cookie('cart_list',$cart_list,3600*24*6);
        }
    }

    public function changeNumber($goods_id,$goods_count,$goods_attr_ids){
        $user_info=session('user_info');
        if($user_info){
            $map=[
                'goods_id'=>$goods_id,
                'goods_attr_ids'=>$goods_attr_ids,
                'user_id'=>$user_info['id']
            ];
            $this->where($map)->setField('goods_count',$goods_count);
        }else{
            $cart_list=cookie('cart_list')?cookie('cart_list'):[];
            $key=$goods_id.'-'.$goods_attr_ids;
            $cart_list[$key]=$goods_count;
            cookie('cart_list',$cart_list,3600*24*6);
        }
    }

    public function cookie2db(){
        $user_info=session('user_info');
        if(!$user_info){
            return false;
        }
        $cart_list=cookie('cart_list')?cookie('cart_list'):[];
        foreach($cart_list as $key=>$value){
            $temp=explode('-',$key);
            $map=[
                'goods_id'=>$temp[0],
                'goods_attr_ids'=>$temp[1],
                'user_id'=>$user_info['id']
            ];
            if($this->where($map)->find()){
                $this->where($map)->setInc('goods_count',$value);
            }else{
                $map['goods_count']=$value;
                $this->save($map);
            }
        }
        cookie('cart_list',null);
    }
}