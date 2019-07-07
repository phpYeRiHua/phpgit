<?php
namespace app\admin\controller;
use think\Db;
use think\Model;
use think\Request;

class Role extends Common{
    // 更新权限相关的缓存
    public function flush(){
        // 获取所有的后台用户
        $user_list=Db::name('admin')->select();
        foreach($user_list as $key =>$value){
            cache('user_info_id_'.$value['id'],null);
        }
        return '更新完成';
    }

    // 类型的添加
    public function add(Request $request){
        if($request->isGet()){
            return $this->fetch();
        }
        model('Role')->save(input());
        $this->success('ok');
    }

    public function index(){
        $data=model('Role')->getList();
        $this->assign('data',$data);
        return $this->fetch();
    }


    public function edit(Request $request){
        $role_id=input('id/d');
        if($role_id<=1){
            $this->error('参数错误');
        }
        $model=model('Role');
        if($request->isGet()){
            $role_info=$model->get($role_id);
            $this->assign('role_info',$role_info);
            return $this->fetch();
        }
        $model->editType(input());
        $this->success('ok','index');
    }

    public function disfetch(Request $request){
        $rule_id=input('id/d');
        if($request->isGet()){
            $role_info=Db::name('role')->where('id',$rule_id)->find();
            $this->assign('hasRules',$role_info['rule_ids']);

            $rules=Db::name('rule')->select();
            $this->assign('rules',$rules);
            return $this->fetch();
        }
        $rule_id=input('id/d');
        $rules=input('rules/a',[]);
        $rule_ids=implode(',',$rules);
        Db::name('role')->where('id',$rule_id)->setField('rule_ids',$rule_ids);
        $this->success('ok','index');
    }
}