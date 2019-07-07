<?php
namespace app\admin\controller;

use think\Db;
use think\Request;

class Category extends Common
{
    public function add(Request $request)
    {
        
        $category_model=model('Category');
        // get请求渲染模板
        if ($request->isGet()) {
            // 查询所有分类数据
            // $category = Db::name('category')->select();
            // // 调用函数对数据进行格式化
            // $category=get_tree($category);


            // model的写法
            $category=$category_model->getCateTree();
            // 将数据分配给模板
            $this->assign('category', $category);
            return $this->fetch();
        }
        // 处理表单提交
        // Db写法
        // Db::name('category')->insert($$request->post());

        // model写法
        $category_model->save($request->post());
        $this->success('数据入库完成！', 'index');
    }
    public function index()
    {
        // Db写法
        // $category = Db::name('category')->select();

        // model写法
        $category=model('Category')->getCateTree();
        // 将数据分配给模板
        $this->assign('category', $category);
        return $this->fetch();
    }
    public function remove()
    {
        // 调用自定义的模型方法删除
        model('Category')->remove(input('id/d'));
        $this->success('删除成功!', 'index');
    }
    public function edit(Request $request)
    {


        // Db写法
        // if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        //     $query = Db::name('category');
        //     $category = $query->select();
        //     // 将数据分配给模板
        //     $this->assign('category', $category);
        //     $id = $_GET['id'];
        //     $cate_name = $query->find($id);
        //     $this->assign('cate_name', $cate_name);
        //     return $this->fetch();
        // }


        // model写法
        $category_model=model('Category');
        if($request->isGet()){
            // 获取当前分类的新信息
            $info=$category_model->get(input('id'));
            // 获取所有分类数据
            $category=$category_model->getCateTree();
            // 模板赋值 使用数组一次赋值完成
            $this->assign(['info'=>$info,'category'=>$category]);
            return $this->fetch();
        }
        // 调用自定义的模型方法修改数据
        $result=$category_model->saveData(input());
        if($result===false){
            $this->error($category_model->getError());
        }
        $this->success('ok','index');
    }
}
