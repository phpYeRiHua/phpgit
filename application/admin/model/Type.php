<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Type extends Model{

    // 获取类型列表
    public function getList(){
        return $this->all();
    }

    // 编辑类型的方法
    public function editType($data){
        return $this->isUpdate(true)->allowField(true)->save($data);
    }
}