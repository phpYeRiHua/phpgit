<?php
namespace app\admin\Controller;
use think\Request;
use think\Db;

class Admin extends Common{
    public function add(Request $request){
        if($request->isGet()){
            $role=model('Role')->getList();
            $this->assign('role',$role);
            return $this->fetch();
        }
        $model=model('Admin');
        $res=$model->addUser(input());
        if($res===false){
            $this->error($model->getError());
        }
        $this->success('ok','index');
    }
    public function index(){
        $data=model('Admin')->getList();
        $this->assign('data',$data);
        return $this->fetch();
    }
    public function edit(Request $request){
        $admin_id=input('id/d');
        if($admin_id<=1){
            $this->error('参数错误');
        }
        $model=model('Admin');
        if($request->isGet()){
            $admin_info=$model->get($admin_id);
            $this->assign('admin_info',$admin_info);
            $role=model('Role')->getList();
            $this->assign('role',$role);
            return $this->fetch();
        }
        $res=$model->editUser(input());
        if($res===false){
            $this->error($model->getError());
        }
        $this->success('ok','index');
    }
}