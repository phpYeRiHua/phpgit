<?php
namespace app\admin\Controller;
use think\Request;
use think\Db;
class Attribute extends Common{
    // 属性的添加
    public function add(Request $request){
        if($request->isGet()){
            $type=model('Type')->getList();
            $this->assign('type',$type);
            return $this->fetch();
        }
        $model=model('Attribute');
        $res=$model->addAttr(input());
        if($res===false){
            $this->error($model->getError());
        }
        $this->success('ok','index');
    }
    // 属性列表显示
    public function index(){
        $data=model('Attribute')->getList();
        $this->assign('data',$data);
        return $this->fetch();
    }

    // 属性删除
    public function remove(){
        model('Attribute')->remove(input('id'));
        $this->success('ok','index');
    }


    // 属性的编辑
    public function edit(Request $request){
        $model=model('Attribute');
        if($request->isGet()){
            // 获取当前属性
            $attr_info=$model->get(input('id'));
            $this->assign('attr_info',$attr_info);
            // 查询所有分类
            $type = model('Type')->getList();
            $this->assign('type',$type);
            return $this->fetch();
        }
        $res=$model->editAttr(input());
        if($res===false){
            $this->error($model->getError());
        }
        $this->success('ok','index');
    }
}