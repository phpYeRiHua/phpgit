<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Rule extends Model{
    public function getRules($id=0,$is_clear=false)
	{
		// 查询所有的分类数据
		$category = Db::name('rule')->select();
		// 调用函数对数据进行格式化
		return get_tree($category,$id,0,$is_clear);
	}

	public function remove($role_id){
		return $this->where('id',$role_id)->delete();
	}
}