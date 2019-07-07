<?php
namespace app\admin\controller;
use think\Request;
use think\Db;

class Type extends Common{
    // 类型的添加
    public function add(Request $request){
        if($request->isGet()){
            return $this->fetch();
        }
        $res=$this->validate(input(),'Type');
        if($res !== true){
            $this->error($res);
        }
        Db::name('Type')->insert(input());
        $this->success('ok','index');
    }

    // 类型列表显示
    public function index(){
        $data=model('Type')->getList();
        $this->assign('data',$data);
        return $this->fetch();
    }


    // 类型的编辑
    public function edit(Request $request){
        $model=model('Type');
        if($request->isGet()){
            $type_info=$model->get(input('id/d'));
            $this->assign('type_info',$type_info);
            return $this->fetch();
        }
        $res=$this->validate(input(),'Type');
        if($res !==true){
            $this->error($res);
        }
        // 调用自定义的模板方法编辑类型
        $model->editType(input());
        $this->success('ok','index');
    }

    // 类型的删除
    public function remove(){
        $model=model('Type');
        // dump(input('id/d'));exit;
        $model->destroy(input('id/d'));
        $this->success('ok');
    }
}