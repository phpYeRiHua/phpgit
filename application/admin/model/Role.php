<?php
namespace app\admin\model;
use think\Model;

class Role extends Model{
    public function getList(){
        return $this->all();
    }

    public function editType($data){
        return $this->isUpdate(true)->allowField(true)->save($data);
    }
}