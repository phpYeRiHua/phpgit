<?php
namespace app\admin\model;

use think\Model;
use think\Db;
use think\Exception;

class Goods extends Model
{
    // 状态切换
    public function changeStatus($goods_id, $field)
    {
        // 查询当前商品的信息
        $goods_info = $this->get($goods_id);
        if (!$goods_info) {
            $this->error = '参数错误';
            return false;
        }
        // 计算最后修改的内容
        $status = $goods_info->getAttr($field) ? 0 : 1;
        // 修改内容
        $goods_info->$field = $status;
        $goods_info->isUpdate(true)->save();
        return $status;
    }
    // 商品添加入库
    public function addGoods($post_data)
    {
        // 增加添加时间元素
        $post_data['addtime'] = time();
        // 开启事务
        Db::startTrans();
        try{
            $this->allowField(true)->isUpdate(false)->save($post_data);
            // 获取商品id
            $goods_id=$this->getLastInsId();
            // 调用模型方法完成入库
            model('GoodsAttr')->addAll($goods_id,input('attr_ids/a'),input('attr_values/a'));
            // 实现相册的入库
            model('GoodsImg')->addAll($goods_id);
            Db::commit();
        }catch(Exception $e){
            Db::rollback();
            $this->error='写入错误';
            return false;
        }       
    }
    // 商品编辑
    public function editGoods($post_data)
    {
        // 处理推荐状态 由于checkbox被取消不会提交内容
        $post_data['is_rec'] = isset($post_data['is_rec']) ? 1 : 0;
        $post_data['is_new'] = isset($post_data['is_new']) ? 1 : 0;
        $post_data['is_hot'] = isset($post_data['is_hot']) ? 1 : 0;
        // 不能修改货号
        unset($post_data['goods_sn']);
        $goods_id=$post_data['id'];
        model('GoodsAttr')->addAll($goods_id,input('attr_ids/a'),input('attr_values/a'));
        // // 实现相册的入库
        // model('GoodsImg')->addAll($goods_id);
        return $this->allowField(true)->isUpdate(true)->save($post_data);
    }
    // 商品列表显示
    public function goodslist($is_del = 0)
    {
        // 保存查询商品的条件
        $map = ['is_del' => $is_del];
        // 关键字搜索
        $keyword = input('keyword');
        if ($keyword) {
            $map['goods_name'] = ['like', '%' . $keyword . '%'];
        }

        // 推荐状态的条件搜索
        $intro_type = input('intro_type');
        if ($intro_type) {
            $map[$intro_type] = 1;
        }


        // 根据分类id搜索
        $cate_id = input('cate_id');
        if ($cate_id) {
            // 获取当前分类下的所有子分类
            $tree = model('Category')->getCateTree($cate_id, true);
            // 提取说有的子分类
            $son = [];
            $son[] = $cate_id;
            foreach ($tree as $k => $v) {
                $son[] = $v['id'];
            }
            $map['cate_id'] = ['in', $son];
        }

        $data = Db::name('goods')->where($map)->paginate(2, false, ['page' => input('page', 1), 'query' => request()->param()]);
        return $data;
    }
    // 商品彻底删除
    public function del($goods_id)
    {
        // 查询商品信息
        $goods_info = $this->get($goods_id);
        // 删除图片
        @unlink($goods_info->getAttr('goods_img'));
        @unlink($goods_info->getAttr('goods_thumb'));
        // 删除数据
        $goods_info->delete();
    }
    
}
