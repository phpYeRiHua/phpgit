<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Category extends Model{
    // 获取格式化之后的分类数据
    public function getCateTree($id=0,$is_clear=false){
        // 查询所有的分类数据
        $category=Db::name('category')->select();
        // 调用函数对数据进行格式化
        return get_tree($category,$id,0,$is_clear);
    }


    // 删除分类
    public function remove($cate_id){
        // 保存最终要删除的数据的id
        $where=[$cate_id];
        // 查找分类下的所有子分类
        $son=$this->getCateTree($cate_id);
        if($son){
            // 存在子分类
            foreach($son as $value){
                // 向$where变量中增加一个元素记录要删除数据的id
                $where[]=$value['id'];
            }
        }
        // 删除数据
        $this->destroy(($where));
    }



    // 修改分类数据
    public function saveData($data){
        // 修改数据不能设置自己为自己的上级父类
        if($data['id']==$data['parent_id']){
            $this->error='不能设置自己为自己的上级分类';
            return false;
        }
        // 判断当前修改的分类的夫分类不能是子分类
        $son=$this->getCateTree($data['id']);
        foreach($son as $v){
            if($data['parent_id']==$v['id']){
                $this->error='不能设置子分类为自己的父类';
                return false;
            }
        }
        return $this->allowField(true)->isUpdate(true)->save($data);
    }
}