<?php
namespace app\admin\controller;
use think\Db;
use think\Request;

class Rule extends Common{
    public function add(Request $request){
        $category_model=model('Rule');
        if($request->isGet()){
            $rules=$category_model->getRules();
            $this->assign('rules',$rules);
            return $this->fetch();
        }
        $category_model->save($request->post());
        $this->success('ok','add');
    }
    public function index(){
        $category=model('Rule')->getRules();
        $this->assign('category',$category);
        return $this->fetch();
    }
    public function remove(){
        model('Rule')->remove(input('id'));
        $this->success('ok','index');
    }
}