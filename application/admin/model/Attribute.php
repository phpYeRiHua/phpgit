<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Attribute extends Model{
    // 属性的添加
    public function addAttr($data){
        // 判断是否存在默认值
        if($data['attr_input_type']==2 && !$data['attr_values']){
            $this->error='默认值需要设置';
            return false;
        }
        return $this->isUpdate(false)->allowField(true)->save($data);
    }

    // 获取属性
    public function getList(){
        return Db::name('attribute')->alias('a')->join('shop_type b','a.type_id=b.id','left')->field('a.*,b.type_name')->paginate(2);
    }

    // 删除属性
    public function remove($attr_id){
        return $this->where('id',$attr_id)->delete();
    }


    // 修改属性
    public function editAttr($data){
        // 判断select选择时是否存在默认值
        if($data['attr_input_type']==2 && !$data['attr_values']){
            $this->error='默认值需要设置';
            return false;
        }
        return $this->isUpdate(true)->allowField(true)->save($data);
    }

    // 根据type_id获取属性信息
    public function getAttrById($type_id){
        $data = $this->all(['type_id'=>$type_id]);
        $list=[];
        foreach($data as $value){
            $value=$value->toArray();
            if($value['attr_input_type']==2){
                // select选择列表
                $value['attr_values']=explode(',',$value['attr_values']);
            }
            $list[]=$value;
        }
        return $list;
    }
}