<?php
namespace app\admin\controller;

use think\Request;
use think\Model;
use think\Db;

class Goods extends Common
{
    // Ajax获取类型下的属性
    public function showAttr(){
        // 接收类型参数
        $type_id=input('type_id');
        // 获取属性
        $attrs=model('Attribute')->getAttrById($type_id);
        $this->assign('attrs',$attrs);
        return $this->fetch();
    }
    // 商品编辑
    public function edit(Request $request)
    {
        $model = model('Goods');
        if ($request->isGet()) {
            // 查询商品原有内容
            $goods_info = $model->get(input('id'));
            // 查询所有分类数据
            $category = model('Category')->getCateTree();
            // 获取所有类型
            $type=model('Type')->getList();
            // 获取商品下的所以属性
            $attrs=model('GoodsAttr')->getAttrByGoodsId(input('id/d'));
            return $this->fetch('edit', ['goods_info' => $goods_info, 'category' => $category,'type'=>$type,'attrs'=>$attrs]);
        }
        // 接收参数
        $post_data = input();
        // dump(input());
        $res = $this->validate($post_data, 'Goods');
        if ($res !== true) {
            $this->error($res);
        }
        // 文件上传图片处理
        $this->upload($post_data, false);
        // 调用自定义的模型方法完成入库
        $goods_model = model('Goods');
        $res = $goods_model->editGoods($post_data);
        if ($res === false) {
            $this->error($goods_model->getError());
        }
        $this->success('ok','index');
    }
    // ajax状态切换
    public function changeStatus()
    {
        $model = model('Goods');
        $res = $model->changeStatus(input('goods_id'), input('field'));
        if ($res === false) {
            echo json_encode(['code' => 0, 'msg' => $model->getError()]);
            exit();
        }
        echo json_encode(['code' => 1, 'msg' => 'ok', 'status' => $res]);
    }
    // 商品列表
    public function index(Request $request)
    {

        $category = model('Category')->getCateTree();
        // $goods=model('Goods')->select();
        // // dump($goods);
        $this->assign(['category' => $category]);
        // return $this->fetch();
        $cate_id = input('cate_id');
        $this->assign('cate_id', $cate_id);
        $intro_type = input('intro_type');
        $this->assign('intro_type', $intro_type);
        $keyword = input('keyword');
        $this->assign('keyword', $keyword);
        // 调用自定义的模型方法获取所有的数据
        $data = model('Goods')->goodslist();
        $this->assign('data', $data);
        return $this->fetch();
    }
    // 商品添加
    public function add(Request $request)
    {
        if ($request->isGet()) {
            // 获取所有类型
            $type=model('Type')->getList();
            $this->assign('type',$type);
            // 查询分类数据
            $category = model('Category')->getCateTree();
            $this->assign('category', $category);
            return $this->fetch();
        }

        // 处理表单
        // 接收参数
        $post_data = input();
        // 验证器检查
        $res = $this->validate($post_data, 'Goods');
        if ($res !== true) {
            $this->error($res);
        }
        // 文件上传图片处理
        $this->upload($post_data);
        // 检查货号
        $this->checkGoodsSn($post_data);
        // 调用自定义的模型方法完成入库
        $goods_model = model('Goods');
        $res = $goods_model->addGoods($post_data);
        if ($res === false) {
            $this->error($goods_model->getError());
        }
        $this->success('ok', 'index');
    }
    // 商品的伪删除
    public function remove()
    {
        $goods_id = input('id/d');
        Db::name('goods')->where('id', $goods_id)->setField('is_del', 1);
        $this->success('ok', 'index');
    }
    // 商品回收站列表
    public function recyle()
    {
        // 获取所有的分类数据
        $category = model('Category')->getCateTree();
        $this->assign('category', $category);
        // 调用自定义的模型方法获取所有数据
        $data = model('Goods')->goodsList(1);
        $this->assign('data', $data);
        return $this->fetch();
    }
    // 商品的还原
    public function rollback()
    {
        $goods_id = input('id/d');
        Db::name('goods')->where('id', $goods_id)->setField('is_del', 0);
        $this->success('ok', 'recyle');
    }
    // 彻底删除商品
    public function del()
    {
        $goods_id = input('id/d');
        model('Goods')->del($goods_id);
        $this->success('商品已经彻底删除');
    }
    // 文件上传
    public function upload(&$post_data, $is_must = true)
    {
        // 获取file对象
        $file = request()->file('goods_img');
        // 没有上传图片时的处理
        if (!$file) {
            if ($is_must) {
                $this->error('图片必须上传！');
            } else {
                return true;
            }
        }
        // 文件上传
        $info = $file->validate(['ext' => 'jpg,png'])->move('uploads');
        if (!$info) {
            $this->error($file->getError());
        }
        // 获取上传后的地址
        $goods_img = 'uploads/' . $info->getSaveName();
        // 更换地址中‘/’为‘\’
        $goods_img = str_replace('\\', '/', $goods_img);
        // 打开图片
        $img = \think\Image::open($goods_img);
        // 缩略图的保存地址
        $goods_thumb = 'uploads/' . date('Ymd') . '/thumb_' . $info->getFilename();
        // 生成缩略图
        $img->thumb(150, 150)->save($goods_thumb);
        $post_data['goods_img'] = $goods_img;
        $post_data['goods_thumb'] = $goods_thumb;
    }

    // 货号检查
    protected function checkGoodsSn(&$post_data)
    {
        if ($post_data['goods_sn']) {
            if (model('Goods')->get(['goods_sn' => $post_data['goods_sn']])) {
                $this->error('货号重复');
            } else {
                // 生成唯一
                $post_data['goods_sn'] = strtoupper('SHOP' . uniqid());
            }
        }
    }
}
