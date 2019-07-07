<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class GoodsAttr extends Model{
    public function addAll($goods_id,$attr_ids,$attr_values){
        $list=[];//最终要写入的数据
        $temp=[];//去重的临时变量
        // 组装数据格式
        foreach($attr_ids as $key=>$value){
            $string =$value.'-'.$attr_values[$key];
            if(in_array($string,$temp)){
                // 说明数据已经重复
                continue;
            }
            // 说明数据没有重复
            $temp[]=$string;
            // attr_ids变量中一个元素对应需要一条数据
            $list[]=[
                'goods_id'=>$goods_id,
                'attr_id'=>$value,
                'attr_value'=>$attr_values[$key]
            ];
        }
        // 批量写入
        $this->saveAll($list);
    }
    // 根据商品ID获取属性名称及值
    public function getAttrByGoodsId($goods_id){
        $sql='select a.*,b.attr_name,b.attr_type,b.attr_input_type,b.attr_values from shop_goods_attr a left join shop_attribute b on a.attr_id=b.id where a.goods_id=?';
        $list=Db::query($sql,[$goods_id]);
        $attrs=[];//保存最终结果
        // 数据格式化
        foreach($list as $key=>$value){
            if($value['attr_input_type']==2){
                $value['attr_values']=explode(',',$value['attr_values']);
            }
            $attrs[$value['attr_id']][]=$value;
        }
        return $attrs;
    }
}